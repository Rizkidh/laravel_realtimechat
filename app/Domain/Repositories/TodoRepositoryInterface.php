<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\TodoEntity;
use Illuminate\Support\Collection;

interface TodoRepositoryInterface
{
    public function save(TodoEntity $todo): TodoEntity;

    public function findById(int $id): ?TodoEntity;

    public function findByUserId(int $userId): Collection;

    public function update(TodoEntity $todo): TodoEntity;

    public function delete(int $id): bool;

    public function getCompletedByUserId(int $userId): Collection;

    public function getIncompleteByUserId(int $userId): Collection;
}
