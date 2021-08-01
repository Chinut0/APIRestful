<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Seller;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $vendedores = Seller::has('products')->get()->random();
        $compradores = User::all()->except($vendedores->id)->random();
        return [
            // 'name' => $this->faker->word,
            'quantity' => $this->faker->numberBetween(1, 3),
            'buyer_id' =>  $compradores->id,
            'product_id' => $vendedores->products->random()->id, //usuario diferente al comprador

        ];
    }
}
