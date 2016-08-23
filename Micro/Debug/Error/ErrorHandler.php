<?php

class ErrorHandler
{
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
        ];
        if(array_key_exists($error, $errors)){
            return $errors[$error];
        }
    }

    public function __construct()
    {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL | E_STRICT);

        set_error_handler([$this, 'error']);

        set_exception_handler([$this, 'exception']);

        register_shutdown_function([$this, 'fatalError']);

   }


    public function error()
    {
        $args = debug_backtrace()[0]['args'];
        $code = $args[0];
        $name = $this->getErrorName($code);
        $message = $args[1];
        $file = $args[2];
        $line =$args[3];
        $this->view($code, $name, $message, $file, $line);

        return true;
    }

    public function exception($e)
    {
        $args = debug_backtrace()[0]['args'][0];
        if ($args instanceof \ParseError) {
            $code = 4; 
        }
        elseif ($args instanceof \Error) {
            $code = 1; 
        }
        $message = $args->getMessage();
        $file = $args->getFile();
        $line = $args->getLine();
        $name = $this->getErrorName($code);
        $this->view($code, $name, $message, $file, $line);

        return true;
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
                <div class=\"error_text content\">
                    {$message}
                </div>
                <div class=\"error_path content\">
                    {$file}::{$line}
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
