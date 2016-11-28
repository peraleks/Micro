<?php
namespace MicroMir\Stages;

use MicroMir\Exception\MicroException;

class NotFound404Exception extends MicroException
{
    protected $exceptionCode = 'NotFound404';

    protected $ru = [
        0 => "{0}  {1}  {2} {3}",

    ];

    protected $en = [
        0 => "not translated",
        1 => "not translated",
        2 => "not translated",
        3 => "not translated",
    ];

    public function __construct(int $num, array $replace, $traceNumber = 0) {
        parent::__construct($num, $replace, $traceNumber);
    }
}