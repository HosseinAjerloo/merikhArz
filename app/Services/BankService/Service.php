<?php

namespace App\Services\BankService;

abstract class Service
{
    protected $totalPrice;
    protected $urlBack;
    protected $bannkUrl;
    protected $action;
    protected $terminalId;
    protected $orderID;
    protected $data = [];


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
    abstract public function backBank();
    abstract protected function generateData();
    abstract public function samanTransactionStatus($ErrorCode);
    abstract public function samanVerifyTransaction($ErrorCode);
}
