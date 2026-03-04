@extends('layouts.app')

@section('title', 'Edit Book - PageTurner')

@section('header')
    <div class="flex justify-between items-center" >
        <div>
            <h1 class="text-3xl font-bold page-turner-font text-white">Edit Book</h1>
            <p class="text-gray-100/80 mt-2">Update book information</p>
        </div>
        <a href="{{ route('books.show', $book) }}" 
           class="text-[var(--pageturner-light)] hover:text-[var(--pageturner-accent)] font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Book
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto" >
        <!-- Form Container -->
        <div class="bg-gradient-to-br from-white to-[#F5EBDC] rounded-xl shadow-lg border border-[#F4A460]/30 p-6 md:p-8 relative overflow-hidden">
            <!-- Book spine effect -->
            <div class="absolute left-0 top-0 bottom-0 w-2 bg-gradient-to-b from-[#5D4037] to-[#8B4513]"></div>
            <form action="{{ route('admin.books.update', $book) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information Section -->
                <div class="border-b border-[#F4A460]/20 pb-6">
                    <h3 class="text-lg font-semibold text-[#8B4513]-800 mb-4">Basic Information</h3>
                    
                    <!-- Title -->
                    <div class="mb-5">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title"
                               value="{{ old('title', $book->title) }}"
                               placeholder="Enter book title"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors @error('title') border-red-500 @enderror"
                               required>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Author -->
                    <div class="mb-5">
                        <label for="author" class="block text-sm font-medium text-gray-700 mb-2">
                            Author <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="author" 
                               id="author"
                               value="{{ old('author', $book->author) }}"
                               placeholder="Enter author name"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors @error('author') border-red-500 @enderror"
                               required>
                        @error('author')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-5">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" 
                                id="category_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors @error('category_id') border-red-500 @enderror"
                                required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Book Details Section -->
                <div class="border-b border-[#F4A460]/20 pb-6">
                    <h3 class="text-lg font-semibold text-[#8B4513]-800 mb-4">Book Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ISBN -->
                        <div>
                            <label for="isbn" class="block text-sm font-medium text-gray-700 mb-2">
                                ISBN <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="isbn" 
                                   id="isbn"
                                   value="{{ old('isbn', $book->isbn) }}"
                                   placeholder="e.g., 978-3-16-148410-0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors @error('isbn') border-red-500 @enderror"
                                   required>
                            @error('isbn')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                Price ($) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">$</span>
                                </div>
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       max="9999.99"
                                       name="price" 
                                       id="price"
                                       value="{{ old('price', $book->price) }}"
                                       placeholder="0.00"
                                       class="pl-7 w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors @error('price') border-red-500 @enderror"
                                       required>
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock Quantity -->
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Stock Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   min="0" 
                                   max="9999"
                                   name="stock_quantity" 
                                   id="stock_quantity"
                                   value="{{ old('stock_quantity', $book->stock_quantity) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors @error('stock_quantity') border-red-500 @enderror"
                                   required>
                            @error('stock_quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pages -->
                        <div>
                            <label for="pages" class="block text-sm font-medium text-gray-700 mb-2">
                                Number of Pages
                            </label>
                            <input type="number" 
                                   min="1" 
                                   max="9999"
                                   name="pages" 
                                   id="pages"
                                   value="{{ old('pages', $book->pages) }}"
                                   placeholder="e.g., 320"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors">
                        </div>

                        <!-- Published Year -->
                        <div>
                            <label for="published_year" class="block text-sm font-medium text-gray-700 mb-2">
                                Published Year
                            </label>
                            <input type="number" 
                                   min="1800" 
                                   max="{{ date('Y') }}"
                                   name="published_year" 
                                   id="published_year"
                                   value="{{ old('published_year', $book->published_year) }}"
                                   placeholder="e.g., 2023"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors">
                        </div>

                        <!-- Publisher -->
                        <div>
                            <label for="publisher" class="block text-sm font-medium text-gray-700 mb-2">
                                Publisher
                            </label>
                            <input type="text" 
                                   name="publisher" 
                                   id="publisher"
                                   value="{{ old('publisher', $book->publisher) }}"
                                   placeholder="e.g., Penguin Books"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors">
                        </div>

                        <!-- Language -->
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                Language
                            </label>
                            <select name="language" 
                                    id="language"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors">
                                <option value="">Select language</option>
                                <option value="English" {{ old('language', $book->language) == 'English' ? 'selected' : '' }}>English</option>
                                <option value="Spanish" {{ old('language', $book->language) == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                <option value="French" {{ old('language', $book->language) == 'French' ? 'selected' : '' }}>French</option>
                                <option value="German" {{ old('language', $book->language) == 'German' ? 'selected' : '' }}>German</option>
                                <option value="Chinese" {{ old('language', $book->language) == 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                <option value="Other" {{ old('language', $book->language) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="border-b border-[#F4A460]/20 pb-6">
                    <h3 class="text-lg font-semibold text-[#8B4513]-800 mb-4">Description</h3>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Book Description
                        </label>
                        <textarea name="description" 
                                  id="description"
                                  rows="6"
                                  placeholder="Provide a detailed description of the book..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-[#8B4513] focus:border-[#8B4513] transition-colors resize-none">{{ old('description', $book->description) }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">
                            Describe the book's content, themes, and any other relevant information.
                        </p>
                    </div>
                </div>

                <!-- Cover Image Section -->
                <div class="border-b border-[#F4A460]/20 pb-6">
                    <h3 class="text-lg font-semibold text-[#8B4513]-800 mb-4">Cover Image</h3>
                    
                    <div class="space-y-4">
                        <!-- Current Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Current Cover
                            </label>
                            <div class="mt-1">
                                @if($book->cover_image)
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                             alt="{{ $book->title }}" 
                                             class="h-40 w-32 object-cover rounded-lg border border-gray-300">
                                        <div>
                                            <p class="text-sm text-gray-600">Current cover image</p>
                                            <label class="flex items-center mt-2">
                                                <input type="checkbox" 
                                                       name="remove_cover" 
                                                       value="1"
                                                       class="rounded border-gray-300 text-[#8B4513] focus:ring-[#8B4513]">
                                                <span class="ml-2 text-sm text-gray-600">Remove current cover</span>
                                            </label>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-[#F5EBDC]-50 border border-[#F4A460]-200 rounded-lg p-4 text-center">
                                        <svg class="h-12 w-12 text-[#8B4513]-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-sm text-[#8B4513] mt-2">No cover image uploaded</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- New Image Upload -->
                        <div>
                            <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload New Cover Image
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-[#F4A460]-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="cover_image" class="relative cursor-pointer bg-white rounded-md font-medium text-[#8B4513] hover:text-[#D2691E] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#8B4513]">
                                            <span>Upload a file</span>
                                            <input id="cover_image" 
                                                   name="cover_image" 
                                                   type="file" 
                                                   accept="image/*"
                                                   class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF up to 2MB
                                    </p>
                                </div>
                            </div>
                        </div>
                        @error('cover_image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Featured Book Toggle -->
                <div class="pb-6">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="featured" 
                               id="featured"
                               value="1"
                               {{ old('featured', $book->featured) ? 'checked' : '' }}
                               class="h-4 w-4 text-[#8B4513] focus:ring-[#8B4513] border-gray-300 rounded">
                        <label for="featured" class="ml-3 text-sm font-medium text-gray-700">
                            Mark as Featured Book
                        </label>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Featured books appear prominently on the homepage.
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-[#F4A460]/20">
                    
                    <div class="flex space-x-3" style="margin-left: 600px;">
                        <a href="{{ route('books.show', $book) }}" 
                           class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" style="margin-left: 20px ;background: linear-gradient(90deg, #8B4513, #D2691E); color: white;" 
                                class="px-8 py-3 bg-gradient-to-r from-[#8B4513] to-[#D2691E] text-black font-medium rounded-lg hover:from-[#D2691E] hover:to-[#8B4513] transition-all shadow-md hover:shadow-lg">
                            Update Book
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Image preview functionality
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const previewContainer = document.getElementById('imagePreviewContainer');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                if (preview) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
            }
            
            reader.readAsDataURL(input.files[0]);
        } else if (preview) {
            previewContainer.classList.add('hidden');
            preview.src = '#';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const coverImageInput = document.getElementById('cover_image');
        if (coverImageInput) {
            coverImageInput.addEventListener('change', function() {
                previewImage(this);
            });
        }
    });
</script>
@endpush