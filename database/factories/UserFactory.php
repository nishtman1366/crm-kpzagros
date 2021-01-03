<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public $usernameArray;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'mobile' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function level($level)
    {
        if ($level == 'ADMIN') {
            $this->usernameArray = [];
            for ($i = 1; $i <= 20; $i++) {
                $this->usernameArray[] = 'admin' . $i;
            }
        } elseif ($level == 'AGENT') {
            $this->usernameArray = [];
            for ($i = 1; $i <= 100; $i++) {
                $this->usernameArray[] = 'agent' . $i;
            }
        } elseif ($level == 'MARKETER') {
            $this->usernameArray = [];
            for ($i = 1; $i <= 400; $i++) {
                $this->usernameArray[] = 'marketer' . $i;
            }
        }
        $usernameList = $this->usernameArray;
        return $this->state(function (array $attributes) use ($level, $usernameList) {
            $username = $this->faker->unique()->randomElement($usernameList);
            return [
                'username' => $username,
                'password' => $username,
                'level' => $level,
            ];
        });
    }

    public function parent($parentId)
    {
        return $this->state(function (array $attributes) use ($parentId) {
            return [
                'parent_id' => $parentId,
            ];
        });
    }
}
