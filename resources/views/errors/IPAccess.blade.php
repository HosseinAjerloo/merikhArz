@extends('Auth.Layout.master')
@section('message-box')
    <h1 class=" border-2 border-red-500 rounded-md py-3 px-12 font-semibold text-lg ">
         VPN شما فعال می باشد
    </h1>
@endsection

@section('action')
    <article class="p-3 flex flex-col items-center justify-center">
        <div class="m-2">
            <p style="background-color: #d7e9ff" class="text-center p-3 rounded-lg font-semibold text-4xl text-red-500">
                لطفا VPN خود را خاموش کنید و سپس ادامه را بزنید یا صفحه را رفرش کنید
            </p>
        </div>
            <a href="{{request()->getRequestUri()}}" class="py-4 px-24 m-4 rounded-md font-semibold bg-sky-400 text-black" type="submit">ادامه</a>
        <img alt="turn off vpn" src="{{asset('src/images/turn-off-vpn.jpg')}}" class="mt-8 w-full max-w-[480px]">
    </article>
@endsection


@section('container')


@endsection
