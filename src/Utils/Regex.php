<?php

namespace Microwin7\PHPUtils\Utils;

class Regex
{
    private $data;
    private $pattern;
    private $last_name;
    private $last_data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
    public function name($name_data)
    {
        $this->last_name = $name_data;
        $this->last_data = $this->data->{$this->last_name};
        return $this;
    }
    public function pattern(...$pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }
    public function valid()
    {
        $this->not_empty()
            ->isset()
            ->preg_match();
        return $this;
    }

    public function isset()
    {
        return isset($this->last_data) ? $this : $this->reply();
    }
    public function not_null()
    {
        return !is_null($this->last_data) ? $this : $this->reply();
    }
    public function not_empty()
    {
        return !empty($this->last_data) ? $this : $this->reply();
    }
    private function preg_match()
    {
        foreach ($this->pattern as $pattern) {
            if (preg_match($pattern, $this->last_data, $v) !== 1) {
                $this->pattern = [];
                return $this->reply();
            }
        }
        $this->pattern = [];
        return $this;
    }
    private function reply()
    {
        Response::failed('[' . $this->last_name . '] ' . $this->last_data . ' - значение не прошло проверку');
    }
}
