@extends('layouts.admin.app')

@section('content')
    <div class="w-full bg-base-200 min-h-screen">
        <div class="flex-col">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold">{{ __('fridge.edit_item') }} </h1>
            </div>
            <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
                <div class="card-body">
                    <form action="{{ route('fridge-items.update',  $fridgeItem) }}" method="POST">
                        @method('PATCH')
                        @csrf
                        @include('admin.fridge._form', [
                            'fridgeItem' => $fridgeItem,
                            'submitLabel' => __('fridge.update')
                            ])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
