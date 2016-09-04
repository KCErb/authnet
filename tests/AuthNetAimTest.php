<?php

namespace mglaman\AuthNet\Tests;

use GuzzleHttp\Client;
use mglaman\AuthNet\AuthNetConfiguration;
use mglaman\AuthNet\Service\AIMRequest;
use mglaman\AuthNet\Service\AIMResponse;

/**
 * Tests the AIM Request and AIM Responses.
 *
 * @todo Test custom fields
 * @todo Test line items
 */
class AuthNetAimTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \mglaman\AuthNet\AuthNetConfiguration
     */
    protected $configuration;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->configuration = new AuthNetConfiguration([
          'api_login' => AUTHORIZENET_API_LOGIN_ID,
          'transaction_key' => AUTHORIZENET_TRANSACTION_KEY,
          'sandbox' => true,
        ]);
        $this->client = new Client();
    }

    public function testUrls()
    {
        $this->assertEquals('https://test.authorize.net/gateway/transact.dll', AIMRequest::getSandboxUrl());
        $this->assertEquals('https://secure2.authorize.net/gateway/transact.dll', AIMRequest::getLiveUrl());
    }

    public function testData()
    {
        $request = new AIMRequest($this->configuration, $this->client);
        $response = $request->authorize(rand(1, 1000), '4111111111111111', '1230');
        $data = $response->data();
        $this->assertEquals($response->transactionId, $data[6]);
    }

    public function testCapture()
    {
        $request = new AIMRequest($this->configuration, $this->client);
        $response = $request->authorize(rand(1, 1000), '4111111111111111', '1230');
        $this->assertEquals(AIMResponse::APPROVED, $response->getCode());
        $this->assertTrue($response->isApproved());
        $this->assertNotEmpty($response->authorizationCode);
        $response = $request->capture($response->authorizationCode, $response->amount, '4111111111111111', '1230');
        $this->assertEquals(AIMResponse::APPROVED, $response->getCode());
        $this->assertTrue($response->isApproved());
    }

    public function testAuthorizeAndCapture()
    {
        $request = new AIMRequest($this->configuration, $this->client);
        $response = $request->authAndCapture(rand(1, 1000), '4111111111111111', '1230');
        $this->assertEquals(AIMResponse::APPROVED, $response->getCode());
    }

    public function testAuthorizeThenCapture()
    {
        $request = new AIMRequest($this->configuration, $this->client);
        $response = $request->authorize(rand(1, 1000), '4111111111111111', '1230');
        $this->assertEquals(AIMResponse::APPROVED, $response->getCode());
        $this->assertTrue($response->isApproved());
        $this->assertNotEmpty($response->authorizationCode);

        $response = $request->priorAuthCapture($response->transactionId, $response->amount);
        $this->assertEquals(AIMResponse::APPROVED, $response->getCode());
        $this->assertTrue($response->isApproved());
    }

    public function testVoid()
    {
        $request = new AIMRequest($this->configuration, $this->client);
        $response = $request->authAndCapture(rand(1, 1000), '4111111111111111', '1230');
        $response = $request->void($response->transactionId);
        $this->assertEquals(AIMResponse::APPROVED, $response->getCode());
    }

    public function testCredit()
    {
        $request = new AIMRequest($this->configuration, $this->client);
        $response = $request->authAndCapture(rand(1, 1000), '4111111111111111', '1230');
        $response = $request->credit(
            $response->transactionId,
            $response->amount,
            substr($response->account_number, 4, 8)
        );
        // @todo We may not be able to test Credit since it needs to settle.
        // The proper workflow would be to void.
        $this->assertEquals(AIMResponse::ERROR, $response->getCode());
    }

    public function testErrorResponses()
    {
        $request = new AIMRequest($this->configuration, $this->client);

        // General error
        $request->setField('zip', '46282');
        $response = $request->authorize(rand(1, 1000), '4111111111111111', '1230');
        $this->assertEquals(AIMResponse::DECLINED, $response->getCode());
        $this->assertFailedRequest($response, 'This transaction has been declined.');
        $request->unsetField('zip');
        $response = $request->authorize(rand(1, 1000), '4111111111111111', '1230');
        $this->assertEquals(AIMResponse::APPROVED, $response->getCode());
    }

    public function testSetFields()
    {
        $request = new AIMRequest($this->configuration, $this->client);
        $request->setFields([
            'first_name' => 'Avery',
            'last_name' => 'Jonson',
            'address' => 'Appleseed Way',
            'city' => 'New York',
            'state' => 'NY',
            'zip' => '10005',
            'country' => 'US',
        ]);
        $request->setField('email', 'example@example.com');
        $response = $request->authorize(rand(1, 1000), '4111111111111111', '1230');
        $this->assertEquals('US', $response->country);
        $this->assertEquals('10005', $response->zip_code);
        $this->assertEquals('NY', $response->state);
        $this->assertEquals('New York', $response->city);
        $this->assertEquals('Appleseed Way', $response->address);
        $this->assertEquals('Jonson', $response->last_name);
        $this->assertEquals('Avery', $response->first_name);
        $this->assertEquals('example@example.com', $response->email_address);
    }

    /**
     * Helper to test failed responses.
     *
     * @param \mglaman\AuthNet\Service\AIMResponse $response
     * @param null $reasonText
     */
    protected function assertFailedRequest(AIMResponse $response, $reasonText = null)
    {
        $this->assertFalse($response->isApproved());
        $this->assertTrue($response->isDeclined());
        if ($reasonText) {
            $this->assertEquals($reasonText, $response->responseReasonText);
        }
    }
}