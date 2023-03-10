<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LinkableTypeEnum;
use App\Enums\MettingTypeEnum;
use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Meeting\MeetingIndexRequest;
use App\Models\Meeting;
use App\Http\Requests\Admin\Meeting\MeetingStoreRequest;
use App\Http\Requests\Admin\Meeting\MeetingUpdateRequest;
use App\Http\Resources\Admin\MeetingResource;
use App\Models\Content;
use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use App\Services\Admin\LinkableService;
use App\Services\Admin\MeetingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class MeetingController extends Controller
{
     /**
     * @var MeetingService
     */
    protected MeetingService $meetingService;

    /**
     * @param MeetingService $meetingService
     */
    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(MeetingIndexRequest $request): Response
    {
        Authorize::abortIfNot('meeting_index');

        $dataQuery = new DataQuery($request);

        $meetings = $this->meetingService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('start_at'),
            $dataQuery->sort('desc')
        );

        return inertia('Admin/Meeting/Index', [
            'meetings' => MeetingResource::collection($meetings),
            'teachers' => User::select('id', 'name')->orderBy('name')->get(),
            'types' => MettingTypeEnum::toArray(),
            'query' => $dataQuery->query(),

            'canMeetingStore' => Authorize::any('meeting_store'),
            'canMeetingEdit' => Authorize::any('meeting_update'),
            'canMeetingShow' => Authorize::any('meeting_show'),
            'canMeetingDestroy' => Authorize::any('meeting_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('meeting_store');

        $groups = Group::select('id', 'name')->orderBy('name')->get();
        $teachers = User::select('id', 'name')->orderBy('name')->get();
        $students = Student::select('id', 'name')->orderBy('name')->get();
        $contents = Content::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Meeting/Create', [
            'types' => MettingTypeEnum::toArray(),
            'linkableTypes' => LinkableTypeEnum::toArray(),
            'teachers' => $teachers,
            'students' => $students,
            'groups' => $groups,
            'contents' => $contents,
            'seasonsOrChapters' => [],
        ]);
    }

    /**
     * @param MeetingStoreRequest $meetingStoreRequest
     * @return RedirectResponse
     */
    public function store(MeetingStoreRequest $meetingStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('meeting_store');

        $meeting = $this->meetingService->store($meetingStoreRequest->validated());

        return redirect()->route('admin.meeting.edit', $meeting);
    }

    /**
     * @param Meeting $meeting
     * @return Response
     */
    public function edit(Meeting $meeting): Response
    {
        Authorize::abortIfNot('meeting_update');

        $groups = Group::select('id', 'name')->orderBy('name')->get();
        $teachers = User::select('id', 'name')->orderBy('name')->get();
        $students = Student::select('id', 'name')->orderBy('name')->get();
        $contents = Content::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Meeting/Edit', [
            'meeting' => new MeetingResource($meeting),
            'types' => MettingTypeEnum::toArray(),
            'linkableTypes' => LinkableTypeEnum::toArray(),
            'teachers' => $teachers,
            'students' => $students,
            'groups' => $groups,
            'contents' => $contents,
            'seasonsOrChapters' => LinkableService::getSeasonsOrChapters($meeting->content, $meeting->getLinkableTypeParse()),

            'canMeetingDestroy' => Authorize::any('meeting_destroy'),
        ]);
    }

    /**
     * @param Meeting $meeting
     * @return Response
     */
    public function show(Meeting $meeting): Response
    {
        Authorize::abortIfNot('meeting_show');

        $groups = Group::select('id', 'name')->orderBy('name')->get();
        $teachers = User::select('id', 'name')->orderBy('name')->get();
        $students = Student::select('id', 'name')->orderBy('name')->get();
        $contents = Content::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Meeting/Show', [
            'meeting' => new MeetingResource($meeting),
            'types' => MettingTypeEnum::toArray(),
            'linkableTypes' => LinkableTypeEnum::toArray(),
            'teachers' => $teachers,
            'students' => $students,
            'groups' => $groups,
            'contents' => $contents,
            'seasonsOrChapters' => LinkableService::getSeasonsOrChapters($meeting->content, $meeting->getLinkableTypeParse()),
        ]);
    }

    /**
     * @param MeetingUpdateRequest $meetingUpdateRequest
     * @param Meeting $meeting
     * @return RedirectResponse
     */
    public function update(MeetingUpdateRequest $meetingUpdateRequest, Meeting $meeting): RedirectResponse
    {
        Authorize::abortIfNot('meeting_update');

        $meeting = $this->meetingService->update($meeting, $meetingUpdateRequest->validated());

        return redirect()->route('admin.meeting.index');
    }

    /**
     * @param Meeting $meeting
     * @return RedirectResponse
     */
    public function destroy(Meeting $meeting): RedirectResponse
    {
        Authorize::abortIfNot('meeting_destroy');

        $this->meetingService->delete($meeting);

        return redirect()->route('admin.meeting.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('meeting_destroy');

        $request->validate(['ids' => 'required|array|in:' . Meeting::get()->pluck('id')->join(',')]);

        $this->meetingService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.meeting.index');
    }
}
