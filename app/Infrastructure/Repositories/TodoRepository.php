<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\TodoEntity;
use App\Domain\Repositories\TodoRepositoryInterface;
use App\Models\Todo;
use Illuminate\Support\Collection;

class TodoRepository implements TodoRepositoryInterface
{
    public function __construct(private Todo $model)
    {
    }

    public function save(TodoEntity $todo): TodoEntity
    {
        $model = $this->model->create([
            'user_id' => $todo->getUserId(),
            'title' => $todo->getTitle(),
            'description' => $todo->getDescription(),
            'is_completed' => $todo->isCompleted(),
            'due_date' => $todo->getDueDate(),
            'priority' => $todo->getPriority(),
        ]);

        return $this->mapToEntity($model);
    }

    public function findById(int $id): ?TodoEntity
    {
        $model = $this->model->find($id);

        if (!$model) {
            return null;
        }

        return $this->mapToEntity($model);
    }

    public function findByUserId(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => $this->mapToEntity($model));
    }

    public function update(TodoEntity $todo): TodoEntity
    {
        $model = $this->model->find($todo->getId());

        if (!$model) {
            throw new \Exception('Todo not found');
        }

        $model->update([
            'title' => $todo->getTitle(),
            'description' => $todo->getDescription(),
            'is_completed' => $todo->isCompleted(),
            'due_date' => $todo->getDueDate(),
            'priority' => $todo->getPriority(),
        ]);

        return $this->mapToEntity($model);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    public function getCompletedByUserId(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_completed', true)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn ($model) => $this->mapToEntity($model));
    }

    public function getIncompleteByUserId(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_completed', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => $this->mapToEntity($model));
    }

    private function mapToEntity(Todo $model): TodoEntity
    {
        return new TodoEntity(
            id: $model->id,
            userId: $model->user_id,
            title: $model->title,
            description: $model->description,
            isCompleted: $model->is_completed,
            dueDate: $model->due_date ? $model->due_date->toDateTime() : null,
            priority: $model->priority,
            createdAt: $model->created_at->toDateTime(),
            updatedAt: $model->updated_at->toDateTime(),
        );
    }
}
