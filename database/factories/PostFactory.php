<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'title' => fake()->sentence(6),
            'content' => fake()->paragraph(),
        ];
    }
}
