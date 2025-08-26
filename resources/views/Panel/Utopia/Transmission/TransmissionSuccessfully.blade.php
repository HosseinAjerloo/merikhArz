@extends('Panel.layout.master')
@section('content')
    <section class="errors">

    </section>
    <section class="container mx-auto w-full md:w-2/3 lg:w-3/4 border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <div class="flex items-center px-.5 space-x-2 space-x-reverse ">
                <img src="{{asset('src/images/checked.svg')}}" alt="" class="w-5 h-5">
                <h1 class="text-sm font-bold">
                    خرید با موفقیت انجام شد
                </h1>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">

                <p class="text-mini-base font-bold flex ">
                    <span style="--i:7;" class="animation flex items-center justify-center text-base-font-color">x</span>
                    <span style="--i:6;" class="animation flex items-center justify-center">e</span>
                    <span style="--i:5;" class="animation flex items-center justify-center">a</span>
                    <span style="--i:4;" class="animation flex items-center justify-center">n</span>
                    <span style="--i:3;" class="animation flex items-center justify-center">i</span>
                    <span style="--i:2;" class="animation flex items-center justify-center">a</span>
                    <span style="--i:1;" class="animation flex items-center justify-center">S</span>
                </p>
                <img src="{{asset('src/images/utopia.png')}}" alt="" class="w-6 h-6">
            </div>

        </header>

        <article class="flex flex-col justify-center space-y-3 p-2 ">
            <div class="flex items-center flex-wrap">
                <p class="w-24 text-mini-base mb-2 md:mb-0">کد hash :</p>
                <div class="flex  items-center space-x-reverse space-x-2">
                    <img src="{{asset('src/images/copy.svg')}}" alt="" class="w-4 h-4 bg-white copy cursor-pointer">
                    <p class="flex items-center justify-center text-mini-base  w-full  break-all leading-6">
                        {{$transitionDelivery->payment_batch_num??''}}
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">تاریخ :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{\Illuminate\Support\Carbon::make($transitionDelivery->created_at)->format('H:i:s Y/m/d')}}

                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">شماره خرید :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{$transitionDelivery->finance_id??''}}
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-36 text-mini-base">مبلغ حواله :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{$transitionDelivery->payment_amount??''}}
                    </p>
                </div>
            </div>
            <div class="flex items-center flex-wrap">
                <p class="w-36 mb-2 md:mb-0 text-mini-base leading-6">آدرس حساب مقصد :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base break-all leading-6">
                        {{$transitionDelivery->payee_account}}
                    </p>
                </div>
            </div>
        </article>
        <div class="flex justify-center items-center py-3">
            <a href="{{route('panel.index')}}" class="bg-sky-900 flex items-center text-mini-mini-base px-4 py-2.5 text-white rounded-lg">
                بازگشت به پنل کاربری

            </a>
        </div>
    </section>


@endsection
@section('script-tag')

    <script>
        function copyToClipboard(text) {

            var textArea = document.createElement("textarea");
            textArea.value = text.trim()
            document.body.appendChild(textArea);
            textArea.select();

            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy', err);
            }
            document.body.removeChild(textArea);
        }

        $('.copy').click(function () {
            let spanText = $(this).siblings('p').text();
            copyToClipboard(spanText);
        });
    </script>
@endsection
