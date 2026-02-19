<?php

namespace App\Http\Controllers;

use App\Application\Services\TodoService;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function __construct(private TodoService $todoService)
    {
    }

    public function index()
    {
        $todos = $this->todoService->getUserTodos(auth()->id());
        $completedCount = $todos->filter(fn($t) => $t->isCompleted())->count();
        $incompleteCount = $todos->filter(fn($t) => !$t->isCompleted())->count();

        return view('todos.index', [
            'todos' => $todos,
            'completedCount' => $completedCount,
            'incompleteCount' => $incompleteCount,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date_format:Y-m-d',
            'priority' => 'in:low,medium,high',
        ]);

        try {
            $this->todoService->createTodo(
                userId: auth()->id(),
                title: $validated['title'],
                description: $validated['description'] ?? null,
                dueDate: $validated['due_date'] ?? null,
                priority: $validated['priority'] ?? 'medium'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat todo: ' . $e->getMessage());
        }

        return back()->with('success', 'Todo berhasil ditambahkan!');
    }

    public function edit(Todo $todo)
    {
        // IDOR protection: only owner can edit
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        return view('todos.edit', [
            'todo' => (object) [
                'id' => $todo->id,
                'title' => $todo->title,
                'description' => $todo->description,
                'due_date' => $todo->due_date,
                'priority' => $todo->priority,
                'is_completed' => $todo->is_completed,
            ]
        ]);
    }

    public function update(Request $request, Todo $todo)
    {
        // IDOR protection: only owner can update
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date_format:Y-m-d',
            'priority' => 'in:low,medium,high',
            'is_completed' => 'boolean',
        ]);

        try {
            $this->todoService->updateTodo(
                id: $todo->id,
                title: $validated['title'],
                description: $validated['description'],
                dueDate: $validated['due_date'],
                priority: $validated['priority'],
                isCompleted: $validated['is_completed'] ?? false
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate todo: ' . $e->getMessage());
        }

        return redirect()->route('todos.index')->with('success', 'Status berhasil diubah!');
    }

    public function destroy(Todo $todo)
    {
        // IDOR protection: only owner can delete
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        try {
            $this->todoService->deleteTodo($todo->id);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus todo: ' . $e->getMessage());
        }

        return back()->with('success', 'Todo berhasil dihapus!');
    }

    public function toggle(Todo $todo)
    {
        // IDOR protection: only owner can toggle
        if ($todo->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        try {
            $this->todoService->toggleTodoStatus($todo->id);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }

        return back()->with('success', 'Status berhasil diubah!');
    }

    public function batchDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:todos,id',
        ]);

        // IDOR protection: only delete todos owned by current user
        $ownedIds = Todo::whereIn('id', $validated['ids'])
            ->where('user_id', auth()->id())
            ->pluck('id')
            ->toArray();

        $deleted = 0;
        foreach ($ownedIds as $id) {
            try {
                $this->todoService->deleteTodo($id);
                $deleted++;
            } catch (\Exception $e) {
                // skip failed ones
            }
        }

        return back()->with('success', "{$deleted} tugas berhasil dihapus!");
    }
}
