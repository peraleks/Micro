<?php
namespace MicroMir\Stages;

use MicroMir\Exception\MicroException;

class StageControllerException extends MicroException
{
    protected $exceptionCode = 'StageController';

    protected $ru = [
        0 => "{0} должен возвращать либо экземпляр {1} либо строку 'stop'. Однако вернул {2} ",

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