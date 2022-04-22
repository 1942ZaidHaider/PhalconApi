<?php

namespace Api\Utils;

use Phalcon\Escaper as E;

class Escaper
{
    public function __construct()
    {
        $this->escaper = new E();
    }
    public function escapeArray($array)
    {
        $arr = [];
        foreach ($array as $k => $a) {
            if (is_array($a)) {
                $arr[$k] = $this->escapeArray($a);
            } else {
                $arr[$k] = $this->escaper->escapeHtml($a);
            }
        }
        return $arr;
    }
    public function escape(String $str)
    {
        return $this->escaper->escapeHtml($str);
    }
}
