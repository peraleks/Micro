<?php

namespace MicroMir\Debug\Error;

class ErrorHandler
{
    static private $instance;

    static private $mgs;

    static private $dev;

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
        set_error_handler([$this, 'error']);

        set_exception_handler([$this, 'exception']);

        register_shutdown_function([$this, 'fatalError']);
   }

   static public function instance() {
        self::$instance ?: self::$instance = new self;

        self::$mgs = &$GLOBALS['MICROCODER_GLOBAL_SETTINGS'];

         if (self::$mgs['DEVELOPMENT'] &&
            array_key_exists($_SERVER['REMOTE_ADDR'], self::$mgs['DEVELOPMENT_IP'])) {
            self::$dev = true;
         }

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
        $this->notify($code, $name, $message, $file, $line);
        
        return true;
    }

    public function exception()
    {
        $args = debug_backtrace()[0]['args'][0];
        $code = $args->getCode();
        $message = $args->getMessage();
        if ($args instanceof \ParseError) {
            $code = 4;
            $this->send500();
        }
        elseif ($args instanceof \Error) {
            $code = 1; 
            $this->send500();
        }
        elseif ($args instanceof \Exception && $code == 0) {
            $code = 3;
            $this->send500();
        } 
        $file = $args->getFile();
        $line = $args->getLine();
        $name = $this->getErrorName($code);
        $this->notify($code, $name, $message, $file, $line);

        return true;
    }


    public function microException($obj, $traceNumber = 0)
    {
        $code    = $obj->getCode();
        $name    = 'Micro_Exception';
        $message = $obj->getMessage();
        $trace   = $obj->getTrace();
        if (!isset($trace[$traceNumber]['file'])) {
            $file = $traceNumber;
            $line = '';
        } else {
            $file = $trace[$traceNumber]['file'];
            $line = $trace[$traceNumber]['line'];
        }
        $this->notify($code, $name, $message, $file, $line);
    }

    public function fatalError()
    {
        if ($error = error_get_last()) {
            ob_end_clean();
            $name = $this->getErrorName($error['type']);
            $this->notify($error['type'], $name, $error['message'], $error['file'], $error['line']);
            $this->send500();
        }
    }

    private function send500() {
        if (self::$dev) return;
        header($_SERVER['SERVER_PROTOCOL'].' 500 Internal Server Error');

        self::$mgs['LOCALE'] == 'en'
        ?
        $message = "Don't worry!<br>Chip 'n Dale Rescue Rangers"
        :
        $message = "Сервер отдыхает. Зайдите позже";

        echo "
        <style>".
                file_get_contents(__DIR__.'/error.css')
        ."</style>
            <div class=\"width error_box\">
                <div class=\"error_500 error_header\">500</div>
                <div></div>
                <div class=\"error_text error_content\">
                    $message
                </div>
            </div>
        ";
    }

    private function notify($code, $name, $message, $file, $line)
    {
        if (self::$dev)
        {
            echo "
            <html>
            <style>".
                    file_get_contents(__DIR__.'/error.css')
            ."</style>
            <body>
                <div class=\"error_box\">
                    <div class=\"$name error_header\">[{$code}] {$name}</div>
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
        else {
            if (self::$mgs['ERROR_LOG_FILE']) {
                $log = &self::$mgs['ERROR_LOG_FILE'];
                $perm = &self::$mgs['WEB_DIR'];
            }
            else {
                $log = __DIR__.'/../../../../../../storage/logs/error_GLOBAL_SETTINGS_!_!_!_!_!_!_!_!.log';
                $perm = __DIR__.'/../../../../../../public/error_permission_storage!_!_!_!_!_!_!.log';
                self::$dev = false;
                $this->send500();
            }

            if (!$error = fopen($log, 'ab')) {
                $error = fopen($perm, 'ab');
            } 
                $time = date('Y m d - h:i:s');
                fwrite($error, '---- '.$time." -------- ".'['.$code.'] '.$name." --------\n"
                                .$message."\n"
                                .$file.'::'.$line."\n\n");
                fclose($error);

        }
    }
}
