@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Post #{{ $post->id }}</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form id="post-form" action="{{ url('/api/posts/' . $post->id) }}" method="POST">
            {{-- No @csrf for API --}}
            {{-- This hidden input tells Laravel's API that it's a PUT request --}}
            <input type="hidden" name="_method" value="PUT">

            <div class="mb-4">
                <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                <input type="text" id="title" name="title"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="Optional Title" value="{{ old('title', $post->title) }}">
                <p id="title-error" class="text-red-500 text-xs italic mt-1 hidden"></p>
            </div>

            <div class="mb-4">
                <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content:</label>
                <textarea id="content" name="content" rows="6"
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          placeholder="Your post content..." required>{{ old('content', $post->content) }}</textarea>
                <p class="text-gray-600 text-xs mt-1">Characters: <span id="character-count">0</span> / <span id="twitter-limit">280</span> (Twitter)</p>
                <p id="content-error" class="text-red-500 text-xs italic mt-1 hidden"></p>
            </div>

            <div class="mb-4">
                <label for="image_url" class="block text-gray-700 text-sm font-bold mb-2">Image URL (optional):</label>
                <input type="url" id="image_url" name="image_url"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="e.g., https://example.com/image.jpg" value="{{ old('image_url', $post->image_url) }}">
                <p id="image_url-error" class="text-red-500 text-xs italic mt-1 hidden"></p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Select Platforms:</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($platforms as $platform)
                        <div class="flex items-center">
                            <input type="checkbox" id="platform_{{ $platform->id }}" name="platform_ids[]" value="{{ $platform->id }}"
                                   class="form-checkbox h-5 w-5 text-blue-600 rounded"
                                   @if(in_array($platform->id, $selectedPlatformIds)) checked @endif>
                            <label for="platform_{{ $platform->id }}" class="ml-2 text-gray-700">{{ $platform->name }}</label>
                        </div>
                    @endforeach
                </div>
                <p id="platform_ids-error" class="text-red-500 text-xs italic mt-1 hidden"></p>
            </div>

            <div class="mb-6">
                <label for="scheduled_time" class="block text-gray-700 text-sm font-bold mb-2">Scheduled Date & Time:</label>
                {{-- Format the datetime for input type="datetime-local" --}}
                <input type="datetime-local" id="scheduled_time" name="scheduled_time" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       value="{{ old('scheduled_time', $post->scheduled_time ? \Carbon\Carbon::parse($post->scheduled_time)->format('Y-m-d\TH:i') : '') }}">
                <p id="scheduled_time-error" class="text-red-500 text-xs italic mt-1 hidden"></p>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Post
                </button>
                <div id="form-message" class="text-sm font-semibold text-green-600 hidden"></div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Flatpickr
            flatpickr("#scheduled_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                minDate: "today", // Prevents selecting past dates
                minuteIncrement: 5,
                altInput: true,
                altFormat: "F j, Y h:i K",
            });

            // Character Counter (same as create)
            const contentTextarea = document.getElementById('content');
            const charCountSpan = document.getElementById('character-count');
            const twitterLimitSpan = document.getElementById('twitter-limit');

            // Initialize count on load
            charCountSpan.textContent = contentTextarea.value.length;

            contentTextarea.addEventListener('input', function() {
                const currentLength = contentTextarea.value.length;
                charCountSpan.textContent = currentLength;

                if (currentLength > parseInt(twitterLimitSpan.textContent)) {
                    charCountSpan.classList.add('text-red-500');
                } else {
                    charCountSpan.classList.remove('text-red-500');
                }
            });

            // Form Submission (AJAX for API)
            const postForm = document.getElementById('post-form');
            const formMessage = document.getElementById('form-message');

            postForm.addEventListener('submit', async function(event) {
                event.preventDefault();

                document.querySelectorAll('.text-red-500.text-xs.italic.mt-1').forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });
                formMessage.classList.add('hidden');

                const formData = new FormData(postForm);
                const data = Object.fromEntries(formData.entries());
                data.platform_ids = Array.from(postForm.querySelectorAll('input[name="platform_ids[]"]:checked')).map(cb => parseInt(cb.value));

                // Use the hidden _method field for PUT
                const method = formData.get('_method') || postForm.method;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                try {
                    const response = await fetch(postForm.action, {
                        method: method, // Will be 'PUT'
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                            // 'Authorization': `Bearer ${localStorage.getItem('sanctum_token')}`
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && result.errors) {
                            for (const field in result.errors) {
                                const errorEl = document.getElementById(`${field.replace('.', '_')}-error`);
                                if (errorEl) {
                                    errorEl.textContent = result.errors[field][0];
                                    errorEl.classList.remove('hidden');
                                }
                            }
                        } else {
                            formMessage.textContent = result.message || 'An unexpected error occurred.';
                            formMessage.classList.remove('hidden');
                            formMessage.classList.add('text-red-600');
                            formMessage.classList.remove('text-green-600');
                        }
                        return;
                    }

                    formMessage.textContent = 'Post updated successfully!';
                    formMessage.classList.remove('hidden');
                    formMessage.classList.add('text-green-600');
                    formMessage.classList.remove('text-red-600');

                    // Optionally, update the URL or redirect if needed
                    // window.location.href = '/dashboard';

                } catch (error) {
                    console.error('Error submitting form:', error);
                    formMessage.textContent = 'Network error. Please try again.';
                    formMessage.classList.remove('hidden');
                    formMessage.classList.add('text-red-600');
                    formMessage.classList.remove('text-green-600');
                }
            });
        });
    </script>
@endpush