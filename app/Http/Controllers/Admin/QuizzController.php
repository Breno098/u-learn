<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AnswerTypeEnum;
use App\Enums\LinkableTypeEnum;
use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Models\Quizz;
use App\Http\Requests\Admin\Quizz\QuizzStoreRequest;
use App\Http\Requests\Admin\Quizz\QuizzUpdateRequest;
use App\Http\Resources\Admin\QuizzResource;
use App\Models\Content;
use App\Services\Admin\LinkableService;
use App\Services\Admin\QuestionService;
use App\Services\Admin\QuizzService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class QuizzController extends Controller
{
    /**
     * @var QuizzService
     */
    protected QuizzService $quizzService;

    /**
     * @param QuizzService $quizzService
     * @param QuestionService $questionService
     */
    public function __construct(QuizzService $quizzService)
    {
        $this->quizzService = $quizzService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        Authorize::abortIfNot('quizz_index');

        $dataQuery = new DataQuery($request);

        $quizzes = $this->quizzService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Quizz/Index', [
            'quizzes' => QuizzResource::collection($quizzes),
            'query' => $dataQuery->query(),
            'canQuizzStore' => Authorize::any('quizz_store'),
            'canQuizzEdit' => Authorize::any('quizz_update'),
            'canQuizzDestroy' => Authorize::any('quizz_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('quizz_store');

        $contents = Content::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Quizz/Create', [
            'answerTypes' => AnswerTypeEnum::toArray(),
            'linkableTypes' => LinkableTypeEnum::toArray(),
            'contents' => $contents,
            'seasonsOrChapters' => []
        ]);
    }

     /**
     * @param QuizzStoreRequest $quizzStoreRequest
     * @return RedirectResponse
     */
    public function store(QuizzStoreRequest $quizzStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('quizz_store');

        $quizz = $this->quizzService->store($quizzStoreRequest->validated());

        return redirect()->route('admin.quizz.edit', $quizz);
    }

    /**
     * @param Quizz  $quizz
     * @return Response
     */
    public function edit(Quizz $quizz): Response
    {
        Authorize::abortIfNot('quizz_update');

        $contents = Content::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Quizz/Edit', [
            'quizz' => new QuizzResource($quizz),
            'answerTypes' => AnswerTypeEnum::toArray(),
            'linkableTypes' => LinkableTypeEnum::toArray(),
            'contents' => $contents,
            'seasonsOrChapters' => LinkableService::getSeasonsOrChapters($quizz->content, $quizz->getLinkableTypeParse()),
            'canQuizzDestroy' => Authorize::any('quizz_destroy')
        ]);
    }

     /**
     * @param Quizz  $quizz
     * @return Response
     */
    public function show(Quizz $quizz): Response
    {
        Authorize::abortIfNot('quizz_show');

        $contents = Content::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Quizz/Show', [
            'quizz' => new QuizzResource($quizz),
            'answerTypes' => AnswerTypeEnum::toArray(),
            'linkableTypes' => LinkableTypeEnum::toArray(),
            'contents' => $contents,
            'seasonsOrChapters' => LinkableService::getSeasonsOrChapters($quizz->content, $quizz->getLinkableTypeParse()),
            'canQuizzDestroy' => Authorize::any('quizz_destroy')
        ]);
    }

    /**
     * @param QuizzUpdateRequest $quizzUpdateRequest
     * @param Quizz $quizz
     * @return RedirectResponse
     */
    public function update(QuizzUpdateRequest $quizzUpdateRequest, Quizz $quizz): RedirectResponse
    {
        Authorize::abortIfNot('quizz_update');

        $quizz = $this->quizzService->update($quizz, $quizzUpdateRequest->validated());

        return redirect()->route('admin.quizz.index');
    }

    /**
     * @param Quizz $quizz
     * @return RedirectResponse
     */
    public function destroy(Quizz $quizz): RedirectResponse
    {
        Authorize::abortIfNot('quizz_destroy');

        $this->quizzService->delete($quizz);

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('category_destroy');

        $request->validate(['ids' => 'required|array|in:' . Quizz::get()->pluck('id')->join(',')]);

        $this->quizzService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.quizz.index');
    }

    /**
     * @param Content $content
     * @return \Illuminate\Http\JsonResponse
     */
    public function linkables(Content $content, string $type): \Illuminate\Http\JsonResponse
    {
        Authorize::abortIfNot(['quizz_store', 'quizz_update']);

        return response()->json(['items' => LinkableService::getSeasonsOrChapters($content, $type)]);
    }
}
