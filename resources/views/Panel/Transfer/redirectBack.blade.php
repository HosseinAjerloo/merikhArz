@extends('Panel.layout.master')

@section('message-box')
    <section class="space-y-9">
        <div class="space-y-9 print">
            <div
                class=" space-x-2 space-x-reverse @if($fastPayment->financeTransaction->payment->state=='finished') bg-green-500  @else bg-rose-500 @endif text-white p-2 rounded-md font-bold flex items-center justify-center">
                @if($fastPayment->financeTransaction->payment->state!='finished')
                    <i class="fas fa-close text-white"></i>
                @endif
                <p class="text-sm sm:text-base">
                    @if($fastPayment->financeTransaction->payment->state!='finished')
                        متاسفانه پرداخت شما با خطا مواجه شده است
                    @else

                        پرداخت موفقیت آمیز بود شما میتوانید از قسمت سوابق تراکنش های خود را مشاهده کنید
                    @endif
                </p>

            </div>
            @if($fastPayment->financeTransaction->payment->state=='finished')
                <section class=" border-2 border-2-white rounded-md py-3 px-3 text-sm sm:text-base  ">
                    <section class="space-y-3">
                        <div class="flex items-center space-x-reverse space-x-1 justify-between">
                            <p>مبلغ پرداخت :</p>
                            <h1 class="font-semibold text-lg"> {{$fastPayment->amount??''}} دلار</h1>
                        </div>

                    </section>
                </section>
            @endif
            @if($fastPayment->financeTransaction->payment->state!='finished')

                <section class=" border-2 border-2-white rounded-md py-3 px-3 text-sm sm:text-base space-y-3 ">
                    <div class=" flex items-center space-x-3 space-x-reverse">
                        <div class=" flex items-center justify-start  max-w-max   rounded-md wallet ">
                            <img src="{{asset('src/images/wallet.png')}}" alt="" class="w-6 h-6 ">
                        </div>
                        <p class="text-sm">در صورت کم شدن مبلغ به کیف پول شما اضافه خواهد شد.</p>
                    </div>
                    <p>
                        لطفا چند دقیقه دیگر تلاش نمایید
                    </p>
                </section>
            @endif

        </div>

        <div class="flex items-center justify-between  ">

            <div
                class="@if($fastPayment->financeTransaction->payment->state=='finished') bg-green-500  @else bg-rose-500 @endif w-full  rounded-md font-semibold py-1 w-1/3 flex items-center justify-center cursor-pointer">
                <a href="{{$fastPayment->url_back}}" class="text-sm">بازگشت به سایت پذیرنده</a>
            </div>


        </div>

    </section>

@endsection

@section('script-tag')

    <script>
        $(".share").click(function () {
            let body = $("body").html();
            let htmlPrint = $('.print').html();
            $("body").html(htmlPrint);
            window.print()
            $("body").html(body);

        })

        function copyToClipboard(text) {

            var textArea = document.createElement("textarea");
            textArea.value = text;
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

            let spanText = $(this).siblings('span').text();
            copyToClipboard(spanText);
        });
    </script>
@endsection
