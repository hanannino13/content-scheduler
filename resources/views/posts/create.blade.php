@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-semibold text-gray-800 mb-6">Create New Post</h2>

<div class="bg-white shadow-md rounded-lg p-6">
    <form id="post-form" action="{{ route('posts.store') }}" method="POST">
        @csrf

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
            <input type="text" id="title" name="title"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Optional Title" value="{{ old('title') }}">
            @error('title')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content:</label>
            <textarea id="content" name="content" rows="6"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="Your post content..." required>{{ old('content') }}</textarea>
            <p class="text-gray-600 text-xs mt-1">Characters: <span id="character-count">0</span> / <span id="twitter-limit">280</span> (Twitter)</p>
            @error('content')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="image_url" class="block text-gray-700 text-sm font-bold mb-2">Image URL (optional):</label>
            <input type="url" id="image_url" name="image_url"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                placeholder="e.g., https://example.com/image.jpg" value="{{ old('image_url') }}">
            @error('image_url')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Select Platforms:</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($platforms as $platform)
                <div class="flex items-center">
                    <input type="checkbox" id="platform_{{ $platform->id }}" name="platform_ids[]" value="{{ $platform->id }}"
                        class="form-checkbox h-5 w-5 text-blue-600 rounded"
                        {{ in_array($platform->id, old('platform_ids', [])) ? 'checked' : '' }}>
                    <label for="platform_{{ $platform->id }}" class="ml-2 text-gray-700">{{ $platform->name }}</label>
                </div>
                @endforeach
            </div>
            @error('platform_ids')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="scheduled_time" class="block text-gray-700 text-sm font-bold mb-2">Scheduled Date & Time:</label>
            <input type="datetime-local" id="scheduled_time" name="scheduled_time" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                value="{{ old('scheduled_time') }}">
            @error('scheduled_time')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Schedule Post
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr
        flatpickr("#scheduled_time", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            minuteIncrement: 5,
            altInput: true,
            altFormat: "F j, Y h:i K",
        });

        // Character Counter
        const contentTextarea = document.getElementById('content');
        const charCountSpan = document.getElementById('character-count');
        const twitterLimitSpan = document.getElementById('twitter-limit');

        contentTextarea.addEventListener('input', function() {
            const currentLength = contentTextarea.value.length;
            charCountSpan.textContent = currentLength;

            if (currentLength > parseInt(twitterLimitSpan.textContent)) {
                charCountSpan.classList.add('text-red-500');
            } else {
                charCountSpan.classList.remove('text-red-500');
            }
        });

       
    });
</script>
@endpush