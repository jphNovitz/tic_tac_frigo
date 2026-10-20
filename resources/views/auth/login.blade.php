@extends('layouts.public.app')


@section('content')
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row-reverse">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold">{{ __('auth.login') }} </h1>

            </div>
            <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
                <div class="card-body">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <fieldset class="fieldset">
                            <label for="email" class="label">{{__('auth.email')}}</label>
                            <input type="email" name="email"   value="{{ old('email') }}" class="input" placeholder="{{__('auth.email')}}"/>
                            @if ($errors->has('email'))
                                <span class="text-error text-sm">{{ $errors->first('email') }}</span>
                            @endif
                            <label for="password" class="label">{{__('auth.password')}}</label>
                            <input type="password" name="password"   value="{{ old('password') }}" class="input" placeholder="{{__('auth.password')}}"/>
                            @if ($errors->has('password'))
                                <span class="text-error text-sm">{{ $errors->first('password') }}</span>
                            @endif
                            <label class="label cursor-pointer justify-start gap-2 mt-2">
                                <input type="checkbox" name="remember" class="checkbox checkbox-sm" />
                                <span class="label-text text-sm">{{ __('auth.remember_me') }}</span>
                            </label>
                            <div><a class="link link-hover">{{__('auth.forgot_password')}}</a></div>
                            <button class="btn btn-neutral mt-4">{{__('auth.login')}}</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
