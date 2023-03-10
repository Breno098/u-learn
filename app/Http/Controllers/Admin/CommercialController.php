<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderStatusEnum;
use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\OrderIndexRequest;
use App\Http\Resources\Admin\Order\OrderResource;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\Student;
use App\Models\User;
use App\Services\Admin\ChurnRateService;
use App\Services\Admin\OrderService;
use App\Services\Admin\StudentService;
use Inertia\Response;
use Illuminate\Support\Carbon;

class CommercialController extends Controller
{
     /**
     * @var OrderService
     */
    protected OrderService $orderService;

    /**
     * @var ChurnRateService
     */
    protected ChurnRateService $churnRateService;

     /**
     * @var StudentService
     */
    protected StudentService $studentService;

    /**
     * @param OrderService $orderService
     */
    public function __construct(
        OrderService $orderService,
        ChurnRateService $churnRateService,
        StudentService $studentService
    )
    {
        $this->orderService = $orderService;
        $this->churnRateService = $churnRateService;
        $this->studentService = $studentService;
    }

    /**
     * @return Response
     */
    public function index(OrderIndexRequest $request): Response
    {
        Authorize::abortIfNot('commercial_index');

        $dataQuery = new DataQuery($request);

        $orders = $this->orderService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy(),
            $dataQuery->sort()
        );

        $date = Carbon::create(now()->format('Y'), $request->get('month', now()->format('m')));

        $selectedMonth = $date->month;
        $beforeSelectedMonth = $date->subMonth()->month;

        $totalActiveStudentsCount = Student::isActive()->count();
        $newStudentsInSelectedMonthCount = $this->studentService->createdByMonthCount($selectedMonth);
        $newStudentsInTheMonthBeforeTheSelectedMonthCount = $this->studentService->createdByMonthCount($beforeSelectedMonth);
        $churnRateInSelectedMonth = $this->churnRateService->month($selectedMonth);
        $churnRateInTheMonthBeforeTheSelectedMonth = $this->churnRateService->month($beforeSelectedMonth);

        return inertia('Admin/Commercial/Index', [
            'orders' => OrderResource::collection($orders),
            'query' => $dataQuery->query(),

            'paymentMethods' => OrderPaymentMethodEnum::toArray(),
            'statuses' => OrderStatusEnum::toArray(),
            'campaigns' => Campaign::select('id', 'name')->orderBy('name')->get(),
            'students' => Student::select('id', 'name')->orderBy('name')->get(),

            'ordersPaidCount' => Order::paid()->count(),
            'ordersExpiredCount' => Order::expired()->count(),
            'ordersCanceledCount' => Order::canceled()->count(),
            'ordersNotRenewedCount' => Order::notRenewed()->count(),

            'selectedMonth' => $selectedMonth,
            'totalActiveStudentsCount' => $totalActiveStudentsCount,
            'newStudentsInSelectedMonthCount' => $newStudentsInSelectedMonthCount,
            'newStudentsInTheMonthBeforeTheSelectedMonthCount' => $newStudentsInTheMonthBeforeTheSelectedMonthCount,
            'churnRateInSelectedMonth' => $churnRateInSelectedMonth,
            'churnRateInTheMonthBeforeTheSelectedMonth' => $churnRateInTheMonthBeforeTheSelectedMonth,

            'canOrderCancel' => Authorize::any('order_cancel'),
            'canOrderSituationUpdate' => Authorize::any('order_situation_update'),
        ]);
    }
}
