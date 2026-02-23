@extends('layouts.app')

@section('title', 'Add New Category - PageTurner')

@section('header')
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Add New Category</h1>
            <p class="text-gray-100/80 mt-2">Create a new book category</p>
        </div>
        <a href="{{ route('categories.index') }}" 
           class="text-[var(--pageturner-light)] hover:text-[var(--pageturner-accent)] font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Categories
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-gradient-to-br from-white to-[#F5EBDC] rounded-xl shadow-lg border border-[#F4A460]/30 p-6 md:p-8 relative overflow-hidden">
            <!-- Book spine effect -->
            <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-[#5D4037] to-[#8B4513]"></div>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Category Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name"
                           value="{{ old('name') }}"
                           placeholder="e.g., Fiction, Science, Biography"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" 
                              id="description"
                              rows="4"
                              placeholder="Describe this category..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors resize-none">{{ old('description') }}</textarea>
                    <p class="mt-2 text-sm text-gray-500">
                        Optional: Provide a brief description of this category.
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('categories.index') }}" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" style="margin-left: 20px ;background: linear-gradient(90deg, #8B4513, #D2691E); color: white;" 
        class="px-8 py-3 bg-gradient-to-r from-[#8B4513] to-[#D2691E] text-black font-medium rounded-lg hover:from-[#D2691E] hover:to-[#8B4513] transition-all shadow-md hover:shadow-lg flex items-center justify-center">
    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Create Category
</button>
                </div>
            </form>
        </div>
    </div>
@endsection