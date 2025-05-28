@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Profile Settings</h2>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">Update Profile Information</h3>
        @if (session('status') === 'profile-updated')
            <p class="text-sm text-green-600 mb-4">Your profile has been updated successfully!</p>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('patch') {{-- Use PATCH method for partial updates --}}

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required autofocus>
                @error('name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Save
                </button>
            </div>
        </form>
    </div>

    {{-- You might also want sections for updating password and deleting account here --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4">Update Password</h3>
        @if (session('status') === 'password-updated')
            <p class="text-sm text-green-600 mb-4">Your password has been updated successfully!</p>
        @endif
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('put') {{-- Use PUT method for updating password --}}

            <div class="mb-4">
                <label for="current_password" class="block text-gray-700 text-sm font-bold mb-2">Current Password:</label>
                <input type="password" name="current_password" id="current_password"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('current_password')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">New Password:</label>
                <input type="password" name="password" id="password"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('password')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('password_confirmation')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Password
                </button>
            </div>
        </form>
    </div>
@endsection