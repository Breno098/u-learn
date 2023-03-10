<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Content\ContentSummaryResource;
use App\Http\Resources\Admin\Schedule\LiveEventResource;
use App\Http\Resources\Admin\Schedule\MeetingResource;
use App\Models\LiveEvent;
use App\Models\Meeting;
use App\Models\Student;
use App\Services\Admin\ChurnRateService;
use App\Services\Admin\ContentService;
use App\Services\Admin\OrderService;
use App\Services\Admin\ScheduleService;
use App\Services\Admin\StudentService;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * @var ContentService
     */
    protected ContentService $contentService;

    /**
     * @var ChurnRateService
     */
    protected ChurnRateService $churnRateService;

    /**
     * @var ScheduleService
     */
    protected ScheduleService $scheduleService;

     /**
     * @var OrderService
     */
    protected OrderService $orderService;


     /**
     * @var StudentService
     */
    protected StudentService $studentService;

    /**
     * @param ContentService $contentService
     * @param ChurnRateService $churnRateService
     * @param ScheduleService $scheduleService
     * @param OrderService $orderService
     * @param StudentService $studentService
     */
    public function __construct(
        ContentService $contentService,
        ChurnRateService $churnRateService,
        ScheduleService $scheduleService,
        OrderService $orderService,
        StudentService $studentService
    )
    {
        $this->contentService = $contentService;
        $this->churnRateService = $churnRateService;
        $this->scheduleService = $scheduleService;
        $this->orderService = $orderService;
        $this->studentService = $studentService;
    }

    /**
     * @return Response
     */
    public function index(Request $request): Response
    {
        $date = Carbon::create(now()->format('Y'), $request->get('month', now()->format('m')));

        $selectedMonth = $date->month;
        $beforeSelectedMonth = $date->subMonth()->month;

        $props = [];

        /** Content summary */
        $props['contentReleasedThisMonth'] = ContentSummaryResource::collection($this->contentService->launchesOfThisMonth());
        $props['contentsMostViewed'] = ContentSummaryResource::collection($this->contentService->mostViewed());
        $props['contentsExpiresIn60Days'] = ContentSummaryResource::collection($this->contentService->expiresIn60Days());

        /** Schedule Data */
        $nextEvent = $this->scheduleService->nextEvent();

        if($nextEvent instanceof Meeting) {
            $props['nextEvent'] = new MeetingResource($nextEvent);
        } else if($nextEvent instanceof LiveEvent) {
            $props['nextEvent'] = new LiveEventResource($nextEvent);
        }

        $props['nextEventDate'] = $this->scheduleService->nextEventDateFormat('Y-m-d H:i');
        $props['liveEventCount'] = LiveEvent::thatStartsAfterNow()->count();
        $props['meetingCount'] = Meeting::thatStartsAfterNow()->count();

        /** Summary commercial */
        $props['totalActiveStudentsCount'] = Student::isActive()->count();
        $props['newStudentsInSelectedMonthCount'] = $this->studentService->createdByMonthCount($selectedMonth);
        $props['newStudentsInTheMonthBeforeTheSelectedMonthCount'] = $this->studentService->createdByMonthCount($beforeSelectedMonth);
        $props['churnRateInSelectedMonth'] = $this->churnRateService->month($selectedMonth);
        $props['churnRateInTheMonthBeforeTheSelectedMonth'] = $this->churnRateService->month($beforeSelectedMonth);

        /** Summary financial */
        $props['valueOfOrdersCanceledInSelectedMonth'] = $this->orderService->valueCanceledByMonth($selectedMonth);
        $props['valueOfOrdersCanceledInTheMonthBeforeSelectedMonth'] = $this->orderService->valueCanceledByMonth($beforeSelectedMonth);
        $props['valueOfOrdersExpiredInSelectedMonth'] = $this->orderService->valueExpiredByMonth($selectedMonth);
        $props['valueOfOrdersExpiredInTheMonthBeforeSelectedMonth'] = $this->orderService->valueExpiredByMonth($beforeSelectedMonth);
        $props['valueOfOrdersToBeReceivedInSelectedMonth'] = $this->orderService->valueToBeReceivedByMonth($selectedMonth);
        $props['valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth'] = $this->orderService->valueToBeReceivedByMonth($beforeSelectedMonth);
        $props['valueOfOrdersToBeReceivedToday'] = $this->orderService->valueToBeReceivedByDay(now()->format('d'));
        $props['valueOfOrdersToBeReceivedYesterday'] = $this->orderService->valueToBeReceivedByDay(now()->subDay()->format('d'));

        return inertia('Admin/Home', array_merge([
            'selectedMonth' => $selectedMonth,
        ], $props));
    }
}
