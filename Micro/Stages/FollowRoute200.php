<?php
namespace MicroMir\Stages;

class FollowRoute200
{
	public function __construct($R)
	{
		$this->R 	 		   = $R;
		$this->Route  		   = $R->Route;
		$this->ResponseFactory = $R->ResponseFactory;
	}

//    public function executeStage()
//    {
//        if ($this->Route->code == 200) {
//            $GLOBALS['MICRO_ERROR_MARKER'] = 1;
//            $action = $this->Route->action;
//            (new $this->Route->controller($this->R, $this->Route->params))
//                ->$action($this->R, $this->Route->params);
//
//            return 'stop';
//        }
//    }

    public function executeStage()
    {
        if ($this->Route->code == 200) {
            $GLOBALS['MICRO_ERROR_MARKER'] = 1;
            $refl = new \ReflectionClass($this->Route->controller);
//            \d::d($refl);
            $method = !$refl->hasMethod($this->Route->action) ?: $refl->getMethod($this->Route->action);
//            \d::d($method);
            $params = $method->getParameters();
//            \d::d($params);
            $arr = [];
            for ($i = 0; $i < count($params); ++$i) {
                $arr[$i] = $this->R->hasName($params[$i]->getName())
                    ? $this->R->get($params[$i]->getName())
                    : null;
//                $arr[]['default'] = $param->getDefaultValue();
            }
//            \d::d($arr);

            call_user_func_array([new $this->Route->controller(), $this->Route->action], $arr);

//            $action = $this->Route->action;
//            (new $this->Route->controller($this->R, $this->Route->params))
//                ->$action($this->R, $this->Route->params);

            return 'stop';
        }
    }
}