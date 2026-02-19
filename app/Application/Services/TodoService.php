<?php

namespace App\Application\Services;

use App\Domain\Entities\TodoEntity;
use App\Domain\Repositories\TodoRepositoryInterface;
use Illuminate\Support\Collection;

class TodoService
{
    public function __construct(private TodoRepositoryInterface $todoRepository)
    {
    }

    public function createTodo(
        int $userId,
        string $title,
        ?string $description = null,
        ?string $dueDate = null,
        string $priority = 'medium'
    ): TodoEntity {
        $todo = new TodoEntity(
            id: 0,
            userId: $userId,
            title: $title,
            description: $description,
            isCompleted: false,
            dueDate: $dueDate ? new \DateTime($dueDate) : null,
            priority: $priority
        );

        return $this->todoRepository->save($todo);
    }

    public function getTodoById(int $id): ?TodoEntity
    {
        return $this->todoRepository->findById($id);
    }

    public function getUserTodos(int $userId): Collection
    {
        return $this->todoRepository->findByUserId($userId);
    }

    public function getCompletedTodos(int $userId): Collection
    {
        return $this->todoRepository->getCompletedByUserId($userId);
    }

    public function getIncompleteTodos(int $userId): Collection
    {
        return $this->todoRepository->getIncompleteByUserId($userId);
    }

    public function updateTodo(
        int $id,
        string $title,
        ?string $description = null,
        ?string $dueDate = null,
        string $priority = 'medium',
        bool $isCompleted = false
    ): TodoEntity {
        $todo = $this->todoRepository->findById($id);

        if (!$todo) {
            throw new \Exception('Todo not found');
        }

        $todo->setTitle($title);
        $todo->setDescription($description);
        $todo->setPriority($priority);

        if ($dueDate) {
            $todo->setDueDate(new \DateTime($dueDate));
        }

        if ($isCompleted) {
            $todo->markAsCompleted();
        } else {
            $todo->markAsIncomplete();
        }

        return $this->todoRepository->update($todo);
    }

    public function toggleTodoStatus(int $id): TodoEntity
    {
        $todo = $this->todoRepository->findById($id);

        if (!$todo) {
            throw new \Exception('Todo not found');
        }

        if ($todo->isCompleted()) {
            $todo->markAsIncomplete();
        } else {
            $todo->markAsCompleted();
        }

        return $this->todoRepository->update($todo);
    }

    public function deleteTodo(int $id): bool
    {
        return $this->todoRepository->delete($id);
    }

    public function markAsCompleted(int $id): TodoEntity
    {
        $todo = $this->todoRepository->findById($id);

        if (!$todo) {
            throw new \Exception('Todo not found');
        }

        $todo->markAsCompleted();

        return $this->todoRepository->update($todo);
    }

    public function markAsIncomplete(int $id): TodoEntity
    {
        $todo = $this->todoRepository->findById($id);

        if (!$todo) {
            throw new \Exception('Todo not found');
        }

        $todo->markAsIncomplete();

        return $this->todoRepository->update($todo);
    }
}
