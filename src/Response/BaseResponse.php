<?php

namespace mglaman\AuthNet\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseResponse
 * @package mglaman\AuthNet\Response
 */
abstract class BaseResponse
{
    const APPROVED = 1;
    const DECLINED = 2;
    const ERROR = 3;
    const HELD = 4;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    public $approved;
    public $declined;
    public $error;
    public $held;
    public $responseCode;
    public $responseSubcode;
    public $responseReasonCode;
    public $responseReasonText;
    public $authorizationCode;
    public $avsResponse;
    public $transactionId;
    public $invoiceNumber;
    public $description;
    public $amount;
    public $method;
    public $transaction_type;
    public $customer_id;
    public $first_name;
    public $last_name;
    public $company;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $country;
    public $phone;
    public $fax;
    public $email_address;
    public $ship_to_first_name;
    public $ship_to_last_name;
    public $ship_to_company;
    public $ship_to_address;
    public $ship_to_city;
    public $ship_to_state;
    public $ship_to_zip_code;
    public $ship_to_country;
    public $tax;
    public $duty;
    public $freight;
    public $tax_exempt;
    public $purchase_order_number;
    public $md5_hash;
    public $card_code_response;
    public $cavv_response;
    public $account_number;
    public $card_type;
    public $split_tender_id;
    public $requested_amount;
    public $balance_on_card;

    /**
     * BaseResponse constructor.
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the response raw data.
     *
     * @return mixed
     */
    public function data()
    {
        return $this->response->getBody()->getContents();
    }

    public function getCode()
    {
        return $this->responseCode;
    }

    public function isApproved()
    {
        return $this->approved;
    }

    public function isDeclined()
    {
        return $this->declined;
    }
}
