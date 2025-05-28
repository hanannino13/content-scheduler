@extends('layouts.app')

@section('content')
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Platform Management</h2>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Available Social Platforms</h3>

        @if($platforms->isEmpty())
            <p class="text-gray-600">No platforms configured. Please seed the database.</p>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($platforms as $platform)
                    <li class="py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-lg font-medium text-gray-900">{{ $platform->name }}</span>
                            <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($platform->type) }}</span>
                        </div>
                        {{-- <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="{{ $platform->id }}" class="sr-only peer" @if(true) checked @endif>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-900">Active for User</span>
                        </label> --}}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection