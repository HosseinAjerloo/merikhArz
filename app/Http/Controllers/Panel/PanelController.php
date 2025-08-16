<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Panel\Purchase\PurchaseRequest;
use App\Http\Requests\Panel\Purchase\PurchaseThroughTheBankRequest;
use App\Http\Requests\Panel\WalletCharging\WalletChargingRequest;
use App\Http\Traits\HasConfig;
use App\Jobs\SendAppAlertsJob;
use App\Models\Bank;
use App\Models\Doller;
use App\Models\FinanceTransaction;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Voucher;
use App\Notifications\IsEmptyUserInformationNotifaction;
use App\Services\BankService\Saman;
use App\Services\SmsService\SatiaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use AyubIRZ\PerfectMoneyAPI\PerfectMoneyAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use function Laravel\Prompts\alert;


class PanelController extends Controller
{
    use HasConfig;

    public function __construct()
    {
        return true;
    }

    public function index()
    {
        $user = Auth::user();
        $UserInformationStatus = $this->validationFiledUser();
        $balance = $user->getCreaditBalance();
        return view('Panel.index', compact('balance', 'UserInformationStatus'));
    }

    public function contactUs()
    {
        return view('Panel.ContactUs.ContactUs');
    }

    public function walletCharging(Request $request)
    {
        $banks = Bank::where('is_active', 1)->get();
        $user = Auth::user();
        $balance = $user->getCreaditBalance();
        $balance = numberFormat($balance);
        return view("Panel.RechargeWallet.index", compact('balance', 'banks'));
    }

    public function walletChargingPreview(WalletChargingRequest $request)
    {
        $user = Auth::user();
        $inputs = $request->all();
        $payment = Payment::create(
            [
                'state' => 'requested',
            ]);
        $balance = $user->getCreaditBalance();

        $payment->update(['order_id' => $payment->id + Payment::transactionNumber]);
        $inputs['orderID'] = $payment->id + Payment::transactionNumber;
        session()->put('payment', $payment->id);

        return view("Panel.RechargeWallet.FinalApproval", compact('inputs', 'balance'));
    }

    public function walletChargingStore(WalletChargingRequest $request)
    {

        if (session()->has('payment')) {
            $inputs = $request->all();
            $payment = Payment::find(session()->get('payment'));
            $inputs['price'] .= 0;
            $inputs['price'] = floor($inputs['price']);
            $bank = Bank::find($inputs['bank_id']);
            $user = Auth::user();
            $balance = Auth::user()->getCreaditBalance();

            $inputs['final_amount'] = $inputs['price'];
            $inputs['type'] = 'wallet';
            $inputs['status'] = 'requested';
            $inputs['bank_id'] = $bank->id;
            $inputs['user_id'] = $user->id;
            $inputs['description'] = ' افزایش مبلغ کیف پول ' . $bank->name;
            $invoice = Invoice::create($inputs);


            $objBank = new $bank->class;
            $objBank->setTotalPrice($inputs['price']);
            $objBank->setBankUrl($bank->url);

            $objBank->setOrderID($payment->id + Payment::transactionNumber);
            $objBank->setTerminalId($bank->terminal_id);
            $objBank->setUrlBack(route('panel.wallet.charging.back'));
            $objBank->setBankModel($bank);


            session()->put('payment', $payment->id);
            session()->put('invoice', $invoice->id);
            $payment->update(
                [
                    'bank_id' => $bank->id,
                    'amount' => $inputs['price'],
                    'invoice_id' => $invoice->id

                ]);
            $financeTransaction = FinanceTransaction::create([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => "bank",
                "creadit_balance" => $balance,
                'description' => " ارتباط با بانک $bank->name",
                'payment_id' => $payment->id,
            ]);
            session()->put('financeTransaction', $financeTransaction->id);

            $status = $objBank->payment();
            if (!$status) {
                $invoice->update(['status' => 'failed', 'description' => "به دلیل عدم ارتباط با بانک $bank->name شارژ کیف پول انجام نشد "]);
                $financeTransaction->update(['description' => "به دلیل عدم ارتباط با بانک $bank->name سفارش شما لغو شد ", 'status' => 'fail']);

                return redirect()->route('panel.index')->withErrors(['error' => 'ارتباط با بانک فراهم نشد لطفا چند دقیقه بعد تلاش فرماید.']);
            }
            $token = $status;
            Log::channel('bankLog')->emergency(PHP_EOL . 'Connection with the bank payment gateway to charge the wallet '
                . PHP_EOL .
                'Name of the bank: ' . $bank->name
                . PHP_EOL .
                'payment price: ' . $inputs['price']
                . PHP_EOL .
                'payment date: ' . Carbon::now()->toDateTimeString()
                . PHP_EOL .
                'user ID: ' . $user->id
                . PHP_EOL

            );


            return $objBank->connectionToBank($token);

        } else {
            return redirect()->route('panel.index')->withErrors(['error' => 'خطایی رخ داد لفا مجدد بعدا تلاش فرمایید.']);
        }
    }

