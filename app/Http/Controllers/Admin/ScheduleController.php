<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Schedule\LiveEventResource;
use App\Http\Resources\Admin\Schedule\MeetingResource;
use App\Models\LiveEvent;
use App\Models\Meeting;
use App\Services\Admin\ContentService;
use App\Services\Admin\ScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Inertia\Response;

class ScheduleController extends Controller
{
    /**
     * @var ScheduleService
     */
    protected ScheduleService $scheduleService;

    /**
     * @param ContentService $contentService
     */
    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * @return Response
     */
    public function index(Request $request): Response
    {
        Authorize::abortIfNot('schedule_index');

        $date = Carbon::create(
            $request->get('year', now()->format('Y')),
            $request->get('month', now()->format('m'))
        );

        $nextEventDate = $this->scheduleService->nextEventDateFormat('Y-m-d H:i');

        $nextEvent = $this->scheduleService->nextEvent();
        if($nextEvent instanceof Meeting) {
            $nextEvent = new MeetingResource($nextEvent);
        } else if($nextEvent instanceof LiveEvent) {
            $nextEvent = new LiveEventResource($nextEvent);
        }

        return inertia('Admin/Schedule/Index', [
            'liveEventCount' => LiveEvent::thatStartsAfterNow()->count(),
            'meetingCount' => Meeting::thatStartsAfterNow()->count(),
            'nextEventDate' => $nextEventDate,
            'nextEvent' => $nextEvent,

            'liveEvents' => LiveEventResource::collection(LiveEvent::whereYear('start_at', $date->year)->whereMonth('start_at', $date->month)->get()),
            'meetings' => MeetingResource::collection(Meeting::whereYear('start_at', $date->year)->whereMonth('start_at', $date->month)->get()),

            'date' => $date->format('Y-m-d'),
            'dateMonthName' => Str::ucfirst($date->monthName),
            'dateMonth' => $date->month,
            'dateYear' => $date->year
        ]);
    }
}
