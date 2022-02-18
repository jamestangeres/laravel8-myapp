@extends('layouts.app')

@section('content')
    <div class="flex justify-center">
        <div class="w-8/12 bg-white p-6 rounded-lg">
            <form action="{{ route('posts') }}" method="post">
                @csrf

                <div class="mb-4">
                    <label for="body" class="sr-only">Body</label>
                    <textarea name="" id="" cols="30" rows="10"></textarea>
                </div>
            </form>
        </div>
    </div>
@endsection