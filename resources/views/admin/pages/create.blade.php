@extends('layouts.admin')

@section('title', 'Create Page')

@section('content')
<div class="p-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Page</h1>
        <a href="{{ route('admin.pages.index') }}" 
           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.pages.store') }}" method="POST">
            @csrf

            <!-- Title -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Page Title <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter page title">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    URL Slug <span class="text-gray-500 text-xs">(Leave blank to auto-generate)</span>
                </label>
                <input type="text" 
                       name="slug" 
                       value="{{ old('slug') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="e.g., about-us">
                @error('slug')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Page Content <span class="text-red-500">*</span>
                </label>
                <textarea name="content" 
                          rows="15" 
                          required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Enter page content (HTML allowed)">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">You can use HTML tags for formatting.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <!-- Show in Footer -->
                <div class="flex items-center pt-8">
                    <input type="checkbox" 
                           name="show_in_footer" 
                           id="show_in_footer"
                           {{ old('show_in_footer') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="show_in_footer" class="ml-2 text-sm text-gray-700">
                        Show in Footer
                    </label>
                </div>

                <!-- Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                    <input type="number" 
                           name="order" 
                           value="{{ old('order', 0) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Submit -->
            <div class="flex space-x-3">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Create Page
                </button>
                <a href="{{ route('admin.pages.index') }}" 
                   class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection