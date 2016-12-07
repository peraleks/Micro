<?php
namespace MicroMir\Stages;

class FollowRoute200
{

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
        $c = c();
//            \d::d($c->Route);

        if ($c->Route->code == 200) {
            $GLOBALS['MICRO_ERROR_MARKER'] = 1;
            $refl = new \ReflectionClass($c->Route->controller);
//            \d::d($refl);
            $method = !$refl->hasMethod($c->Route->action) ?: $refl->getMethod($c->Route->action);
//            \d::d($method);
            $params = $method->getParameters();
//            \d::d($params);
            $arr = [];
            for ($i = 0; $i < count($params); ++$i) {
//                \d::d($params[$i]->getName());
                $c->hasAlias($params[$i]->getName())
                    ? $arr[$i] = $c->get($params[$i]->getName())
                    : $arr[$i] = null;
//                $arr[]['default'] = $param->getDefaultValue();
//                \d::d($arr);
            }
//            \d::d($c->Route);

            call_user_func_array([new $c->Route->controller, $c->Route->action], $arr);

//            $action = $this->Route->action;
//            (new $this->Route->controller($this->R, $this->Route->params))
//                ->$action($this->R, $this->Route->params);

            return 'stop';
        }
    }
}