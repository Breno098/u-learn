<?php

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class OrderService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Order[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        /** @var Order|Builder */
        $queryOrder = Order::query()->select(['orders.*']);

        if ($studentId = Arr::get($filters, 'student_id')) {
            $queryOrder->where('student_id', $studentId);
        }

        if ($campaignId = Arr::get($filters, 'campaign_id')) {
            $queryOrder->where('campaign_id', $campaignId);
        }

        if ($paymentMethod = Arr::get($filters, 'payment_method')) {
            $queryOrder->where('payment_method', $paymentMethod);
        }

        if ($status = Arr::get($filters, 'status')) {
            $queryOrder->where('status', $status);
        }

        if ($hiringStartAt = Arr::get($filters, 'hiring_start_at')) {
            $queryOrder->whereDate('hiring_start_at', Carbon::createFromFormat('d/m/Y', $hiringStartAt));
        }

        if ($orderBy === 'student_name') {
            $queryOrder->join('students', 'orders.student_id', '=', 'students.id')->orderBy('students.name', $sort);
        } else if ($orderBy === 'student_email') {
            $queryOrder->join('students', 'orders.student_id', '=', 'students.id')->orderBy('students.email', $sort);
        } else if (in_array($orderBy, (new Order)->getFillable())) {
            $queryOrder->orderBy("orders.{$orderBy}", $sort);
        }

        return $queryOrder->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Order
     */
    public function store(array $requestData = []): Order
    {
        return Order::create($this->transformData($requestData));
    }

    /**
     * @param Order $order
     * @param array $requestData
     * @return Order
     */
    public function update(Order $order, array $requestData = []): Order
    {
        $order->update($this->transformData($requestData));

        return $order;
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function transformData(array $requestData): array
    {
        $startAt = Arr::get($requestData, 'start_at') ? Carbon::createFromFormat('d/m/Y H:i', Arr::get($requestData, 'start_at')) : null;
        $endAt = Arr::get($requestData, 'end_at') ? Carbon::createFromFormat('d/m/Y H:i', Arr::get($requestData, 'end_at')) : null;

        return  array_merge($requestData, [
            'start_at' => $startAt,
            'end_at' => $endAt,
        ]);
    }

    /**
     * @param Order $order
     * @return boolean|null
     */
    public function delete(Order $order): ?bool
    {
        return $order->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($order = Order::find($id)) {
                $this->delete($order);
            }
        }
    }

     /**
     * @param Order $order
     * @return boolean|null
     */
    public function cancel(Order $order): ?bool
    {
        return $order->update([
            'canceled_at' => now(),
            'status' => 'cancelado'
        ]);
    }

    /**
     * @param array $ids
     * @return void
     */
    public function cancelMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($order = Order::find($id)) {
                $this->cancel($order);
            }
        }
    }

    /**
     * @param integer $month
     * @param integer|null|null $year
     * @return float
     */
    public function valueCanceledByMonth(int $month, int|null $year = null): float
    {
        $date = Carbon::create($year ?? now()->format('Y'), $month, 1);

        return Order::canceled()
            ->whereMonth('canceled_at', $date->month)
            ->whereYear('canceled_at', $date->year)
            ->sum('payment_value');
    }

    /**
     * @param integer $month
     * @param integer|null|null $year
     * @return float
     */
    public function valueExpiredByMonth(int $month, int|null $year = null): float
    {
        $date = Carbon::create($year ?? now()->format('Y'), $month, 1);

        return Order::expired()
            ->whereMonth('hiring_end_at', $date->month)
            ->whereYear('hiring_end_at', $date->year)
            ->sum('payment_value');
    }

    /**
     * @param integer $month
     * @param integer|null|null $year
     * @return float
     */
    public function valueToBeReceivedByMonth(int $month, int|null $year = null): float
    {
        $date = Carbon::create($year ?? now()->format('Y'), $month, 1);

        return Order::paid()
            ->whereMonth('hiring_end_at', $date->month)
            ->whereYear('hiring_end_at', $date->year)
            ->sum('payment_value');
    }

     /**
     * @param integer $month
     * @param integer|null|null $year
     * @return float
     */
    public function valueToBeReceivedByDay(int $day, int|null $month = null, int|null $year = null): float
    {
        $date = Carbon::create(
            $year ?? now()->format('Y'),
            $month ?? now()->format('m'),
            $day
        );

        return Order::paid()
            ->whereMonth('hiring_end_at', $date->month)
            ->whereYear('hiring_end_at', $date->year)
            ->whereDay('hiring_end_at', $date->day)
            ->sum('payment_value');
    }
}