    public function walletChargingBack(Request $request)
    {

        try {

            $user = Auth::user();
            $lastBalance = $user->financeTransactions()->orderBy('id', 'desc')->first();
            $inputs = $request->all();
            $payment = Payment::find(session()->get('payment'));
            $financeTransaction = FinanceTransaction::find(session()->get('financeTransaction'));
            $bank = $payment->bank;
            $objBank = new $bank->class;
            $objBank->setBankModel($bank);

            Log::channel('bankLog')->emergency(PHP_EOL . "Back from the bank and the bank's response to charging the wallet " . PHP_EOL . json_encode($request->all()) . PHP_EOL .
                'Bank message: ' . PHP_EOL . $objBank->transactionStatus() . PHP_EOL .
                'user ID :' . $user->id
                . PHP_EOL
            );
            $invoice = Invoice::find(session()->get('invoice'));
            if (!$objBank->backBank()) {
                $payment->update(
                    [
                        'RefNum' => null,
                        'ResNum' => $inputs['ResNum'],
                        'state' => 'failed'

                    ]);
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus(), 'status' => 'fail']);

                return redirect()->route('panel.index')->withErrors(['error' => ' پرداخت موفقیت آمیز نبود ' . $objBank->transactionStatus()]);
            }

            $back_price = $objBank->verify($payment->amount);

            if ($back_price !== true or Payment::where("order_id", $inputs['ResNum'])->count() > 1) {
                $invoice->update(['status' => 'failed', 'description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price)]);
                $financeTransaction->update(['description' => ' پرداخت موفقیت آمیز نبود ' . $objBank->verifyTransaction($back_price), 'status' => 'fail']);

                Log::channel('bankLog')->emergency(PHP_EOL . "Bank Credit VerifyTransaction wallet recharge  : " . json_encode($request->all()) . PHP_EOL .
                    'Bank message: ' . $objBank->verifyTransaction($back_price)
                    . PHP_EOL .
                    'user ID :' . $user->id
                    . PHP_EOL
                );
                return redirect()->route('panel.error', $payment->id);
            }
            $payment->update(
                [
                    'RefNum' => $inputs['RefNum'],
                    'ResNum' => $inputs['ResNum'],
                    'state' => 'finished'

                ]);
            $invoice->update(['status' => 'finished']);
            if ($lastBalance) {
                $amount = $payment->amount + $lastBalance->creadit_balance;
            } else {
                $amount = $payment->amount;
            }


            $financeTransaction->update([
                'user_id' => $user->id,
                'amount' => $payment->amount,
                'type' => "deposit",
                "creadit_balance" => $amount,
                'description' => 'افزایش   مبلغ کیف پول',
                'payment_id' => $payment->id

            ]);
            return redirect()->route('panel.index')->with(['success' => 'پرداخت باموفقیت انجام شد و مبلغ کیف پول شما فزایش داده شد']);
        } catch (\Exception $e) {
            Log::emergency("panel Controller :" . $e->getMessage());
            SendAppAlertsJob::dispatch('شارژکیف پول به مشکل خورده است لطفا درگاه بانکی  وسایر موارد چک شود')->onQueue('perfectmoney');
            return redirect()->route('panel.index')->withErrors(['error' => "خطایی رخ داد از صبر و شکیبایی شما مچکریم لطفا جهت پیگیری در خواست تیکت ثبت کنید"]);

        }
    }

    public function error(Request $request, Payment $payment)
    {
        $user = Auth::user();
        if ($payment->invoice->user->id == $user->id)
            return view('bank.bankErrorPage', compact('payment'));
        else
            return redirect()->route('panel.index');
    }

    public function rules()
    {
        return view('Panel.Rules.index');
    }

    public function delivery(){
        if (session()->has('voucher') && session()->get('payment_amount')) {
            $voucher = session()->get('voucher');
            $payment_amount = session()->get('payment_amount');
            return view('Panel.Delivery.index', compact('voucher', 'payment_amount'));
        } else {
            return redirect()->route('panel.index');
        }
    }


}
