@php use App\Enums\UnitLabel; @endphp

@extends('layouts.admin.app')

@section('content')
    <div class="w-full bg-base-200 min-h-screen">
        <div class="flex-col">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold">{{ __('fridge.add_item') }} </h1>

            </div>
            <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
                <div class="card-body">
                    <form action="{{ route('fridge-items.store') }}" method="POST">
                        @csrf
                        <fieldset class="fieldset">
                            <div class="flex">
                                <div>
                                    <label for="unit_count" class="label">{{__('fridge.unit_count')}}</label>
                                    <input type="number" id="unit_count" name="unit_count" class="input" min="1" step="1"
                                           value="{{ old('unit_count', 1) }}"
                                           placeholder="{{__('fridge.unit_count')}}"/>
                                    @if ($errors->has('unit_count'))
                                        <span class="text-error text-sm">{{ $errors->first('unit_count') }}</span>
                                    @endif
                                </div>
                                <div>
                                    <select name="unit_label" id="unit_label" class="select">
                                        <option value="">{{ __('fridge.unit_label_empty') }}</option>

                                        @foreach (UnitLabel::cases() as $unitLabel)
                                            <option
                                                value="{{ $unitLabel->value }}"
                                                @selected(old('unit_label') === $unitLabel->value)
                                            >
                                                {{ $unitLabel->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('unit_label'))
                                        <span class="text-error text-sm">{{ $errors->first('unit_label') }}</span>
                                    @endif
                                </div>
                            </div>

                            <label for="product_name" class="label">{{__('fridge.product_name')}}</label>
                            <input type="text" id="product_name" name="product_name" class="input"
                                   value="{{ old('product_name') }}"
                                   placeholder="{{__('fridge.product_name')}}"/>
                            @if ($errors->has('product_name'))
                                <span class="text-error text-sm">{{ $errors->first('product_name') }}</span>
                            @endif

                            <label for="expires_at" class="label">{{__('fridge.expires_at')}}</label>
                            <input type="date" id="expires_at" name="expires_at" class="input"
                                   value="{{ old('expires_at') }}"
                                   placeholder="{{__('fridge.expires_at')}}"/>
                            @if ($errors->has('expires_at'))
                                <span class="text-error text-sm">{{ $errors->first('expires_at') }}</span>
                            @endif

                            <button type="submit" class="btn btn-neutral mt-4">{{__('fridge.submit')}}</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
