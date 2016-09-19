<?php
namespace MicroMir\Root;

class Root
{
    private static $helpers = [];

    private static $register = [];

    public static $instance;

    private function __construct() {}

    public static function instance()
    {
        self::$instance
        ?:
        self::$instance = new self;

        return self::$instance;
    }

    public function FuncToLink($funcName) {
        if (array_key_exists($funcName, self::$helpers)) {
            return self::$helpers[$funcName]['method'];
        }
    }

    public function link($name = null, $content = null)
    {
        if (!$name || !$content) {
            new RootException(3, ['link()', 2, '([string], [Closure|Object])']);
            return $this;
        }
        if (isset(self::$register[$name])) {
            new RootException(0, ['\' '.$name.' \'']);
            return $this;
        }
        self::$register[$name] = $content; 
        return $this;
    }

    public function func($name = null, $content = null, $method = null)
    {
        if (!$name || !$content) {
            new RootException(3, ['func()', 3, '([string], [string], [string])']);
            return $this;
        }
        if (isset(self::$helpers[$name])) {
            new RootException(0, ['\' '.$name.' \'']);
            return $this;
        }
        self::$helpers[$name]['content'] = $content; 
        self::$helpers[$name]['method'] = $method;

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
        new RootException(10,
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
        if (!isset(self::$helpers[$name])) {
            new RootException(1, [$name]);
            return null;
        }
        if (!isset(self::$register[self::$helpers[$name]['content']])) {
            new RootException(4, ['::'.$name.'()', self::$helpers[$name]['content']]);
            return;
        }
        $content = &self::$register[self::$helpers[$name]['content']];

        if (is_callable($content)) {

            $content
            =
            call_user_func($content, self::$instance);
        }
        return
        call_user_func_array(array(
                                    $content,
                                    self::$helpers[$name]['method'],
                                   ), $args);
    }

}
