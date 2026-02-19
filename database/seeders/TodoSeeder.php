<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user or create one
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Sample todos
        $todos = [
            [
                'title' => 'Setup development environment',
                'description' => 'Install PHP, Node.js, and database tools',
                'priority' => 'high',
                'is_completed' => true,
            ],
            [
                'title' => 'Learn Vue.js Composition API',
                'description' => 'Study Vue 3 composition API and setup patterns',
                'priority' => 'high',
                'is_completed' => false,
            ],
            [
                'title' => 'Implement todo list backend',
                'description' => 'Create API endpoints with clean architecture',
                'priority' => 'high',
                'is_completed' => true,
            ],
            [
                'title' => 'Build frontend components',
                'description' => 'Create Vue components for todo list UI',
                'priority' => 'high',
                'is_completed' => false,
            ],
            [
                'title' => 'Setup PWA features',
                'description' => 'Configure service worker and manifest',
                'priority' => 'medium',
                'is_completed' => false,
            ],
            [
                'title' => 'Implement offline mode',
                'description' => 'Add IndexedDB for offline persistence',
                'priority' => 'medium',
                'is_completed' => false,
            ],
            [
                'title' => 'Write documentation',
                'description' => 'Create comprehensive docs and guides',
                'priority' => 'low',
                'is_completed' => false,
            ],
            [
                'title' => 'Deploy to production',
                'description' => 'Setup hosting and domain',
                'priority' => 'low',
                'is_completed' => false,
            ],
        ];

        foreach ($todos as $todo) {
            Todo::create([
                'user_id' => $user->id,
                ...$todo,
            ]);
        }
    }
}
