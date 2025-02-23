@extends('Panel.layout.master')
@section('header-tag')
    <style>
        button:disabled {
            background-color: #6398b4;
        }

        #vpn_alert {
            animation: blink-animation 1s infinite;
            -webkit-animation: blink-animation 1s infinite;
        }

        @keyframes blink-animation {
            from {
                opacity: 0.5;
            }
            to {
                opacity: 1;
            }
        }

        @-webkit-keyframes blink-animation {
            from {
                opacity: 0.5;
            }
            to {
                opacity: 1;
            }
        }
    </style>
@endsection
@section('container')
    <section class="w-full flex items-center justify-center">
        <div
            class="w-full sm:w-4/5 md:w-3/5 lg:w-1/2 xl:w-1/3 flex items-center flex-col justify-center  p-8 rounded-lg space-y-8 bg-F5F5F5">

            <div class="w-full rounded-md bg-F5F5F5 border-black border-2  p-8 relative ">
                <div
                    class="  border-black border absolute -right-[6px] -top-[15px] flex space-x-4 space-x-reverse bg-white py-1.5 px-3 text-black text-min rounded-2xl">
                    <span
                        class="text-sm -right-[12px] -top-[5px] bg-sky-900 w-9 h-9 flex items-center justify-center rounded-[50%] absolute text-white font-bold">1</span>
                    <h1 class="font-bold">
                        اطلاعات حواله
                    </h1>
                </div>
                <div class="rounded-md  text-black">
                    <div class=" flex items-center justify-center text-min text-center leading-6">
                        <p>حواله <span class="text-rose-700">{{$inputs['amount']??''}} دلاری </span>به شماره حساب پرفکت
                            مانی <span class="text-rose-700">{{$inputs['account'] ?? ''}}</span></p>
                    </div>
                </div>

                <div class="rounded-md  text-black border border-black border-dashed py-2 px-4 mt-5">
                    <div class=" flex items-center justify-between text-min  leading-6">
                        <span>جمع کل:</span>
                        <span>{{ $inputs['rial']??'0'}} تومان</span>
                    </div>
                </div>
            </div>
            <div class="w-full rounded-md bg-F5F5F5 border-black border-2  p-8 relative ">
                <div
                    class="  border-black border absolute -right-[6px] -top-[15px] flex space-x-4 space-x-reverse bg-white py-1.5 px-3 text-black text-min rounded-2xl">
                    <span
                        class="text-sm -right-[12px] -top-[5px] bg-sky-900 w-9 h-9 flex items-center justify-center rounded-[50%] absolute text-white font-bold">2</span>
                    <h1 class="font-bold">
                        شماره موبایل
                    </h1>
                </div>
                @if(auth()->check())
                    <div>
                        <div class="flex items-center justify-center">
                            <img width="32px" alt="login success" src="{{asset('src/images/Group 414.png')}}">
                            <p class="text-black text-xs p-2">شما با شماره <span
                                    class="text-rose-700 mx-2">{{auth()->user()?->mobile}}</span>وارد شده اید</p>
                        </div>
                        <div class="flex items-center justify-center text-min">
                            <button id="transfer_logout" type="button"
                                    class="p-2 bg-rose-400 text-black rounded-md text-center">خروج
                            </button>
                        </div>
                    </div>
                @else
                    <div id='mobile_input'>
                        <p class="text-black text-xs p-2">شماره موبایل خود را برای پیگیری سفارش وارد نمایید</p>
                        <div
                            class="flex items-center justify-center text-black border border-black border-dashed p-2 rounded-md">
                            <div class="flex items-center justify-between text-min space-x-4 space-x-reverse">
                                <button id="submit_mobile" type="button"
                                        class="p-2 bg-sky-600 text-white rounded-md text-center w-full text-nowrap">
                                    ثبت شماره
                                </button>
                                <input dir="ltr" id="mobile_number" type="text" placeholder="09000000000"
                                       class="text-center placeholder:text-center placeholder:text-gray-300 outline-none rounded-md p-2 w-full mobile">
                                <img src="{{asset('src/images/phone_blue.svg')}}" alt="">
                            </div>
                        </div>
                    </div>
                    <div id="verification_input" class="hidden">
                        <p class="text-black text-xs p-2">کد پیامک شده را وارد نمایید</p>
                        <div
                            class="flex items-center justify-center text-black border border-black border-dashed p-2 rounded-md">
                            <div class="flex items-center justify-between text-min space-x-4 space-x-reverse">
                                <input dir="ltr" id="verification_code" type="text" placeholder="کد تایید"
                                       autocomplete="off" maxlength="5" size="5"
                                       class="text-center placeholder:text-center placeholder:text-gray-300 outline-none rounded-md py-2 px-4 w-full mobile">
                                <button type="button" id="change_mobile"
                                        class="p-2 bg-sky-600 text-white rounded-md text-center w-full text-nowrap">
                                    تغییر شماره
                                </button>
                            </div>
                        </div>
                    </div>
                    <p id="mobile_error" class="m-2 text-red-600 text-center"></p>
                @endif
            </div>
            <div class="w-full rounded-md bg-F5F5F5 border-black border-2  p-8 relative ">
                <div
                    class="  border-black border absolute -right-[6px] -top-[15px] flex space-x-4 space-x-reverse bg-white py-1.5 px-3 text-black text-min rounded-2xl">
                    <span
                        class="text-sm -right-[12px] -top-[5px] bg-sky-900 w-9 h-9 flex items-center justify-center rounded-[50%] absolute text-white font-bold">3</span>
                    <h1 class="font-bold">
                        پرداخت کنید
                    </h1>
                </div>
                <p id="vpn_alert" class="text-red-600 text-center m-2">حتما VPN خود را خاموش کنید</p>
                <div class="rounded-md  w-full flex items-center justify-center  text-black">
                    <div class=" flex w-full items-center justify-between text-min space-x-4 space-x-reverse">
                        <form action="{{route('panel.transferFromThePaymentGateway')}}" method="post"
                              class="flex items-center justify-between space-x-reverse space-x-2 w-full">
                            @csrf
                            <input id="verification_token" type="hidden" name="verification_token" value="">
                            <input type="hidden" name="transfer_type" value="fast_payment">
                            <input type="hidden" name="bank" value="{{$bank->id}}">
                            <input type="hidden" name="transmission" value="{{$inputs['account']??''}}">
                            <input type="hidden" name="custom_payment" value="{{$inputs['amount']??''}}">
                            <input type="hidden" id="mobile" name="mobile" value="{{auth()->user()?->mobile}}">
                            <button
                                class="px-2 py-1.5 bg-sky-600 text-white p-4 rounded-md text-center w-full submit">
                                پرداخت از درگاه بانکی
                            </button>
                            <a href="{{route('panel.index')}}"
                               class="px-2 py-1.5 bg-rose-600 text-white p-4 rounded-md text-center w-full">
                                انصراف
                            </a>
                        </form>
                    </div>
                </div>
                <p class="m-2 text-red-600 text-min submit-error"></p>
            </div>

        </div>

    </section>
