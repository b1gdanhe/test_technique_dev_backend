<?php

namespace Core;

class Request
{
    private $method;
    private $uri;
    private $headers;
    private $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $this->parseUri();
        $this->headers = $this->parseHeaders();
        $this->body = $this->parseBody();
    }

    private function parseUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $position = strpos($uri, '?');
        
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        
        return rtrim($uri, '/');
    }

    private function parseHeaders(): array
    {
        $headers = [];
        
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('HTTP_', '', $key);
                $headerName = str_replace('_', '-', $headerName);
                $headers[strtolower($headerName)] = $value;
            } else if (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'])) {
                $headerName = str_replace('_', '-', $key);
                $headers[strtolower($headerName)] = $value;
            }
        }
        
        // Add custom auth header if present
        if (isset($_SERVER['HTTP_X_AUTH_TOKEN'])) {
            $headers['x-auth-token'] = $_SERVER['HTTP_X_AUTH_TOKEN'];
        }
        
        return $headers;
    }

    private function parseBody(): array
    {
        $body = [];
        
        if ($this->method === 'GET') {
            return $_GET;
        }
        
        // Handle JSON input
        $contentType = $this->getHeader('content-type');
        
        if ($contentType && strpos($contentType, 'application/json') !== false) {
            $content = file_get_contents('php://input');
            $decoded = json_decode($content, true);
            
            if (is_array($decoded)) {
                $body = $decoded;
            }
        } else {
            // Handle form data
            $body = $_POST;
        }
        
        return $body;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeader(string $name)
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function input(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->body;
        }
        
        return $this->body[$key] ?? $default;
    }

    public function getAuthToken(): ?string
    {
        return $this->getHeader('x-auth-token');
    }
}