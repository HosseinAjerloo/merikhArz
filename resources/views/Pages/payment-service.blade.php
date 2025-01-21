@extends('Auth.Layout.master')
@section('message-box')

@endsection

@section('action')
    <article class="py-5 px-3 flex flex-col items-center justify-center">
        <div class="space-y-2">
            <h1 class="text-center font-semibold">
                درگاه رمز ارز آسان
            </h1>
            <p class="text-center text-sm sm:text-base">
                برای استفاده از این درگاه باید طبق دستور زیر عمل نمایید :
            </p>
            <ul class="list-decimal">
                <li>وارد پنل شده و <a href="{{route('panel.payment-service-register')}}" class="bg-sky-500 border rounded-2xl shadow-purple-950 p-1">ثبت نام</a> فروشگاه برای درگاه رمز ارزی را انجام بدهید</li>
                <li>
                    <p>طبق ساختار زیر درخواست خود را به ساینا ارز ارسال نمایید </p>
                    <p>https://sainaex.ir/transfer?amount=<span class="text-purple-950">amount</span>&pay_id=<span class="text-purple-950">pay_id</span>
                        &url_back=<span class="text-purple-950">url_back</span>&account=<span class="text-purple-950">account</span></p>
                    <p><span class="text-purple-950">amount</span> : مبلغ دلاری ووچر</p>
                    <p><span class="text-purple-950">pay_id</span> : شماره پرداخت که باید یکتا باشد.</p>
                    <p><span class="text-purple-950">url_back</span> : آدرسی که پس از پرداخت به آن فرستاده می شود</p>
                    <p><span class="text-purple-950">account</span> : اکانتی که مبلغ به آن واریز می شود</p>
                </li>
                <li>
                    <p>پس از پرداخت کاربر یه <span class="text-purple-950">url_back</span> منتقل می شود</p>
                </li>
                <li>
                    <p>پس از انتقال به سایت پذیرنده شما می توانید طبق API زیر استعلام پرداخت خود را دریافت نمایید</p>
                    <p>https://sainaex.ir/api/verify-fastPayment</p>
                    <p>در خواست به آدرس بالا و متود <span class="text-purple-950">POST</span> ارسال شود</p>
                    <p>در قسمت header توکنی که بعد از ثبت نام دریافت نموده اید را با نام <span class="text-purple-950">token</span> و شماره پرداختی که می خواهید استعلام بگیرید را با نام
                        <span class="text-purple-950">pay_id</span> در دیتای ارسالی، بفرستید</p>
                </li>
                <li>
                    <p>در جواب json دریافت خواهید کرد که شامل <span class="text-purple-950">success</span> و <span class="text-purple-950">amount</span> می باشد.</p>
                    <p><span class="text-purple-950">success : True</span> : که به معنی تایید و <span class="text-purple-950">success : False</span> عدم تایید پرداخت می باشد.</p>
                    <p><span class="text-purple-950">amount</span> : مبلغ تایید شده پرداخت پس از کسر کارمزد می باشد.</p>
                </li>
            </ul>
        </div>

        <div class="mt-3.5 sm:mt-8">
            <a href="{{route('panel.index')}}"
               class="text-base font-semibold bg-sky-500 px-6 py-1.5 rounded-md font-yekan">صفحه اصلی ساینا ارز</a>
        </div>

    </article>
@endsection
