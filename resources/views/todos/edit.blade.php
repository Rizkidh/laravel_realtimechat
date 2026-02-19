@extends('layouts.app')

@section('title', 'Edit Todo - ' . $todo->title)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Tugas</h2>

        <form action="{{ route('todos.update', $todo->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul *</label>
                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $todo->title) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea
                    name="description"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >{{ old('description', $todo->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Deadline</label>
                    <input
                        type="date"
                        name="due_date"
                        value="{{ old('due_date', $todo->due_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    @error('due_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                    <select
                        name="priority"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="low" {{ old('priority', $todo->priority) === 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ old('priority', $todo->priority) === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ old('priority', $todo->priority) === 'high' ? 'selected' : '' }}>Tinggi</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_completed"
                        value="1"
                        {{ $todo->is_completed ? 'checked' : '' }}
                        class="w-5 h-5 text-blue-500 rounded focus:ring-2 focus:ring-blue-500"
                    />
                    <span class="text-gray-700">Tandai sebagai selesai</span>
                </label>
            </div>

            <div class="flex gap-4 pt-4">
                <button
                    type="submit"
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
                >
                    Simpan Perubahan
                </button>
                <a
                    href="{{ route('todos.index') }}"
                    class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200 text-center"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
