<?php

namespace Database\Factories;

use App\Enums\OrderPaymentMethodEnum;
use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = fake()->randomElement(OrderStatusEnum::toValues());

        $hiringStartAt = fake()->dateTimeBetween('-12 months', '-10 months');

        if ($status === OrderStatusEnum::vencido()->value) {
            $hiringEndAt = fake()->dateTimeBetween('-1 month', '-1 day');
        } else {
            $hiringEndAt = fake()->dateTimeBetween('+1 months', '+10 months');
        }

        $canceledAt = null;
        if($status === OrderStatusEnum::cancelado()->value) {
            $canceledAt = fake()->dateTimeBetween('-2 months', 'now');
        }

        return [
            'status' => $status,
            'canceled_at' => $canceledAt,
            'payment_value' => fake()->numberBetween(100, 10000),
            'payment_method' => fake()->randomElement(OrderPaymentMethodEnum::toValues()),
            'term_accepted' => true,
            'text_terms_acceptance' => fake()->realText(50),
            'registration_type' => 'anual',
            'hiring_start_at' => $hiringStartAt,
            'hiring_end_at' => $hiringEndAt,
            'invoices' => fake()->url(),
        ];
    }
}
