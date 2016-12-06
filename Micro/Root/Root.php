<?php
namespace MicroMir\Root;

class Root
{
    private $services = [];

    private $classLink = [];

    public static $instance;

    private function __construct() {}

    public static function instance()
    {
        self::$instance
            ?: self::$instance = new self;
        return self::$instance;
    }

    public function link($alias, $class, $provaider = null)
    {
        if (isset($this->services[$alias])) {
            new RootException(0, ['\' '.$alias.' \'']); // переделать
            return $this;
        }
        if (isset($this->classLink[$class])) {
            new RootException(0, ['\' '.$class.' \'']); // переделать
            return $this;
        }
        $this->services[$alias]['class'] = $class;
        $this->services[$alias]['provaider'] = $provaider;
        $this->classLink[$class] = &$this->services[$alias];

        return $this;
    }

    public function get($name)
    {
        if (!isset($this->services[$name])) {
            new RootException(1, [$name], 1);
            return new RootEmptyObject($name);
        }

        if (isset($this->services[$name]['provaider'])) {
            return $this->$name = (new $this->services[$name]['provaider'])->getService();
        }

        $arr = [];
        $refl = new \ReflectionClass($this->services[$name]['class']);
//        \d::d($refl);

        if ($method = $refl->getConstructor()) {
//            \d::d($method);

            $params = $method->getParameters();
//            \d::d($params);

            for ($i = 0; $i < count($params); ++$i) {
                $paramName = $params[$i]->getName();

                if ($this->hasName($paramName)) {
                    $arr[$i] = $this->$paramName;
                } else {
                    $arr[$i] = null;
                }
            }
        }
//        \d::d($arr);
//        \d::d($this);


        return $this->$name = $refl->newInstanceArgs($arr);

    }

    public function __call($name, $args)
    {
        new RootException(5, ['->'.$name.'()', 'Root']);
        return $this;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function hasName($name)
    {
        if (isset($this->services[$name])) return true;
    }


}
