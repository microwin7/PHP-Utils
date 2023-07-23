<?php

namespace Microwin7\PHPUtils\Utils;

class ResponseConstructor
{
    private array $data;

    public function message(string $message): object
    {
        $this->data['message'] = $message;
        return $this;
    }
    public function extra(array $array): object
    {
        foreach ($array as $k => $v) {
            $this->data[$k] = $v;
        }
        return $this;
    }
    public function success(string $message = ''): void
    {
        if (!empty($message)) $this->message($message);
        $this->data['success'] = true;
        $this->response();
    }
    public function failed(string $message = ''): void
    {
        if (!empty($message)) $this->message($message);
        $this->data['success'] = false;
        $this->response();
    }
    private function json_encode()
    {
        return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }
    private function header(): void
    {
        header("Content-Type: application/json; charset=UTF-8");
    }
    public function response(): void
    {
        $this->header();
        die($this->json_encode());
    }
}
