<?php

namespace App\Services\BankService;

use App\Models\Bank;

abstract class Service
{
    protected $totalPrice;
    protected $urlBack;
    protected $bannkUrl;
    protected $action;
    protected $terminalId;
    protected $orderID;
    protected $data = [];
    protected  $objectBank = null;



    public function setTotalPrice($price)
    {
        $this->totalPrice = $price;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function setBankUrl($url)
    {
        $this->bannkUrl = $url;
    }

    public function getBankUrl()
    {
        return $this->bannkUrl;
    }

    public function setTerminalId($terminalId)
    {
        $this->terminalId = $terminalId;
    }

    public function getTerminalId()
    {
        return $this->terminalId;
    }


    public function setUrlBack($urlBack)
    {
        $this->urlBack = $urlBack;
    }

    public function getUrlBack()
    {
        return $this->urlBack;
    }

    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;
    }

    public function getOrderID()
    {
        return $this->orderID;
    }
    abstract public function payment();

    abstract public function GetToken();
    abstract function cullRequest($url);
    abstract public function backBank();
    abstract protected function generateData();
    abstract public function transactionStatus();
    abstract public function verifyTransaction($ErrorCode);
    abstract public function verify($amount=0);
    abstract public function connectionToBank($token);
    abstract public function setBankModel(Bank $bank);
}
