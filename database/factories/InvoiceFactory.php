<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'invoice_number' => $this->faker->unique()->numerify('INV-#####'),
            'due_date' => $this->faker->date(),
            'total' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
