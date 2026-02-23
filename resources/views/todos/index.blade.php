@extends('layouts.app')

@section('title', 'Todo List - Manage Your Tasks')

@section('content')
<div class="space-y-8">
    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium mb-2">Total Tugas</p>
            <p class="text-3xl font-bold text-blue-600">{{ $todos->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium mb-2">Belum Selesai</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $incompleteCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium mb-2">Sudah Selesai</p>
            <p class="text-3xl font-bold text-green-600">{{ $completedCount }}</p>
        </div>
    </div>

    <!-- Add Todo Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Tambah Tugas Baru</h2>
        <form action="{{ route('todos.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                <input
                    type="text"
                    name="title"
                    placeholder="Masukkan judul tugas..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea
                    name="description"
                    placeholder="Masukkan deskripsi (opsional)..."
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Deadline</label>
                    <input
                        type="date"
                        name="due_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                    <select
                        name="priority"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="low">Rendah</option>
                        <option value="medium" selected>Sedang</option>
                        <option value="high">Tinggi</option>
                    </select>
                </div>
            </div>

            <button
                type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
            >
                + Tambah Tugas
            </button>
        </form>
    </div>

    <!-- Todos List -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Daftar Tugas</h2>
            @if ($todos->count() > 0)
                <button
                    type="submit"
                    form="batch-delete-form"
                    id="btn-batch-delete"
                    class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed hidden"
                >
                    Hapus Terpilih (<span id="selected-count">0</span>)
                </button>
            @endif
        </div>

        @if ($todos->count() == 0)
            <p class="text-center text-gray-500 py-8">Tidak ada tugas. Mulai tambahkan tugas baru!</p>
        @else
            <form id="batch-delete-form" action="{{ route('todos.batchDestroy') }}" method="POST" onsubmit="return confirmBatchDelete();">
                @csrf
                @method('DELETE')

                <!-- Select All -->
                <div class="flex items-center gap-2 mb-3 pb-3 border-b border-gray-200">
                    <input type="checkbox" id="select-all" class="w-5 h-5 text-blue-500 rounded cursor-pointer" />
                    <label for="select-all" class="text-sm font-medium text-gray-600 cursor-pointer">Pilih Semua</label>
                </div>

                <div class="space-y-3">
                    @foreach ($todos as $todo)
                        <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <!-- Batch Select Checkbox -->
                            <input
                                type="checkbox"
                                name="ids[]"
                                value="{{ $todo->id }}"
                                class="todo-checkbox w-5 h-5 text-red-500 rounded cursor-pointer mt-1"
                            />

                            <!-- Toggle Checkbox -->
                            

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start gap-2 mb-2">
                                    <h3 class="text-lg font-medium {{ $todo->is_completed ? 'line-through text-gray-400' : 'text-gray-800' }}">
                                        {{ $todo->title }}
                                    </h3>
                                    <span class="px-2 py-1 text-xs font-medium rounded whitespace-nowrap
                                        @if ($todo->priority === 'high')
                                            bg-red-100 text-red-800
                                        @elseif ($todo->priority === 'medium')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-green-100 text-green-800
                                        @endif
                                    ">
                                        {{ $todo->priority === 'low' ? 'Rendah' : ($todo->priority === 'high' ? 'Tinggi' : 'Sedang') }}
                                    </span>
                                </div>

                                @if ($todo->description)
                                    <p class="text-gray-600 text-sm mb-2">{{ $todo->description }}</p>
                                @endif

                                <div class="flex gap-4 text-xs text-gray-500">
                                    @if ($todo->dueDate)
                                        <span>{{ $todo->dueDate->format('d M Y') }}</span>
                                    @endif
                                    <span>{{ $todo->createdAt->format('d M Y') }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a
                                    href="{{ route('todos.edit', $todo->id) }}"
                                    class="text-blue-500 hover:text-blue-700 hover:bg-blue-50 p-2 rounded transition"
                                    title="Edit tugas"
                                >
                                    Edit
                                </a>

                                <button
                                    type="button"
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition"
                                    title="Hapus tugas"
                                    onclick="deleteSingle({{ $todo->id }})"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>

            <!-- Hidden form for single delete -->
            <form id="single-delete-form" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</div>

@push('scripts')
<script src="{{ asset('storage/script/todos/service.js') }}"></script>
<script src="{{ asset('storage/script/todos/controller.js') }}"></script>
@endpush
@endsection

