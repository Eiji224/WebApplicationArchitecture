<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $users = User::factory(5)->create();
         $posts = Post::factory(10)->recycle($users)->create();
         Comment::factory(20)->recycle($users)->recycle($posts)->create();
    }
}
