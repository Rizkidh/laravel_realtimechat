<?php

namespace App\Domain\Entities;

use DateTime;

class TodoEntity
{
    private int $id;
    private int $userId;
    private string $title;
    private ?string $description;
    private bool $isCompleted;
    private ?DateTime $dueDate;
    private string $priority;
    private DateTime $createdAt;
    private DateTime $updatedAt;

    public function __construct(
        int $id,
        int $userId,
        string $title,
        ?string $description = null,
        bool $isCompleted = false,
        ?DateTime $dueDate = null,
        string $priority = 'medium',
        DateTime $createdAt = null,
        DateTime $updatedAt = null,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->description = $description;
        $this->isCompleted = $isCompleted;
        $this->dueDate = $dueDate;
        $this->priority = $priority;
        $this->createdAt = $createdAt ?? new DateTime();
        $this->updatedAt = $updatedAt ?? new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function markAsCompleted(): void
    {
        $this->isCompleted = true;
    }

    public function markAsIncomplete(): void
    {
        $this->isCompleted = false;
    }

    public function getDueDate(): ?DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): void
    {
        if (!in_array($priority, ['low', 'medium', 'high'])) {
            throw new \InvalidArgumentException('Invalid priority');
        }
        $this->priority = $priority;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Magic method to allow property access via getter methods
     * Enables $entity->id instead of $entity->getId()
     */
    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        // Special case for is_completed
        if ($name === 'is_completed') {
            return $this->isCompleted();
        }

        throw new \Exception("Property '{$name}' does not exist");
    }

    /**
     * Magic method to check if property exists
     */
    public function __isset(string $name): bool
    {
        $method = 'get' . ucfirst($name);
        return method_exists($this, $method) || $name === 'is_completed';
    }
}
