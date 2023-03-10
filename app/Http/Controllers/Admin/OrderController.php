<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderStatusEnum;
use App\Helpers\Acess\Authorize;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Order\OrderResource;
use App\Models\Campaign;
use App\Models\Order;
use App\Services\Admin\OrderService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    protected OrderService $orderService;

    /**
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param Order $order
     * @return RedirectResponse
     */
    public function cancel(Order $order): RedirectResponse
    {
        Authorize::abortIfNot('order_cancel');

        $this->orderService->cancel($order);

        return redirect()->back();
    }

    /**
     * @param Order $order
     * @return Response
     */
    public function editSituation(Order $order): Response
    {
        Authorize::abortIfNot('order_situation_update');

        return inertia('Admin/Order/Situation/Edit', [
            'order' => new OrderResource($order),
            'paymentMethods' => OrderPaymentMethodEnum::toArray(),
            'statuses' => OrderStatusEnum::toArray(),
            'campaigns' => Campaign::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

     /**
     * @param Order $order
     * @return Response
     */
    public function showSituation(Order $order): Response
    {
        Authorize::abortIfNot('order_situation_show');

        return inertia('Admin/Order/Situation/Show', [
            'order' => new OrderResource($order),
            'paymentMethods' => OrderPaymentMethodEnum::toArray(),
            'statuses' => OrderStatusEnum::toArray(),
            'campaigns' => Campaign::select('id', 'name')->orderBy('name')->get(),
        ]);
    }
}
