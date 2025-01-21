@extends('Panel.layout.master')

@section('message-box')
    <div class="flex  p-2 w-full items-center justify-center">
        <div
            class="rounded-md border-2 border-white w-full p-2 flex justify-center space-x-3 space-x-reverse items-center sm:w-1/2 md:w-1/3">
            <h1 class="font-bold text-white text-md mt-1 text-center">ثبت نام فروشگاه برای درگاه رمز ارزی</h1>
        </div>
    </div>
@endsection

@section('container')
    <form class="flex items-center justify-center flex-col space-y-3  w-full sm:w-1/2 mx-auto" method="POST" enctype="multipart/form-data"
          action="{{route('panel.payment-service-register-submit')}}" id="form">
        @csrf
        <label class="flex items-center justify-between w-full">
            <span class="text-nowrap">نام فروشگاه</span>
            <input type="text"
                   class="rounded-md  py-1  border border-white outline-none text-black px-2 w-8/12 mx-2"
                   name="name" placeholder="نام شرکت یا فروشگاه">
        </label>
        <label class="flex items-center justify-between w-full">
            <span class="text-nowrap">آدرس سایت</span>
            <input type="text"
                   class="rounded-md  py-1  border border-white outline-none text-black px-2 w-8/12 mx-2"
                   name="domain" placeholder="آدرس وب سایت">
        </label>
        <label class="flex items-center justify-between w-full">
            <span class="text-nowrap">کیف پول ترون</span>
            <input type="text"
                   class="rounded-md  py-1  border border-white outline-none text-black px-2 w-8/12 mx-2"
                   name="tron_wallet" placeholder="آدرس کیف پول Tron">
        </label>
        <label class="flex items-center justify-between w-full">
            <span class="text-nowrap"> محصولات</span>
            <input type="text"
                   class="rounded-md  py-1  border border-white outline-none text-black px-2 w-8/12 mx-2"
                   name="products" placeholder="محصول یا خدمات">
        </label>
        <label class="flex items-center justify-between w-full">
            <span class="text-nowrap">نام و نام خانوادگی</span>
            <input type="text"
                   class="rounded-md  py-1  border border-white outline-none text-black px-2 w-8/12 mx-2"
                   name="person_name" placeholder="مسئول مربوطه">
        </label>
        <label class="flex items-center justify-between w-full">
            <span class="text-nowrap">ایمیل</span>
            <input type="text"
                   class="rounded-md  py-1  border border-white outline-none text-black px-2 w-8/12 mx-2"
                   name="email" placeholder="Email">
        </label>
        <label class="flex items-center justify-between w-full">
            <span class="text-nowrap">شماره موبایل</span>
            <input type="text"
                   class="rounded-md  py-1  border border-white outline-none text-black px-2 w-8/12 mx-2"
                   name="phone" placeholder="شماره تماس جهت پیگیری">
        </label>
        <label class="flex items-center justify-between w-full">
            <span class="text-nowrap">بارگذاری جواز کسب</span>
            <input type="file"
                   class="rounded-md  py-1  border border-white outline-none text-black px-2 w-8/12 mx-2"
                   name="license_image">
        </label>

        <div class="flex justify-around w-full">
            <button class="flex items-center justify-start bg-sky-500 rounded-md cursor-pointer py-2 px-4" type="submit">
                <span>ثبت درخواست</span>
            </button>
            <a href="{{route('panel.index')}}" class="block bg-red-600 rounded-md py-2 px-4">
                <span>بازگشت به صفحه اصلی</span>
            </a>
        </div>
        <a href="{{route('payment-service')}}" class="block bg-red-600 rounded-md py-2 px-4">
            <span>آموزش استفاده از درگاه رمز ارزی</span>
        </a>
    </form>

@endsection



@section('script-tag')

@endsection
