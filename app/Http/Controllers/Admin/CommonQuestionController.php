<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CommonQuestion\CommonQuestionIndexRequest;
use App\Models\CommonQuestion;
use App\Http\Requests\Admin\CommonQuestion\CommonQuestionStoreRequest;
use App\Http\Requests\Admin\CommonQuestion\CommonQuestionUpdateRequest;
use App\Http\Resources\Admin\CommonQuestionsResource;
use App\Services\Admin\CommonQuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CommonQuestionController extends Controller
{
    /**
     * @var CommonQuestionService
     */
    protected CommonQuestionService $commonQuestionService;

    /**
     * @param CommonQuestionService $commonQuestionService
     */
    public function __construct(CommonQuestionService $commonQuestionService)
    {
        $this->commonQuestionService = $commonQuestionService;
    }

    /**
     * @param CommonQuestionIndexRequest $request
     * @return Response
     */
    public function index(CommonQuestionIndexRequest $request): Response
    {
        Authorize::abortIfNot('common_question_index');

        $dataQuery = new DataQuery($request);

        $commonQuestions = $this->commonQuestionService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('question_text'),
            $dataQuery->sort()
        );

        return inertia('Admin/CommonQuestion/Index', [
            'commonQuestions' => CommonQuestionsResource::collection($commonQuestions),
            'query' => $dataQuery->query(),

            'canCommonQuestionStore' => Authorize::any('common_question_store'),
            'canCommonQuestionEdit' => Authorize::any('common_question_update'),
            'canCommonQuestionShow' => Authorize::any('common_question_show'),
            'canCommonQuestionDestroy' => Authorize::any('common_question_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('common_question_store');

        return inertia('Admin/CommonQuestion/Create');
    }

    /**
     * @param CommonQuestionStoreRequest $commonQuestionStoreRequest
     * @return RedirectResponse
     */
    public function store(CommonQuestionStoreRequest $commonQuestionStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('common_question_store');

        $commonQuestion = $this->commonQuestionService->store($commonQuestionStoreRequest->validated());

        return redirect()->route('admin.common-question.edit', $commonQuestion);
    }

    /**
     * @param CommonQuestion $commonQuestion
     * @return Response
     */
    public function edit(CommonQuestion $commonQuestion): Response
    {
        Authorize::abortIfNot('common_question_update');

        return inertia('Admin/CommonQuestion/Edit', [
            'commonQuestion' => new CommonQuestionsResource($commonQuestion),
            'canCommonQuestionDestroy' => Authorize::any('common_question_destroy'),
        ]);
    }

    /**
     * @param CommonQuestion $commonQuestion
     * @return Response
     */
    public function show(CommonQuestion $commonQuestion): Response
    {
        Authorize::abortIfNot('common_question_store');

        return inertia('Admin/CommonQuestion/Show', [
            'commonQuestion' => new CommonQuestionsResource($commonQuestion)
        ]);
    }

    /**
     * @param CommonQuestionUpdateRequest $commonQuestionUpdateRequest
     * @param CommonQuestion $commonQuestion
     * @return RedirectResponse
     */
    public function update(CommonQuestionUpdateRequest $commonQuestionUpdateRequest, CommonQuestion $commonQuestion): RedirectResponse
    {
        Authorize::abortIfNot('common_question_update');

        $commonQuestion = $this->commonQuestionService->update($commonQuestion, $commonQuestionUpdateRequest->validated());

        return redirect()->route('admin.common-question.index');
    }

    /**
     * @param CommonQuestion $commonQuestion
     * @return RedirectResponse
     */
    public function destroy(CommonQuestion $commonQuestion): RedirectResponse
    {
        Authorize::abortIfNot('common_question_destroy');

        $this->commonQuestionService->delete($commonQuestion);

        return redirect()->route('admin.common-question.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('common_question_destroy');

        $request->validate(['ids' => 'required|array|in:' . CommonQuestion::get()->pluck('id')->join(',')]);

        $this->commonQuestionService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.common-question.index');
    }
}
