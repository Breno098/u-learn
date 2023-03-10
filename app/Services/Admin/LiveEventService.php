<?php

namespace App\Services\Admin;

use App\Enums\LiveEventTypeEnum;
use App\Jobs\Admin\LiveEvent\LiveEventDeletedSendMails;
use App\Models\Chapter;
use App\Models\Content;
use App\Models\LiveEvent;
use App\Models\Material;
use App\Models\Season;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class LiveEventService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return LiveEvent[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        /** @var LiveEvent|Builder */
        $query  = LiveEvent::query()->select(['live_events.*']);

        if ($name = Arr::get($filters, 'name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($type = Arr::get($filters, 'type')) {
            $query->where('type', $type);
        }

        if ($startAt = Arr::get($filters, 'start_at')) {
            $query->whereDate('start_at', Carbon::createFromFormat('d/m/Y H:i', $startAt));
        }

        if ($endAt = Arr::get($filters, 'end_at')) {
            $query->whereDate('end_at', Carbon::createFromFormat('d/m/Y H:i', $endAt));
        }

        if ($responsibleId = Arr::get($filters, 'responsible_id')) {
            $query->where('responsible_id', $responsibleId);
        }

        if ($orderBy === 'responsible_name') {
            $query->join('users', 'live_events.responsible_id', '=', 'users.id')->orderBy('users.name', $sort);
        } else if (in_array($orderBy, (new LiveEvent)->getFillable())) {
            $query->orderBy("live_events.{$orderBy}", $sort);
        }

        return $rowsPerPage ? $query->paginate($rowsPerPage) : $query->get();
    }

    /**
     * @param array $requestData
     * @return LiveEvent
     */
    public function store(array $requestData = []): LiveEvent
    {
        $liveEvent = LiveEvent::create($this->transformData($requestData));

        $liveEvent->campaigns()->sync(Arr::get($requestData, 'campaign_ids'));

        if (Arr::get($requestData, 'type') === LiveEventTypeEnum::grupo()->value) {
            $liveEvent->groups()->sync(Arr::get($requestData, 'group_ids'));
        } else if (Arr::get($requestData, 'type') === LiveEventTypeEnum::individual()->value) {
            $liveEvent->students()->sync(Arr::get($requestData, 'student_ids'));
        }

        $this->uploadImage($liveEvent, Arr::get($requestData, 'image'));

        $this->updateOrCreateMaterials($liveEvent, Arr::get($requestData, 'materials', []));

        return $liveEvent;
    }

    /**
     * @param LiveEvent $liveEvent
     * @param array $requestData
     * @return LiveEvent
     */
    public function update(LiveEvent $liveEvent, array $requestData = []): LiveEvent
    {
        $liveEvent->update($this->transformData($requestData));

        $liveEvent->campaigns()->sync(Arr::get($requestData, 'campaign_ids'));

        if (Arr::get($requestData, 'type') === LiveEventTypeEnum::grupo()->value) {
            $liveEvent->students()->detach();
            $liveEvent->groups()->sync(Arr::get($requestData, 'group_ids'));
        } else if (Arr::get($requestData, 'type') === LiveEventTypeEnum::individual()->value) {
            $liveEvent->groups()->detach();
            $liveEvent->students()->sync(Arr::get($requestData, 'student_ids'));
        }

        $this->uploadImage($liveEvent, Arr::get($requestData, 'image'));

        $this->updateOrCreateMaterials($liveEvent, Arr::get($requestData, 'materials', []));

        return $liveEvent;
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function transformData(array $requestData): array
    {
        $startAt = Arr::get($requestData, 'start_at') ? Carbon::createFromFormat('d/m/Y H:i', Arr::get($requestData, 'start_at')) : null;
        $endAt = Arr::get($requestData, 'end_at') ? Carbon::createFromFormat('d/m/Y H:i', Arr::get($requestData, 'end_at')) : null;
        $hasLinkWithContent = Arr::get($requestData, 'has_link_with_content', false);

        $transform = [
            'name' => Arr::get($requestData, 'name'),
            'description' => Arr::get($requestData, 'description'),
            'event_type' => Arr::get($requestData, 'event_type', 'live'),
            'type' => Arr::get($requestData, 'type'),
            'embed' => Arr::get($requestData, 'embed'),
            'number_of_students' => Arr::get($requestData, 'number_of_students'),
            'responsible_id' => Arr::get($requestData, 'responsible_id'),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'has_link_with_content' => $hasLinkWithContent
        ];

        if ($contentId = Arr::get($requestData, 'course_id') && $hasLinkWithContent) {
           $transform['course_id'] = $contentId;

           $transform['linkable_type'] = match (Arr::get($requestData, 'linkable_type')) {
                'season' => Season::class,
                'chapter' => Chapter::class,
                default => Content::class
            };

            $transform['linkable_id'] = match (Arr::get($requestData, 'linkable_type')) {
                'season' => Arr::get($requestData, 'linkable_id'),
                'chapter' => Arr::get($requestData, 'linkable_id'),
                default => $contentId
            };
        } else {
            $transform['course_id'] = null;
            $transform['linkable_type'] = null;
            $transform['linkable_id'] = null;
        }

        return $transform;
    }

    /**
     * @param LiveEvent $liveEvent
     * @return boolean|null
     */
    public function delete(LiveEvent $liveEvent): ?bool
    {
        $liveEventName = $liveEvent->name;
        $emails = $liveEvent->participants_emails;

        $liveEvent->materials()->each(function(Material $material) use ($liveEvent){
            $this->deleteMaterial($liveEvent, $material);
        });

        $liveEvent->campaigns()->detach();
        $liveEvent->students()->detach();
        $liveEvent->groups()->detach();

        $deleted = $liveEvent->delete();

        // if ($deleted) {
        //     LiveEventDeletedSendMails::dispatch($liveEventName, $emails);
        // }

        return $deleted;
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($liveEvent = LiveEvent::find($id)) {
                $this->delete($liveEvent);
            }
        }
    }

     /**
     * @param LiveEvent $liveEvent
     * @param array $materials
     * @return void
     */
    public function updateOrCreateMaterials(LiveEvent $liveEvent, array $materials = []): void
    {
        /**
         * Filtra apenas materiais do tipo UploadedFile, ou seja, materiais que vieram como arquivos temporários
         * do navegador. Após realizar o upload desses materiais, criando registro no banco para cada um deles.
         */
        collect($materials)->filter(function ($material) {
            return Arr::get($material, 'uploadedFile') instanceof UploadedFile;
        })->each(function($material) use ($liveEvent) {
            /** @var UploadedFile */
            $uploadedFile = Arr::get($material, 'uploadedFile');

            $liveEvent->materials()->create([
                'name' => $uploadedFile->getClientOriginalName(),
                'path' => Storage::url(Storage::disk('public')->put('liveEvents', $uploadedFile)),
                'size' => $uploadedFile->getSize()
            ]);
        });

        /**
         * Verifica os arquivos que não vieram na requisição e deleta do registro
         * no banco de dados e os arquivo
         *
         * Por exemplo: No Evento há 3 materiais com ID's 1, 2 e 3.
         * Caso venha na requisição apenas os materiais de ID 1 e 3, o material de ID numero 2
         * será deletedo do storage e o registro de banco deletedo.
         */
        $liveEvent->materials()
            ->whereNotIn('id', data_get($materials, '*.id'))
            ->each(fn(Material $material) => $this->deleteMaterial($liveEvent, $material));
    }

     /**
     * @param LiveEvent $liveEvent
     * @param Material $material
     * @return bool|null
     */
    public function deleteMaterial(LiveEvent $liveEvent, Material $material): bool|null
    {
        Storage::disk('public')->delete(Str::replaceFirst('storage', '', $material->path));

        return $liveEvent->materials()->find($material->id)->delete();
    }

    /**
     * @param LiveEvent $liveEvent
     * @return void
     */
    public function deleteImage(LiveEvent $liveEvent): void
    {
        if ($liveEvent->image) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $liveEvent->image));

            $liveEvent->update(['image' => null]);
        }
    }

    /**
     * @param LiveEvent $liveEvent
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(LiveEvent $liveEvent, UploadedFile $image): void
    {
        $this->deleteImage($liveEvent);

        $liveEvent->update([
            'image' => Storage::url(Storage::disk('public')->put('liveEvents', $image))
        ]);
    }

    /**
     * @param LiveEvent $liveEvent
     * @param null|string|UploadedFile $image
     * @return void
     */
    public function uploadImage(LiveEvent $liveEvent, null|string|UploadedFile $image): void
    {
        if (! $image) {
            $this->deleteImage($liveEvent);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($liveEvent, $image);
        }
    }
}
