@extends('layouts.admin.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-neutral">{{ __('auth.logout') }}</button>
    </form>
</div>
@endsection
