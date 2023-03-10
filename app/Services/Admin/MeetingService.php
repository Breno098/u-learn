<?php

namespace App\Services\Admin;

use App\Models\Chapter;
use App\Models\Content;
use App\Models\Material;
use App\Models\Meeting;
use App\Models\Season;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MeetingService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Meeting[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        /** @var Meeting|Builder */
        $queryMeeting = Meeting::query();

        $queryMeeting->select(['meetings.*']);

        if ($name = Arr::get($filters, 'name')) {
            $queryMeeting->where('name', 'like', "%{$name}%");
        }

        if ($type = Arr::get($filters, 'type')) {
            $queryMeeting->where('type', $type);
        }

        if ($startAt = Arr::get($filters, 'start_at')) {
            $queryMeeting->whereDate('start_at', Carbon::createFromFormat('d/m/Y H:i', $startAt));
        }

        if ($endAt = Arr::get($filters, 'end_at')) {
            $queryMeeting->whereDate('end_at', Carbon::createFromFormat('d/m/Y H:i', $endAt));
        }

        if ($teacherId = Arr::get($filters, 'teacher_id')) {
            $queryMeeting->where('teacher_id', $teacherId);
        }

        if ($orderBy === 'teacher_name') {
            $queryMeeting->join('users', 'meetings.teacher_id', '=', 'users.id')->orderBy('users.name', $sort);
        } else if (in_array($orderBy, (new Meeting)->getFillable())) {
            $queryMeeting->orderBy("meetings.{$orderBy}", $sort);
        }

        return $queryMeeting->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Meeting
     */
    public function store(array $requestData = []): Meeting
    {
        $meeting = Meeting::create($this->transformData($requestData));

        $this->uploadImage($meeting, Arr::get($requestData, 'image'));

        $this->updateOrCreateMaterials($meeting, Arr::get($requestData, 'materials', []));

        return $meeting;
    }

    /**
     * @param Meeting $meeting
     * @param array $requestData
     * @return Meeting
     */
    public function update(Meeting $meeting, array $requestData = []): Meeting
    {
        $meeting->update($this->transformData($requestData));

        $this->uploadImage($meeting, Arr::get($requestData, 'image'));

        $this->updateOrCreateMaterials($meeting, Arr::get($requestData, 'materials', []));

        return $meeting;
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
            'type' => Arr::get($requestData, 'type'),
            'number_of_students' => Arr::get($requestData, 'number_of_students'),
            'teacher_id' => Arr::get($requestData, 'teacher_id'),
            'live_offer' => Arr::get($requestData, 'live_offer'),
            'name_offer' => Arr::get($requestData, 'name_offer'),
            'description_offer' => Arr::get($requestData, 'description_offer'),
            'embed_offer' => Arr::get($requestData, 'embed_offer'),
            'tags' => Arr::get($requestData, 'tags'),
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
     * @param Meeting $meeting
     * @return boolean|null
     */
    public function delete(Meeting $meeting): ?bool
    {
        $meeting->materials()->each(function(Material $material) use ($meeting){
            $this->deleteMaterial($meeting, $material);
        });

        $meeting->groups()->detach();

        return $meeting->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($meeting = Meeting::find($id)) {
                $this->delete($meeting);
            }
        }
    }

     /**
     * @param Meeting $meeting
     * @param array $materials
     * @return void
     */
    public function updateOrCreateMaterials(Meeting $meeting, array $materials = []): void
    {
        /**
         * Filtra apenas materiais do tipo UploadedFile, ou seja, materiais que vieram como arquivos temporários
         * do navegador. Após realizar o upload desses materiais, criando registro no banco para cada um deles.
         */
        collect($materials)->filter(function ($material) {
            return Arr::get($material, 'uploadedFile') instanceof UploadedFile;
        })->each(function($material) use ($meeting) {
            /** @var UploadedFile */
            $uploadedFile = Arr::get($material, 'uploadedFile');

            $meeting->materials()->create([
                'name' => $uploadedFile->getClientOriginalName(),
                'path' => Storage::url(Storage::disk('public')->put('mettings', $uploadedFile)),
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
        $meeting->materials()
            ->whereNotIn('id', data_get($materials, '*.id'))
            ->each(fn(Material $material) => $this->deleteMaterial($meeting, $material));
    }

    /**
     * @param Meeting $meeting
     * @param Material $material
     * @return bool|null
     */
    public function deleteMaterial(Meeting $meeting, Material $material): ?bool
    {
        Storage::disk('public')->delete(Str::replaceFirst('storage', '', $material->path));

        return $meeting->materials()->find($material->id)->delete();
    }

     /**
     * @param Meeting $meeting
     * @return void
     */
    public function deleteImage(Meeting $meeting): void
    {
        if ($meeting->image) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $meeting->image));

            $meeting->update(['image' => null]);
        }
    }

    /**
     * @param Meeting $meeting
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(Meeting $meeting, UploadedFile $image): void
    {
        $this->deleteImage($meeting);

        $meeting->update([
            'image' => Storage::url(Storage::disk('public')->put('meetings', $image))
        ]);
    }

    /**
     * @param Meeting $meeting
     * @param null|string|UploadedFile $image
     * @return void
     */
    public function uploadImage(Meeting $meeting, null|string|UploadedFile $image): void
    {
        if (! $image) {
            $this->deleteImage($meeting);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($meeting, $image);
        }
    }
}