@endsection
@section('script-tag')

    <script>
        const is_login = '{{auth()->check()}}';
        const submit_error = $('.submit-error');
        submit_error.empty();
        $(document).ready(function () {
            $(".submit").click(function (e) {
                if (is_login == '') {
                    submit_error.html('لطفا ابتدا شماره موبایل را وارد کرده و ثبت نمایید.');
                    e.preventDefault();
                }
            })
        })

        $('#change_mobile').on('click', function () {
            $('#verification_input').fadeOut(500);
            setTimeout(function () {
                $('#mobile_input').fadeIn(500);
            }, 500);
        });

        $('#submit_mobile').on('click', function (e) {
            const submit_mobile_element = $(this);
            const mobile_error = $('#mobile_error')
            mobile_error.empty();
            const mobile = $('#mobile_number').val();
            if (mobile.length !== 11 || !mobile.match("^09")) {
                mobile_error.html('شماره موبایل بدرستی وارد نشده است!');
                return;
            }
            const data_ = {
                "mobile": mobile
            }
            submit_mobile_element.prop('disabled', true);
            $.ajax({
                type: "post",
                url: "{{ route('transfer.mobile-submit') }}",
                headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'},
                data: data_,
                success: function (response) {
                    if (response.success == false)
                        mobile_error.html(response.message);
                    else {
                        $('#verification_token').val(response.token);
                        $('#mobile_input').fadeOut(500);
                        setTimeout(function () {
                            $('#verification_input').fadeIn(500);
                        }, 500);
                    }
                },
                complete: function () {
                    submit_mobile_element.prop('disabled', false);
                }
            });
        });

        $('#verification_code').on('input', function (e) {
            const verification_code_element = $(this);
            const verify_code = $(this).val();
            if (verify_code.length != 5)
                return;
            const token = $('#verification_token').val();
            const mobile_error = $('#mobile_error')
            mobile_error.empty();
            verification_code_element.prop('disabled', true);
            const data_ = {
                "token": token,
                'code': verify_code
            }
            $.ajax({
                type: "post",
                url: "{{ route('transfer.verification-code-submit') }}",
                headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'},
                data: data_,
                success: function (response) {
                    if (response.success == false)
                        mobile_error.html(response.message);
                    else
                        window.location.reload();
                },
                complete: function () {
                    verification_code_element.prop('disabled', false);
                }
            });
        });

        $('#transfer_logout').on('click', function () {
            $.ajax({
                type: "post",
                url: "{{route('transfer.logout')}}",
                headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'},
                data: {},
                success: function (response) {
                    if (response.success == false)
                        mobile_error.html(response.message);
                    else
                        window.location.reload();
                }
            });
        });

        @if(auth()->check() && auth()->user()?->id == 1177)
        if ('OTPCredential' in window) {
            window.addEventListener('DOMContentLoaded', e => {
                const ac = new AbortController();
                navigator.credentials.get({
                    otp: {transport: ['sms']},
                    signal: ac.signal
                }).then(otp => {
                    alert(otp.code)
                }).catch(err => {
                    alert(err)
                });
            })
        } else {
            alert('WebOTP not supported!.')
        }
        @endif
    </script>
@endsection

