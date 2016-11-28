<?php
namespace MicroMir\Stages;

use Zend\Diactoros\Response;

class StageController
{
    private $stages = [];

    private $after  = [];

    public function __construct($R)
    {
        $this->R       = $R;
        $this->Emitter = $R->Emitter;
    }

    public function stages(array $array)
    {
        $this->stages = array_merge($this->stages, array_reverse($array));
        return $this;
    }

    public function afterResponse(array $array)
    {
        $this->after = $array;
        return $this;
    }

    public function nextStage()
    {
        if (!$stage = array_pop($this->stages)) {
            $this->afterResponseRun();
            return;
        }
        if (($response = (new $stage($this->R))->executeStage()) !== null) {
            if ($response === 'stop') {
                $this->afterResponseRun();
                return;
            }
            if (!($response instanceof Response)) {
                $returned = gettype($response);
                if ($returned == 'string') {
                    $returned = "'$response'";
                } elseif ($returned == 'object') {
                    $returned = get_class($response);
                }
                new StageControllerException(0, [$stage, Response::class, $returned]);
            } else {
                $this->Emitter->emit($response);
            }
            $this->afterResponseRun();
            return;
        }
        $this->nextStage();
    }

    private function afterResponseRun()
    {
        foreach ($this->after as $value) {
            (new $value($this->R))->executeStage();
        }
    }
}