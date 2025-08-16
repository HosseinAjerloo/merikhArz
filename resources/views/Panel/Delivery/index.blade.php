@extends('Panel.layout.master')
@section('content')

    <section class="container mx-auto w-full md:w-1/2 lg:w-1/3 border-2 border-black/15 rounded-lg mt-4">
        <header class="flex items-center justify-between h-10 bg-DFEDFF rounded-lg space-x-2 space-x-reverse p-1.5">
            <div class="flex items-center px-.5 space-x-2 space-x-reverse ">
                <img src="{{asset('src/images/checked.svg')}}" alt="" class="w-5 h-5">
                <h1 class="text-sm font-bold">
                    خرید با موفقیت انجام شد
                </h1>
            </div>
            <div class="flex items-center space-x-reverse space-x-2">

                <p class="text-mini-base font-bold flex ">
                    <span style="--i:9;" class="animation flex items-center justify-center text-base-font-color">z</span>
                    <span style="--i:8;" class="animation flex items-center justify-center text-base-font-color">r</span>
                    <span style="--i:7;" class="animation flex items-center justify-center text-base-font-color">a</span>
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
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">کد ووچر :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <img src="{{asset('src/images/copy.svg')}}" alt="" class="w-4 h-4 bg-white cursor-pointer copy">
                    <p class="flex items-center justify-center text-mini-base">{{$voucher->code??''}}</p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">تاریخ :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{\Illuminate\Support\Carbon::make($voucher->created_at)->format('H:i:s Y/m/d')}}
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">شماره خرید :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{$voucher->financeTransaction->id??''}}
                    </p>
                </div>
            </div>
            <div class="flex items-center ">
                <p class="w-24 text-mini-base">مبلغ حواله :</p>
                <div class="flex items-center space-x-reverse space-x-2">
                    <p class="flex items-center justify-center text-mini-base">
                        {{$payment_amount??''}}
                    </p>
                </div>
            </div>

        </article>
        <div class="flex justify-center items-center py-3">
            <a href="{{route('panel.index')}}" class="bg-sky-900  flex items-center text-mini-mini-base px-4 py-2.5 text-white rounded-lg">
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
