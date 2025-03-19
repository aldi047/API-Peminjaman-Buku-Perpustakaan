<?php

namespace App\PresentationLayer;

class ResponseCreatorPresentationLayer{
    private $code;
    private $message;
    private $data;
    private $errors;

    public function __construct($code, $message, $data, $errors)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code): void
    {
        $this->code = $code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message): void
    {
        $this->message = $message;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setErrors($errors): void
    {
        $this->errors = $errors;
    }

    public function getResponse()
    {
        return [
            'code'      => $this->code,
            'message'   => $this->message,
            'data'      => $this->data,
            'error'     => $this->errors,
        ];
    }
}