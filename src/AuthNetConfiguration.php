<?php

namespace mglaman\AuthNet;

class AuthNetConfiguration
{
    private $apiLogin;
    private $transactionKey;
    private $sandbox;

    public function __construct(array $config)
    {
        // @todo validate and throw invalid argument exception if missing.
        $this->apiLogin = $config['api_login'];
        $this->transactionKey = $config['transaction_key'];
        $this->sandbox = $config['sandbox'];
    }

    /**
     * @return mixed
     */
    public function getApiLogin()
    {
        return $this->apiLogin;
    }

    /**
     * @param mixed $apiLogin
     * @return AuthNetConfiguration
     */
    public function setApiLogin($apiLogin)
    {
        $this->apiLogin = $apiLogin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTransactionKey()
    {
        return $this->transactionKey;
    }

    /**
     * @param mixed $transactionKey
     * @return AuthNetConfiguration
     */
    public function setTransactionKey($transactionKey)
    {
        $this->transactionKey = $transactionKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSandbox()
    {
        return $this->sandbox;
    }

    /**
     * @param mixed $sandbox
     * @return AuthNetConfiguration
     */
    public function setSandbox($sandbox)
    {
        $this->sandbox = $sandbox;
        return $this;
    }
}
