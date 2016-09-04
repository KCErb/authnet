<?php

namespace mglaman\AuthNet\Service;

use mglaman\AuthNet\Exception\AuthNetException;
use mglaman\AuthNet\Request\PostRequest;

/**
 * Class AIM
 * @package mglaman\AuthNet\Service
 */
class AIMRequest extends PostRequest
{
    /**
     * @var string[]
     */
    protected $postFields = [
      'x_version' => '3.1',
      'x_delim_char' => ',',
      'x_delim_data' => 'TRUE',
      'x_relay_response' => 'FALSE',
      'x_encap_char' => '|',
    ];

    /**
     * @var array
     */
    protected $additionalLineItems = [];
    /**
     * @var array
     */
    protected $customFields = [];

    /**
     * @var array
     */
    protected $possibleAIMFields = [
      'address', 'allow_partial_auth', 'amount', 'auth_code',
      'authentication_indicator', 'bank_aba_code','bank_acct_name',
      "bank_acct_num","bank_acct_type","bank_check_number","bank_name",
      "card_code","card_num","cardholder_authentication_value","city","company",
      "country","cust_id","customer_ip","delim_char","delim_data","description",
      "duplicate_window","duty","echeck_type",'email',"email_customer",
      "encap_char","exp_date","fax","first_name","footer_email_receipt",
      "freight","header_email_receipt","invoice_num","last_name","line_item",
      "login","method","phone","po_num","recurring_billing","relay_response",
      "ship_to_address","ship_to_city","ship_to_company","ship_to_country",
      "ship_to_first_name","ship_to_last_name","ship_to_state","ship_to_zip",
      "split_tender_id","state","tax","tax_exempt","test_request","tran_key",
      "trans_id","type","version","zip"
    ];

    /**
     *
     */
    protected function setPostFields()
    {
        $this->postFields['x_login'] = $this->configuration->getApiLogin();
        $this->postFields['x_tran_key'] = $this->configuration->getTransactionKey();
        // @todo insert custom fields and line items.
    }

    /**
     * @return string
     */
    public static function getSandboxUrl()
    {
        return 'https://test.authorize.net/gateway/transact.dll';
    }

    /**
     * @return string
     */
    public static function getLiveUrl()
    {
        return 'https://secure2.authorize.net/gateway/transact.dll';
    }

    /**
     * @param $amount
     * @param $cardNum
     * @param $expDate
     * @return \mglaman\AuthNet\Service\AIMResponse
     */
    public function authAndCapture($amount, $cardNum, $expDate, $cvv = null)
    {
        $this->setField('amount', $amount);
        $this->setField('card_num', $cardNum);
        // @todo regex or something to validate.
        $this->setField('exp_date', $expDate);
        if ($cvv) {
            $this->setField('card_code', $cvv);
        }
        $this->setField('type', 'AUTH_CAPTURE');
        return new AIMResponse($this->sendRequest());
    }

    /**
     * @param $transactionId
     * @param $amount
     * @return \mglaman\AuthNet\Service\AIMResponse
     */
    public function priorAuthCapture($transactionId, $amount)
    {
        $this->setField('trans_id', $transactionId);
        $this->setField('amount', $amount);
        $this->setField('type', 'PRIOR_AUTH_CAPTURE');
        return new AIMResponse($this->sendRequest());
    }

    /**
     * @param $amount
     * @param $cardNum
     * @param $expDate
     * @param null $cvv
     * @return \mglaman\AuthNet\Service\AIMResponse
     */
    public function authorize($amount, $cardNum, $expDate, $cvv = null)
    {
        $this->setField('amount', $amount);
        $this->setField('card_num', $cardNum);
        // @todo regex or something to validate.
        $this->setField('exp_date', $expDate);
        if ($cvv) {
            $this->setField('card_code', $cvv);
        }
        $this->setField('type', 'AUTH_ONLY');
        return new AIMResponse($this->sendRequest());
    }

    /**
     * @param $authCode
     * @param $amount
     * @param $cardNum
     * @param $expDate
     * @return \mglaman\AuthNet\Service\AIMResponse
     */
    public function capture($authCode, $amount, $cardNum, $expDate)
    {
        $this->setField('auth_code', $authCode);
        $this->setField('amount', $amount);
        $this->setField('card_num', $cardNum);
        // @todo regex or something to validate.
        $this->setField('exp_date', $expDate);
        $this->setField('type', 'CAPTURE_ONLY');
        return new AIMResponse($this->sendRequest());
    }

    /**
     * @param $transactionId
     * @return \mglaman\AuthNet\Service\AIMResponse
     */
    public function void($transactionId)
    {
        $this->setField('trans_id', $transactionId);
        $this->setField('type', 'VOID');
        return new AIMResponse($this->sendRequest());
    }

    /**
     * @param $transactionId
     * @param $amount
     * @param $cardNum
     * @return \mglaman\AuthNet\Service\AIMResponse
     */
    public function credit($transactionId, $amount, $cardNum)
    {
        $this->setField('trans_id', $transactionId);
        $this->setField('amount', $amount);
        $this->setField('card_num', $cardNum);
        $this->setField('type', 'CREDIT');
        return new AIMResponse($this->sendRequest());
    }

    /**
     * @param $name
     * @param $value
     * @throws \mglaman\AuthNet\Exception\AuthNetException
     */
    public function setField($name, $value)
    {
        if ($this->possibleAIMFields && !in_array($name, $this->possibleAIMFields)) {
            throw new AuthNetException("Invalid AIM API field: $name");
        }

        parent::setField('x_' . $name, $value);
    }

    /**
     * @param $name
     */
    public function unsetField($name)
    {
        unset($this->postFields['x_' . $name]);
    }
}
