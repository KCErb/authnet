<?php

namespace mglaman\AuthNet\Request;

/**
 * Class PostRequest
 * @package mglaman\AuthNet\Request
 */
abstract class PostRequest extends BaseRequest
{

    /**
     * @var string[]
     */
    protected $postFields = [];

    /**
     * Sets the POST fields for the request.
     */
    abstract protected function setPostFields();

    /**
     * @param $name
     * @param $value
     */
    public function setField($name, $value)
    {
        $this->postFields[$name] = $value;
    }

    /**
     * @param $name
     */
    public function unsetField($name)
    {
        unset($this->postFields[$name]);
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        foreach ($fields as $name => $value) {
            $this->setField($name, $value);
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setField($name, $value);
    }

    /**
     * @return array
     */
    protected function requestOptions()
    {
        $this->setPostFields();
        return parent::requestOptions() + [
          'form_params' => $this->postFields,
        ];
    }
}
