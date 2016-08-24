<?php

namespace Micro\Debug\Error;

class ErrorHandler
{
    static private $instance;

    private function getErrorName($error){
        $errors = [
            E_ERROR             => 'ERROR',
            E_WARNING           => 'WARNING',
            E_PARSE             => 'PARSE',
            E_NOTICE            => 'NOTICE',
            E_CORE_ERROR        => 'CORE_ERROR',
            E_CORE_WARNING      => 'CORE_WARNING',
            E_COMPILE_ERROR     => 'COMPILE_ERROR',
            E_COMPILE_WARNING   => 'COMPILE_WARNING',
            E_USER_ERROR        => 'USER_ERROR',
            E_USER_WARNING      => 'USER_WARNING',
            E_USER_NOTICE       => 'USER_NOTICE',
            E_STRICT            => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED        => 'DEPRECATED',
            E_USER_DEPRECATED   => 'USER_DEPRECATED',
            3                   => 'not_caught_Exception',
            0                   => 'Unknown_Type_Error',
        ];
        if(array_key_exists($error, $errors)){
            return $errors[$error];
        }
    }

    private function __construct()
    {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL | E_STRICT);

        set_error_handler([$this, 'error']);

        set_exception_handler([$this, 'exception']);

        register_shutdown_function([$this, 'fatalError']);

   }

   static public function instance() {
        self::$instance ?: self::$instance = new self;

        return self::$instance;
   }


    public function error()
    {
        $args    = debug_backtrace()[0]['args'];
        $code    = $args[0];
        $name    = $this->getErrorName($code);
        $message = $args[1];
        $file    = $args[2];
        $line    = $args[3];
        $this->view($code, $name, $message, $file, $line);

        return true;
    }

    public function exception()
    {
        $args = debug_backtrace()[0]['args'][0];
        $code = $args->getCode();
        $message = $args->getMessage();
        if ($args instanceof \ParseError) {
            $code = 4; 
        }
        elseif ($args instanceof \Error) {
            $code = 1; 
        }
        elseif ($args instanceof \Exception && $code == 0) {
            $code = 3;
        } 
        $file = $args->getFile();
        $line = $args->getLine();
        $name = $this->getErrorName($code);
        $this->view($code, $name, $message, $file, $line);

        return true;
    }


    public function microException($obj)
    {
        $code    = $obj->getCode();
        $name    = 'Micro_Exception';
        $message = $obj->getMessage();
        $trace   = $obj->getTrace();
        $file    = $trace[0]['file'];
        $line    = $trace[0]['line'];
        $this->view($code, $name, $message, $file, $line);
    }

    public function fatalError()
    {
        if ($error = error_get_last()) {
            ob_end_clean();
            $name = $this->getErrorName($error['type']);
            $this->view($error['type'], $name, $error['message'], $error['file'], $error['line']);
        }
    }

    private function view($code, $name, $message, $file, $line)
    {
        echo "
        <html>
        <style>".
                file_get_contents(__DIR__.'/error.css')
        ."</style>
        <body>
            <div class=\"error_handler\">
                <div class=\"$name header\">[{$code}] {$name}</div>
                <div class=\"error_text error_content\">
                    {$message}
                </div>
                <div class=\"error_path error_content\">
                    {$file} <div class=\"error_path error_content error_line\">{$line}</div>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
