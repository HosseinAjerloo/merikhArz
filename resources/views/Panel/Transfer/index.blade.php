@extends('Panel.layout.master')
@section('container')
    <section class="w-full flex items-center justify-center">
        <div class="w-full sm:w-4/5 md:w-3/5 lg:w-1/2 xl:w-1/3 flex items-center flex-col justify-center  p-8 rounded-lg space-y-8 bg-F5F5F5">

            <div class="w-full rounded-md bg-F5F5F5 border-black border-2  p-8 relative ">
                <div class="  border-black border absolute -right-[6px] -top-[15px] flex space-x-4 space-x-reverse bg-white py-1.5 px-3 text-black text-min rounded-2xl">
                    <span class="text-sm -right-[12px] -top-[5px] bg-sky-900 w-9 h-9 flex items-center justify-center rounded-[50%] absolute text-white font-bold">1</span>
                    <h1 class="font-bold">
                        اطلاعات حواله
                    </h1>
                </div>
                <div class="rounded-md  text-black">
                    <div class=" flex items-center justify-center text-min text-center leading-6">
                        <p>سفارش دریافت <span class="text-rose-700">1 دلار حواله</span> PM از فروشگاه هلوگیت دریافت شد</p>
                    </div>
                </div>

                <div class="rounded-md  text-black border border-black border-dashed py-2 px-4 mt-5">
                    <div class=" flex items-center justify-between text-min  leading-6">
                        <span>قیمت به تومان:</span>
                        <span>{{$inputs['amount']??''}} تومان</span>
                    </div>
                    <div class=" flex items-center justify-between text-min  leading-6">
                        <span>کارمزد حواله:</span>
                        <span>{{$inputs['Commission']??''}} تومان</span>
                    </div>
                    <div class=" flex items-center justify-between text-min  leading-6">
                        <span>جمع کل:</span>
                        <span>{{ $inputs['rial']??'0'}} تومان</span>
                    </div>
                </div>
            </div>
            <div class="w-full rounded-md bg-F5F5F5 border-black border-2  p-8 relative ">
                <div class="  border-black border absolute -right-[6px] -top-[15px] flex space-x-4 space-x-reverse bg-white py-1.5 px-3 text-black text-min rounded-2xl">
                    <span class="text-sm -right-[12px] -top-[5px] bg-sky-900 w-9 h-9 flex items-center justify-center rounded-[50%] absolute text-white font-bold">2</span>
                    <h1 class="font-bold">
                        شماره موبایل خود را وارد کنید
                    </h1>
                </div>
                <div class="rounded-md flex items-center justify-center text-black ">
                    <div class=" flex items-center justify-between text-min space-x-4 space-x-reverse">
                        <input type="text" placeholder="09000000000" class="text-center placeholder:text-center placeholder:text-gray-300 outline-none rounded-md py-2 px-4 mobile">
                        <img src="{{asset('src/images/phone_blue.svg')}}" alt="">
                    </div>
                </div>
            </div>
            <div class="w-full rounded-md bg-F5F5F5 border-black border-2  p-8 relative ">
                <div class="  border-black border absolute -right-[6px] -top-[15px] flex space-x-4 space-x-reverse bg-white py-1.5 px-3 text-black text-min rounded-2xl">
                    <span class="text-sm -right-[12px] -top-[5px] bg-sky-900 w-9 h-9 flex items-center justify-center rounded-[50%] absolute text-white font-bold">3</span>
                    <h1 class="font-bold">
                        پرداخت کنید
                    </h1>
                </div>
                <div class="rounded-md  w-full flex items-center justify-center  text-black">
                    <div class=" flex w-full items-center justify-between text-min space-x-4 space-x-reverse">

                            <form action="{{route('panel.transfer.external.post')}}" method="post" class="flex items-center justify-between space-x-reverse space-x-2 w-full">
                                @csrf
                                <input type="hidden" name="transmission" value="{{$inputs['account']??''}}">
                                <input type="hidden" name="custom_payment" value="{{$inputs['amount']??''}}">
                                <input type="hidden" name="pay_id" value="{{$inputs['pay_id']??''}}">
                                <input type="hidden" name="url_back" value="{{$inputs['url_back']??''}}">
                                <input type="hidden" id="mobile" name="mobile" >
                                <button class="px-2 py-1.5 bg-sky-600 text-white p-4 rounded-md text-center w-full submit">ادامه
                                </button>
                                <a href="{{$inputs['url_back']??'#'}}" class="px-2 py-1.5 bg-rose-600 text-white p-4 rounded-md text-center w-full">
                                    انصراف
                                </a>
                            </form>

                    </div>
                </div>
            </div>

        </div>

    </section>
@endsection
@section('script-tag')

    <script>
        $(document).ready(function(){
            $(".submit").click(function (){
                var value=$(".mobile").val();
                $('#mobile').val(value)
            })
        })
    </script>
@endsection

