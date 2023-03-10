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
use Illuminate\Support\Carbon;
use App\Services\Admin\ChurnRateService;
use App\Services\Admin\OrderService;
use Inertia\Response;

class FinancialController extends Controller
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
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService, ChurnRateService $churnRateService)
    {
        $this->orderService = $orderService;
        $this->churnRateService = $churnRateService;
    }

    /**
     * @return Response
     */
    public function index(OrderIndexRequest $request): Response
    {
        Authorize::abortIfNot('financial_index');

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

       $valueOfOrdersCanceledInSelectedMonth = $this->orderService->valueCanceledByMonth($selectedMonth);
       $valueOfOrdersCanceledInTheMonthBeforeSelectedMonth = $this->orderService->valueCanceledByMonth($beforeSelectedMonth);
       $valueOfOrdersExpiredInSelectedMonth = $this->orderService->valueExpiredByMonth($selectedMonth);
       $valueOfOrdersExpiredInTheMonthBeforeSelectedMonth = $this->orderService->valueExpiredByMonth($beforeSelectedMonth);
       $valueOfOrdersToBeReceivedInSelectedMonth = $this->orderService->valueToBeReceivedByMonth($selectedMonth);
       $valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth = $this->orderService->valueToBeReceivedByMonth($beforeSelectedMonth);
       $valueOfOrdersToBeReceivedToday = $this->orderService->valueToBeReceivedByDay(now()->format('d'));
       $valueOfOrdersToBeReceivedYesterday = $this->orderService->valueToBeReceivedByDay(now()->subDay()->format('d'));

        return inertia('Admin/Financial/Index', [
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
            'valueOfOrdersCanceledInSelectedMonth' => $valueOfOrdersCanceledInSelectedMonth,
            'valueOfOrdersCanceledInTheMonthBeforeSelectedMonth' => $valueOfOrdersCanceledInTheMonthBeforeSelectedMonth,
            'valueOfOrdersExpiredInSelectedMonth' => $valueOfOrdersExpiredInSelectedMonth,
            'valueOfOrdersExpiredInTheMonthBeforeSelectedMonth' => $valueOfOrdersExpiredInTheMonthBeforeSelectedMonth,
            'valueOfOrdersToBeReceivedInSelectedMonth' => $valueOfOrdersToBeReceivedInSelectedMonth,
            'valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth' => $valueOfOrdersToBeReceivedInTheMonthBeforeSelectedMonth,
            'valueOfOrdersToBeReceivedToday' => $valueOfOrdersToBeReceivedToday,
            'valueOfOrdersToBeReceivedYesterday' => $valueOfOrdersToBeReceivedYesterday,

            'canOrderCancel' => Authorize::any('order_cancel'),
            'canOrderSituationUpdate' => Authorize::any('order_situation_update'),
        ]);
    }
}
