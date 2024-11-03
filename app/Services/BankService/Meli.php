<?php

namespace App\Services\BankService;

use App\Interface\BankInterface;
use App\Models\Bank;

class Meli extends Service
{
    /**
     * Create a new class instance.
     */
    private $meli = null;

    public function __construct()
    {
    }

    public function payment()
    {
        $arrres=$this->GetToken();
        dd($arrres);
        if ($arrres->ResCode == 0) {
            $Token = $arrres->Token;
            $url = $this->getBankUrl()."?Token=$Token";
            header("Location:$url");
        } else {
            //            return array(0, $arrres->Description);
            $payam = "خطا در پرداخت : " . $arrres->Description . " . شماره خريد جهت پيگيري : " . $OrderId;
            $_SESSION['display_message'] = $payam;
            $_SESSION['display_message_type'] = "error";
            header('Location:' . $ReturnUrl);
            die();
        }
    }

    public function GetToken()
    {
        $this->generateData();
        $str_data = json_encode($this->data);
        $curl = curl_init('https://sadad.shaparak.ir/vpg/api/v0/Request/PaymentRequest');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $str_data );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($str_data)));
        curl_setopt($curl,CURLOPT_TIMEOUT,30);
        $result = curl_exec($curl);
        curl_close($curl);
        $arrres = json_decode($result);

        return $arrres;


    }

    public function backBank()
    {
        // TODO: Implement backBank() method.
    }

    protected function generateData()
    {
        $this->findModel();
        $password = $this->meli->password;
        $SignData = $this->encrypt_pkcs7_meli("$this->terminalId;$this->orderID;$this->totalPrice", "$password");

        $this->data = array(
            'TerminalId' => $this->getTerminalId(),
            'MerchantId' => $this->meli->username,
            'Amount' => $this->getTotalPrice(),
            'SignData' => $SignData,
            'ReturnUrl' => $this->getUrlBack(),
            'LocalDateTime' => date("m/d/Y g:i:s a"),
            'OrderId' => $this->getOrderID());
    }

    public function samanTransactionStatus($ErrorCode)
    {
        // TODO: Implement samanTransactionStatus() method.
    }

    public function samanVerifyTransaction($ErrorCode)
    {
        // TODO: Implement samanVerifyTransaction() method.
    }

    protected function encrypt_pkcs7_meli($str, $key)
    {
        $key = base64_decode($key);
        $ciphertext = openssl_encrypt($str, "DES-EDE3", $key, OPENSSL_RAW_DATA);
        return base64_encode($ciphertext);
    }

    private function findModel()
    {
        $this->meli = Bank::where('terminal_id', $this->getTerminalId())->where('is_active', 1)->first();
    }
}
