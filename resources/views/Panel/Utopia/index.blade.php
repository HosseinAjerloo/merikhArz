@extends('Panel.layout.master')


@section('container')
    <section class="rounded-lg p-2 border-4 border-sky-900/75 bg-sky-900">
        <div class="flex justify-center items-center space-x-3">
            <img src="{{asset('src/images/utopia.png')}}" alt="" class="w-14">
            <h4 class="font-semibold">ووچر یوتوپیا</h4>
        </div>
        <form id="form" action="{{route('utopia.store')}}" class="space-y-6" method="POST">
            @csrf
            <div class="flex items-center  space-x-4 space-x-reverse">
                <span class="text-md tracking-tight">مبلغ وچر:</span>
                <input name="custom_payment" value="{{old('custom_payment')}}" type="text"
                       class="custom_payment text-black outline-none border-none rounded-md py-1 w-full md:w-1/2 lg:w-1/4 px-2">
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <img src="{{asset('src/images/dollar.png')}}" class="w-8">
                <p>
                    <span class="text-white text-md">مبلغ قابل پرداخت</span>
                    <span class="text-white mr-1 live-amount"></span>
                </p>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <img src="{{asset('src/images/dollar.png')}}" class="w-8">
                <p class="text-white">
                    <span class="text-md">موجودی فعلی شما <span
                            class="text-green-600">{{numberFormat($balance/10)}}</span> تومان میباشد:</span>
                </p>
            </div>
            <div
                class="pb-2 flex justify-between flex-wrap items-center  sm:justify-start sm:space-x-reverse sm:space-x-2 ">
                @foreach($banks as $bank)
                    <label for="bank-{{$bank->id}}"
                           class="mt-2 text-[12px] sm:text-[15px] flex w-1/2	 rounded-lg shadow shadow-bluee-900 max-w-max items-center py-3 px-4 space-x-2 space-x-reverse bg-green-500">
                        {{$bank->name??''}}
                        <input type="radio" value="saman" class="hidden" name="bank" id="bank-{{$bank->id}}">
                    </label>

                @endforeach

                <button type="submit"
                    class="mt-2 text-[12px] sm:text-[15px] flex w-1/2	 rounded-lg shadow shadow-bluee-900 max-w-max items-center py-3 px-4 space-x-2 space-x-reverse bg-green-500">
                    <img src="{{asset('src/images/wallet.png')}}" class="w-6">
                    <span class="text-[15px]">پرداخت از طریق کیف پول</span>
                </button>
            </div>

        </form>

    </section>
@endsection
@section('script-tag')

    <script>
        let voucherNumber = document.querySelector('.custom_payment');
        let dollerValue = parseInt("{{$doller->DollarRateWithAddedValue()}}");
        let price = 0
        let doted = 0;
        voucherNumber.addEventListener('input', function (e) {
            if (!validation())
                return;

        })


        let form = document.querySelector('#form');
        form.addEventListener('click', function (e) {
            e.preventDefault();
            let element = e.target;
            if (element.nodeName == "LABEL" && validation()) {
                let input = element.querySelector('input');
                input.setAttribute("checked", "checked");
                form.submit()

            }
            if (element.nodeName == 'SPAN' || element.nodeName == 'IMG'  && validation()) {
                form.submit()
            }

        },true)
        let validation = () => {
            if (voucherNumber.value.includes('.') && voucherNumber.value.split('.').length > 2) {
                voucherNumber.value = '';
                showError('تایک رقم اشار مجاز است')

                return false;
            }


            if (Number(voucherNumber.value) > Number("{{env('Daily_Purchase_Limit')}}")) {
                showError("مقدار ووچر نباید بزرگ تر از {{env('Daily_Purchase_Limit')}} باشد")
                voucherNumber.value = '';
                document.querySelector('.live-amount').innerHTML = ''

                return false;
            }
            if (!voucherNumber.value) {
                showError('مقدار ووچر نمیتواند خالی باشد')
                document.querySelector('.live-amount').innerHTML = ''

                return false;

            }
            if (!/^\d+(\.\d{1,2})?$/.test(voucherNumber.value) && !voucherNumber.value.includes('.')) {
                showError('مقدار ووچر باید عددی باشد')
                document.querySelector('.live-amount').innerHTML = ''

                voucherNumber.value = '';
                return false;

            }
            if (Number(voucherNumber.value) < Number("{{env('Daily_Purchase_Limit')}}")) {

                let valueInput = parseFloat(voucherNumber.value);

                price = dollerValue * valueInput;
                price = Math.floor((price / 10000)) * 10000;
                document.querySelector('.live-amount').innerHTML = formatNumber(price) + ' ریال';
                return true;


            }
            return true
        }




    </script>
@endsection
