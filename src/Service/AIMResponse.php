<?php

namespace mglaman\AuthNet\Service;

use mglaman\AuthNet\Response\BaseResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Response class for AIM requests
 *
 * @author Matt Glaman <nmd.matt@gmail.com>
 */
class AIMResponse extends BaseResponse
{
    /**
     * @var string
     */
    protected $delimiter;
    /**
     * @var string
     */
    protected $encapChar;
    /**
     * @var array
     */
    protected $customFields = [];
    /**
     * @var array
     */
    protected $data;

    /**
     * AIMResponse constructor.
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string $delimiter
     * @param string $encap_char
     * @param array $custom_fields
     */
    public function __construct(ResponseInterface $response, $delimiter = ',', $encap_char = '|', $custom_fields = [])
    {
        parent::__construct($response);
        $this->delimiter = $delimiter;
        $this->encapChar = $encap_char;
        $this->customFields = $custom_fields;

        $data = explode(
            $this->encapChar.$this->delimiter.$this->encapChar,
            substr($this->response->getBody()->getContents(), 1, -1)
        );
        $this->data = $data;

        // Set all fields
        $this->responseCode = $data[0];
        $this->responseSubcode = $data[1];
        $this->responseReasonCode = $data[2];
        $this->responseReasonText = $data[3];
        $this->authorizationCode = $data[4];
        $this->avsResponse = $data[5];
        $this->transactionId = $data[6];
        $this->invoiceNumber = $data[7];
        $this->description = $data[8];
        $this->amount = $data[9];
        $this->method = $data[10];
        $this->transaction_type = $data[11];
        $this->customer_id = $data[12];
        $this->first_name = $data[13];
        $this->last_name = $data[14];
        $this->company = $data[15];
        $this->address = $data[16];
        $this->city = $data[17];
        $this->state = $data[18];
        $this->zip_code = $data[19];
        $this->country = $data[20];
        $this->phone = $data[21];
        $this->fax = $data[22];
        $this->email_address = $data[23];
        $this->ship_to_first_name = $data[24];
        $this->ship_to_last_name = $data[25];
        $this->ship_to_company = $data[26];
        $this->ship_to_address = $data[27];
        $this->ship_to_city = $data[28];
        $this->ship_to_state = $data[29];
        $this->ship_to_zip_code = $data[30];
        $this->ship_to_country = $data[31];
        $this->tax = $data[32];
        $this->duty = $data[33];
        $this->freight = $data[34];
        $this->tax_exempt = $data[35];
        $this->purchase_order_number = $data[36];
        $this->md5_hash = $data[37];
        $this->card_code_response = $data[38];
        $this->cavv_response = $data[39];
        $this->account_number = $data[50];
        $this->card_type = $data[51];
        $this->split_tender_id = $data[52];
        $this->requested_amount = $data[53];
        $this->balance_on_card = $data[54];

        $this->approved = ($this->responseCode == self::APPROVED);
        $this->declined = ($this->responseCode == self::DECLINED);
        $this->error = ($this->responseCode == self::ERROR);
        $this->held = ($this->responseCode == self::HELD);
    }

    /**
     * {@inheritDoc}
     */
    public function data()
    {
        return $this->data;
    }
}
