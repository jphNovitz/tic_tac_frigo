@extends('layouts.admin.app')

@section('content')

    <ul class="list bg-base-100 rounded-box shadow-md">
        <li class="p-4 pb-2 text-xs opacity-60 tracking-wide">{{__('fridge.index_title')}}</li>

        @foreach($fridgeItems as $item)
            <li class="list-row">
                <div>
                    <div>{{ $item->unit_count }} </div>
                    <div>{{ $item->product_name }} </div>
                    <div
                        class="flex flex-col lg:flex-row text-xs uppercase font-semibold opacity-60">{{__('fridge.expires_at')}}
                        : {{$item->expires_at->format('d m Y')}}
                    </div>
                    <div class="inline-grid *:[grid-area:1/1]">
                        @if($item->expireAlert['diff'] < 0)
                            <div class="status status-error animate-ping"></div>
                            <div class="status status-error"></div>
                        @elseif($item->expireAlert['diff'] >= 0 and $item->expireAlert['diff'] < 14)
                            <div class="status status-warning animate-ping"></div>
                            <div class="status status-warning"></div>
                        @else
                            <div class="status status-success animate-ping"></div>
                            <div class="status status-success"></div>
                        @endif
                    </div>
                    {{$item->expireAlert['label']}}
                    <form action="{{route('fridge-items.increase', $item)}}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline btn-success mt-2">{{__('fridge.increase')}}</button>
                    </form>
                    <form action="{{route('fridge-items.decrease', $item)}}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline btn-success mt-2">{{__('fridge.decrease')}}</button>
                    </form>
                    <form action="{{route('fridge-items.consume', $item)}}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline btn-success mt-2">{{__('fridge.consume')}}</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endsection
