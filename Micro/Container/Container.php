<?php
namespace MicroMir\Container;

class Container
{
    private $services = [];

    private $classLink = [];

    public static $instance;

    private function __construct() {}

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
            self::$instance->c = self::$instance;
            self::$instance->services['c'] = [];
            self::$instance->services['c']['class'] = __CLASS__;
        }
        return self::$instance;
    }

    public function bind($alias, $class, $provaider = null)
    {
        if (isset($this->services[$alias])) {
            new ContainerException(0, [$alias]);
            return $this;
        }
        $this->services[$alias]['class'] = $class;
        $this->services[$alias]['provaider'] = $provaider;
        !$provaider ?: $this->classLink[$class] = &$this->services[$alias];

        return $this;
    }

    public function get($alias)
    {
        if (property_exists($this, $alias)) {
            return $this->$alias;
        }
        if (!isset($this->services[$alias])) {
            $e = new ContainerException(1, [$alias]);
            throw new \Exception('STOP -> '.$e->getMessage()['logError']);
        }
        return $this->inject($alias);
    }

    public function __get($alias)
    {
        if (!isset($this->services[$alias])) {
            $e = new ContainerException(1, [$alias]);
            throw new \Exception('STOP -> '.$e->getMessage()['logError']);
        }
        return $this->inject($alias);
    }

    private function inject($alias)
    {
        if (isset($this->services[$alias]['provaider'])) {
            return $this->$alias = (new $this->services[$alias]['provaider'])->getService();
        }

        $injectParams = [];
        $refl = new \ReflectionClass($this->services[$alias]['class']);
//        \d::d($refl);

        if ($method = $refl->getConstructor()) {
//            \d::d($method);

            $params = $method->getParameters();
//            \d::d($params);

            for ($i = 0; $i < count($params); ++$i) {
                $paramName = $params[$i]->getName();
//                \d::p($paramName);

                try {
                    $paramClass = $params[$i]->getClass();

                } catch (\Exception $e) {
                    $file = $method->getFileName();
                    $line = $method->getStartLine();
                    $e = new ContainerException(3, [$e->getMessage()], $file.'::'.$line);
                    throw new \Exception('STOP -> '.$e->getMessage()['logError']);
                }

                if ($paramClass) {
                    $classType = $paramClass->getName();
                } else {
                    $classType = null;
                }

                if ($this->hasAlias($paramName)) {
                    if ($classType) {
                        if ($this->services[$paramName]['class'] != $classType) {
                            $e = new ContainerException(2, [$this->services[$paramName]['class'], $classType, $paramName]);
                            throw new \Exception('STOP -> '.$e->getMessage()['logError']);
                        }
                    }
                    $injectParams[$i] = $this->$paramName;

                } else {
                    if ($classType) {
                        $classRefl = new \ReflectionClass($classType);
                        if (!$classRefl->isInstantiable()) {
                            $file = $method->getFileName();
                            $line = $method->getStartLine();
                            $e = new ContainerException(4, [$classType], $file.'::'.$line);
                            throw new \Exception('STOP -> '.$e->getMessage()['logError']);
                        }
                        $injectParams[$i] = ($classRefl->newInstance());
                    } else {
                        $injectParams[$i] = null;
                    }
                }
            }
        }
//        \d::d($injectParams);
//        \d::d($this);


        return $this->$alias = $refl->newInstanceArgs($injectParams);
    }

    public function hasAlias($alias)
    {
        if (isset($this->services[$alias])) return true;
    }


}
