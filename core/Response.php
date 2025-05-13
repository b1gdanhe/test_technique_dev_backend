<?php

namespace Core;

class Response
{
    private $statusCode = 200;
    private $headers = [];
    private $sent = false;

    public function setStatusCode(int $code): Response
    {
        $this->statusCode = $code;
        return $this;
    }

    public function setHeader(string $name, string $value): Response
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function json($data = null): void
    {
        $this->setHeader('Content-Type', 'application/json');
        
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        
        http_response_code($this->statusCode);
        
        if ($data !== null) {
            echo json_encode($data);
        }
        
        $this->sent = true;
    }

    public function error(string $message, int $code = 400): void
    {
        $this->setStatusCode($code);
        $this->json(['error' => $message]);
    }

    public function isSent(): bool
    {
        return $this->sent;
    }
}