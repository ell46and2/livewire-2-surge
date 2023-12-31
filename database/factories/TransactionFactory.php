<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'title' => 'Payment to '.$this->faker->name,
            'amount' => rand(10, 500),
            'status' => Arr::random(['success', 'processing', 'failed']),
            'date' => Carbon::now()->subDays(rand(1, 365))->startOfDay(),
        ];
    }
}
