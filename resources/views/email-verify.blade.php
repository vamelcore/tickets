@extends('layout')

@section('title', 'Email verification')

@section('content')
    <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-1">
            <div class="p-6">
                <div class="text-gray-600 dark:text-gray-400 text-sm">
                    Hello, {{ $user->name }}!<br>
                    Your email {{ $user->email }} verified successfully!
                </div>
            </div>
        </div>
    </div>
@endsection
