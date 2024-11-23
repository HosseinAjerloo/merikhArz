<?php

namespace App\Services\BankService;

use App\Interface\BankInterface;
use App\Jobs\SendAppAlertsJob;
use App\Models\Bank;
use Illuminate\Support\Facades\Log;

class Meli extends Service
{
    /**
     * Create a new class instance.
     */

    public function __construct()
    {
    }

    public function payment()
    {
        $arrres = $this->GetToken();

        if ($arrres->ResCode == 0) {
            $token = $arrres->Token;
            return $token;
        } else {
            Log::emergency('An error occurred while connecting to National Bank :'.PHP_EOL.
            $arrres->Description);

            SendAppAlertsJob::dispatch('هنگام اتصال به بانک ملی خطایی رخ  داد و اتصال برقرار نشد')->onQueue('perfectmoney');
            return false;
        }
    }

    public function GetToken()
    {
        $this->generateData();
        return $this->cullRequest('https://sadad.shaparak.ir/vpg/api/v0/Request/PaymentRequest');
    }

    function cullRequest($url)
    {
        try {
            $str_data = json_encode($this->data);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $str_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($str_data)));
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            $result = curl_exec($curl);
            curl_close($curl);
            $arrres = json_decode($result);

            return $arrres;
        }
        catch (\Exception $e)
        {

            Log::emergency('An error occurred while connecting to National Bank '.PHP_EOL.
            $e->getMessage());
            SendAppAlertsJob::dispatch('هنگام اتصال به بانک ملی خطایی رخ  داد و نوع خطا در لاگ ذخیره شد لطفا پیگیری کنید')->onQueue('perfectmoney');

            return false;
        }

    }

    public function backBank()
    {
        $Token = request()->input("token");
        $ResCode = request()->input("ResCode");
        if (isset($Token) and isset($ResCode)) {
            return true;
        } else
            return false;
    }

    protected function generateData()
    {
        $password = $this->objectBank->password;
        $SignData = $this->encrypt_pkcs7_meli("$this->terminalId;$this->orderID;$this->totalPrice", "$password");

        $this->data = array(
            'TerminalId' => $this->getTerminalId(),
            'MerchantId' => $this->objectBank->username,
            'Amount' => $this->getTotalPrice(),
            'SignData' => $SignData,
            'ReturnUrl' => $this->getUrlBack(),
            'LocalDateTime' => date("m/d/Y g:i:s a"),
            'OrderId' => $this->getOrderID());
    }

    public function transactionStatus()
    {
        return $this->verifyTransaction($this->verify());
    }

    public function verifyTransaction($ErrorCode)
    {
        $ErrorDesc = $ErrorCode;
        if ($ErrorCode == "0")
            $ErrorDesc = "تراکنش موفق";
        else if ($ErrorCode == "1")
            $ErrorDesc = "با بانک ملی تماس حا صل نمایید";
        else if ($ErrorCode == "3")
            $ErrorDesc = "پذيرنده کارت فعال نیست لطفا با بخش امور پذيرندگان، تماس حاصل فرمائید";
        else if ($ErrorCode == "12")
            $ErrorDesc = "تراکنش معتبر نمی باشد";
        else if ($ErrorCode == "13")
            $ErrorDesc = "مبلغ تراکنش معتبر نمی باشد";
        else if ($ErrorCode == "23")
            $ErrorDesc = "پذيرنده کارت نامعتبر است لطفا با بخش امور پذيرندگان، تماس حاصل فرمائید";
        else if ($ErrorCode == "30")
            $ErrorDesc = "فرمت پيام دچار اشكال مي باشد";
        else if ($ErrorCode == "33")
            $ErrorDesc = "تاریخ استفاده کارت به پایان رسیده است";
        else if ($ErrorCode == "41")
            $ErrorDesc = "كارت مفقود مي باشد";
        else if ($ErrorCode == "43")
            $ErrorDesc = "كارت مسروقه است";
        else if ($ErrorCode == "51")
            $ErrorDesc = "موجودي حساب كافي نمي باشد";
        else if ($ErrorCode == "55")
            $ErrorDesc = "رمز وارده صحيح نمي باشد";
        else if ($ErrorCode == "56")
            $ErrorDesc = "شماره کارت یا CVV2  صحیح نمی باشد ";
        else if ($ErrorCode == "57")
            $ErrorDesc = "دارنده کارت مجاز به انجام این تراکنش نمی باشد";
        else if ($ErrorCode == "58")
            $ErrorDesc = "انجام تراکنش مربوطه توسط پايانه ی انجام دهنده مجاز نمی باشد";
        else if ($ErrorCode == "61")
            $ErrorDesc = "مبلغ تراکنش از حد مجاز بالاتر است";
        else if ($ErrorCode == "65")
            $ErrorDesc = "تعداد دفعات تراکنش از حد مجاز بیشتر است";
        else if ($ErrorCode == "75")
            $ErrorDesc = "ورود رمز دوم از حد مجاز گذشته است. رمز دوم جدید در خواست نمایید";
        else if ($ErrorCode == "79")
            $ErrorDesc = "شماره حساب نامعتبر است";
        else if ($ErrorCode == "80")
            $ErrorDesc = "تراكنش موفق عمل نكرده است";
        else if ($ErrorCode == "84")
            $ErrorDesc = "سوئيچ صادركننده فعال نيست";
        else if ($ErrorCode == "88")
            $ErrorDesc = "سيستم دچار اشكال شده است";
        else if ($ErrorCode == "90")
            $ErrorDesc = "ارتباط به طور موقت قطع می باشد";
        else if ($ErrorCode == "91")
            $ErrorDesc = "پاسخ در زمان تعیین شده بدست سیستم نرسیده است";
        else if ($ErrorCode == "-1")
            $ErrorDesc = "یکی از موارد مبلغ، شماره سفارش یا کلید اشتباه است";
        else if ($ErrorCode == "503")
            $ErrorDesc = "سفارش ثبت گردیده و منتظر پرداخت می باشد";
        else if ($ErrorCode == "1000")
            $ErrorDesc = "ترتیب پارامترهای ارسالی اشتباه می باشد، لطفا مسئول فنی پذيرنده با بانک تماس حاصل فرمايند";
        else if ($ErrorCode == "1001")
            $ErrorDesc = "لطفا مسئول فنی پذيرنده با بانک تماس حاصل فرمايند،پارامترهای پرداخت اشتباه می باشد";
        else if ($ErrorCode == "1002")
            $ErrorDesc = "خطا در سیستم - تراکنیش ناموفق";
        else if ($ErrorCode == "1003")
            $ErrorDesc = "IP پذيرنده اشتباه است.لطفا مسئول فنی پذيرنده با بانک تماس حاصل فرمايند";
        else if ($ErrorCode == "1004")
            $ErrorDesc = "لطفا مسئول فنی پذيرنده با بانک تماس حاصل فرمايند،شماره پذيرنده اشتباه است";
        else if ($ErrorCode == "1005")
            $ErrorDesc = "خطای دسترسی:لطفا بعدا تالش فرمايید";
        else if ($ErrorCode == "1006")
            $ErrorDesc = "خطا در سیستم";
        else if ($ErrorCode == "1011")
            $ErrorDesc = "درخواست تکراری- شماره سفارش تکراری می باشد";
        else if ($ErrorCode == "1012")
            $ErrorDesc = "اطالعات پذيرنده صحیح نیست،يکی از موارد تاريخ،زمان يا کلید تراکنش اشتباه است.لطفا مسئول فنی پذيرنده با بانک تماس حاصل فرمايند";
        else if ($ErrorCode == "1015")
            $ErrorDesc = "پاسخ خطای نامشخص از سمت مرکز";
        else if ($ErrorCode == "1017")
            $ErrorDesc = "مبلغ درخواستی شما جهت پرداخت از حد مجاز تعريف شده برای اين پذيرنده بیشتر است";
        else if ($ErrorCode == "1018")
            $ErrorDesc = "اشکال در تاريخ و زمان سیستم. لطفا تاريخ و زمان سرور خود را با بانک هماهنگ نمايید";
        else if ($ErrorCode == "1019")
            $ErrorDesc = "امکان پرداخت از طريق سیستم شتاب برای اين پذيرنده امکان پذير نیست";
        else if ($ErrorCode == "1020")
            $ErrorDesc = "پذيرنده غیرفعال شده است.لطفا جهت فعال سازی با بانک تماس بگیريد";
        else if ($ErrorCode == "1023")
            $ErrorDesc = "آدرس بازگشت پذيرنده نامعتبر است";
        else if ($ErrorCode == "1024")
            $ErrorDesc = "مهر زمانی پذيرنده نامعتبر است";
        else if ($ErrorCode == "1025")
            $ErrorDesc = "امضا تراکنش نامعتبر است";
        else if ($ErrorCode == "1026")
            $ErrorDesc = "شماره سفارش تراکنش نامعتبر است";
        else if ($ErrorCode == "1027")
            $ErrorDesc = "شماره پذيرنده نامعتبر است";
        else if ($ErrorCode == "1028")
            $ErrorDesc = "شماره ترمینال پذيرنده نامعتبر است";
        else if ($ErrorCode == "1029")
            $ErrorDesc = "آدرس  IP پرداخت در محدوده آدرس های معتبر اعلام شده توسط پذيرنده نیست .لطفا مسئول فنی پذيرنده با بانک تماس حاصل فرمايند";
        else if ($ErrorCode == "1030")
            $ErrorDesc = "آدرس  Domain پرداخت در محدوده آدرس های معتبر اعلام شده توسط پذيرنده نیست .لطفا مسئول فنی پذيرنده با بانک تماس حاصل فرمايند";
        else if ($ErrorCode == "1031")
            $ErrorDesc = "مهلت زمانی شما جهت پرداخت به پايان رسیده است.لطفا مجددا سعی بفرمايید";
        else if ($ErrorCode == "1032")
            $ErrorDesc = "پرداخت با اين کارت , برای پذيرنده مورد نظر شما امکان پذير نیست.لطفا از کارتهای مجاز که توسط پذيرنده معرفی شده است , استفاده نمايید";
        else if ($ErrorCode == "1033")
            $ErrorDesc = "به علت مشکل در سايت پذيرنده, پرداخت برای اين پذيرنده غیرفعال شده است.لطفا مسوول فنی سايت پذيرنده با بانک تماس حاصل فرمايند";
        else if ($ErrorCode == "1036")
            $ErrorDesc = "اطلاعات اضافی ارسال نشده يا دارای اشکال است";
        else if ($ErrorCode == "1037")
            $ErrorDesc = "شماره پذيرنده يا شماره ترمینال پذيرنده صحیح نمیباشد";
        else if ($ErrorCode == "1053")
            $ErrorDesc = "خطا: درخواست معتبر، از سمت پذيرنده صورت نگرفته است لطفا اطلاعات پذيرنده خود را چک کنید";
        else if ($ErrorCode == "1055")
            $ErrorDesc = "مقدار غیرمجاز در ورود اطلاعات";
        else if ($ErrorCode == "1056")
            $ErrorDesc = "سیستم موقتا قطع میباشد.لطفا بعدا تلاش فرمايید";
        else if ($ErrorCode == "1058")
            $ErrorDesc = "سرويس پرداخت اينترنتی خارج از سرويس می باشد.لطفا بعدا سعی بفرمايید";
        else if ($ErrorCode == "1061")
            $ErrorDesc = "اشکال در تولید کد يکتا. لطفا مرورگر خود را بسته و با اجرای مجدد مرورگر عملیات پرداخت را انجام دهید (احتمال استفاده از دکمه «Back» مرورگر)";
        else if ($ErrorCode == "1064")
            $ErrorDesc = "لطفا مجددا سعی بفرمايید";
        else if ($ErrorCode == "1065")
            $ErrorDesc = "ارتباط ناموفق .لطفا چند لحظه ديگر مجددا سعی کنید";
        else if ($ErrorCode == "1066")
            $ErrorDesc = "سیستم سرويس دهی پرداخت موقتا غیر فعال شده است";
        else if ($ErrorCode == "1068")
            $ErrorDesc = "با عرض پوزش به علت بروزرسانی , سیستم موقتا قطع میباشد";
        else if ($ErrorCode == "1072")
            $ErrorDesc = "خطا در پردازش پارامترهای اختیاری پذيرنده";
        else if ($ErrorCode == "1101")
            $ErrorDesc = "مبلغ تراکنش نامعتبر است";
        else if ($ErrorCode == "1103")
            $ErrorDesc = "توکن ارسالی نامعتبر است";
        else if ($ErrorCode == "1104")
            $ErrorDesc = "توکن ارسالی نامعتبر است";
        else if ($ErrorCode == "1105")
            $ErrorDesc = "تراکنش بازگشت داده شده است(مهلت زمانی به پايان رسیده است)";
        else if ($ErrorCode == "9005")
            $ErrorDesc = "تراکنش ناموفق ( مبلغ به حساب دارنده کارت برگشت داده شده است)";
        else if ($ErrorCode == "9006")
            $ErrorDesc = "تراکنش ناتمام ( در صورت کسرموجودی مبلغ به حساب دارنده کارت برگشت داده می شود)";
        else if ($ErrorCode == "-1111")
            $ErrorDesc = "پارامترهای ارسالی صحیح نیست و يا تراکنش در سیستم وجود ندارد";
        else if ($ErrorCode == "-2222")
            $ErrorDesc = "مهلت ارسال تراکنش به پايان رسیده Sاست";
        else if ($ErrorCode == "-3333")
            $ErrorDesc = "تراکنش توسط خریدار لغو شده است";
        else if ($ErrorCode == "1021")
            $ErrorDesc = "درگاه غیر فعال است";
        return $ErrorDesc;
    }

    protected function encrypt_pkcs7_meli($str, $key)
    {
        $key = base64_decode($key);
        $ciphertext = openssl_encrypt($str, "DES-EDE3", $key, OPENSSL_RAW_DATA);
        return base64_encode($ciphertext);
    }


    public function verify($amount=0)
    {
        $key = $this->objectBank->password;
        $ResCode = request()->input('ResCode');
        $Token = request()->input('token');

        if ($ResCode == 0) {

            $this->data = array('Token' => $Token, 'SignData' => $this->encrypt_pkcs7_meli($Token, $key));
            $arrres = $this->cullRequest('https://sadad.shaparak.ir/vpg/api/v0/Advice/Verify');
            if ($arrres->ResCode != -1 && $arrres->ResCode == 0) {
                $Refid = $arrres->SystemTraceNo;
                if ($Refid == '') {
                    return array(0, $arrres->ResCode);
                }
                $RefNo = $arrres->RetrivalRefNo;
                return array(1, 0);
            } else {
                return array(0, $arrres->ResCode);
            }
        } else {
            if ($ResCode == "-1") {
                return array(0, -3333);
            } elseif ($ResCode == 101) {
                return array(0, -2222);
            } else {
                return array(0, 503);
            }

        }
    }

    public function connectionToBank($token)
    {
        return redirect()->away($this->getBankUrl() . $token);
    }

    public function setBankModel(Bank $bank)
    {
        $this->objectBank = $bank;
    }
}
