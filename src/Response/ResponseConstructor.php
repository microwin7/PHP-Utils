<?php

namespace Microwin7\PHPUtils\Response;

class ResponseConstructor
{
    private $data = [];

    public function __construct(
        $data = []
    ) {
        $this->data = $data;
    }
    public function message(string $message): object
    {
        $this->data['message'] = $message;
        return $this;
    }
    public function error(string $error): object
    {
        $this->data['error'] = $error;
        return $this;
    }
    public function code(int $code): object
    {
        $this->data['code'] = $code;
        return $this;
    }
    public function code_response(int $code): object
    {
        http_response_code($code);
        return $this;
    }
    public function extra(array $array): object
    {
        foreach ($array as $k => $v) {
            $this->data[$k] = $v;
        }
        return $this;
    }
    public function success(?string $message = null, bool $need_success = false): void
    {
        null === $message ?: $this->message($message);
        !$need_success ?: $this->data['success'] = true;
        $this->response();
    }
    public function failed(?string $message = null, ?string $error = null, bool $need_success = false, int $code = 400): void
    {
        null === $message ?: $this->message($message);
        null === $error ?: $this->error($error);
        !$need_success ?: $this->data['success'] = false;
        $this->code_response($code);
        $this->response();
    }
    private function json_encode()
    {
        return json_encode((object)$this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
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
