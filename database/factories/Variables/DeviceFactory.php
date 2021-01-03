<?php

namespace Database\Factories\Variables;

use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Device::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => 1,
            'device_type_id' => DeviceType::all()->random()->id,
            'serial' => $this->faker->randomNumber(8),
            'physical_status' => $this->faker->randomElement([1, 2]),
            'transport_status' => $this->faker->randomElement([1, 2, 3]),
            'psp_status' => $this->faker->randomElement([1, 2]),
        ];
    }
}
