<?php
namespace MicroMir\Root;

class Root
{
    public static $register = [];

    public static $instance;

    private function __construct() {}

    public static function instance()
    {
        self::$instance
        ?:
        self::$instance = new self;

        return self::$instance;
    }

    public function link($name, $class)
    {
        if (isset(self::$register[$name])) {
            new RootException(0, ['\' '.$name.' \'']);
            return $this;
        }
        self::$register[$name] = $class; 
        return $this;
    }

    public function get($name)
    {
        if (!isset(self::$register[$name])) {
            new RootException(1, [$name], 1);
            return new RootEmptyObject($name);
        }
        if (is_callable(self::$register[$name])) {

            $this->$name
            =
            self::$register[$name]
            =
            call_user_func(self::$register[$name], self::$instance);
        }
        return $this->$name = self::$register[$name];
    }

    public function __call($name, $args)
    {
        new RootException(3,
        [
            $name,
            '$R->Server()',
            '$R->Server',
            '$R->get(\'Server\')',
            '$R::Server()',
        ]
        );
        return $this->get($name);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public static function __callStatic($name, $args)
    {
        if (!isset(self::$register[$name])) {
            new RootException(1, [$name]);
            return new RootEmptyObject($name);
        }
        if (is_callable(self::$register[$name])) {

            self::$register[$name]
            =
            call_user_func(self::$register[$name], self::$instance);
        }
        return self::$register[$name];
    }

}
