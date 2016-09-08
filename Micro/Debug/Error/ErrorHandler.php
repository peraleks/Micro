<?php

namespace MicroMir\Debug\Error;

class ErrorHandler
{
    static private $instance;

    static private $mgs;

    static private $countHeaders = 0;

    private $headerMessage = [];

    private $headerMessageDefault = [];

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

        self::$mgs = &$GLOBALS['MICROCODER_GLOBAL_SETTINGS'];

        $this->headerMessageDefault['header'] = '500 Internal Server Error';
        $this->headerMessageDefault['space']  = "@_#_@_%_@_#_@";
        $this->headerMessageDefault['en']     = "Don't worry!<br>Chip 'n Dale Rescue Rangers";
        $this->headerMessageDefault['ru']     = "Сервер отдыхает. Зайдите позже";
   }

    static public function instance()
    {
        self::$instance ?: self::$instance = new self;
        
        return self::$instance;
    }

    private function Dev() {
        if (self::$mgs['DEVELOPMENT']
            &&
            array_key_exists($_SERVER['REMOTE_ADDR'], self::$mgs['DEVELOPMENT_IP']))
        { return true; }
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
        $args    = debug_backtrace()[0]['args'][0];
        $code    = $args->getCode();
        $message = $args->getMessage();
        $file    = $args->getFile();
        $line    = $args->getLine();

        if ($args instanceof \ParseError) {
            $code = 4;
            $this->sendHeaderMessage($file, $message);
        }
        elseif ($args instanceof \Error) {
            $code = 1; 
            $this->sendHeaderMessage($file, $message);
        }
        elseif ($args instanceof \Exception && $code == 0) {
            $code = 3;
            $this->sendHeaderMessage($file, $message);
        }

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
        }
        else {
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
            $this->sendHeaderMessage($error['file'], $error['message']);
        }
    }

    public function headerMessage($array = null) {
        if ($array === null) {
            $this->errorParam('empty parametrs');
            return $this;
        }
        if (!array_key_exists('dir', $array)) {
            $this->errorParam("missing key 'dir'");
            return $this;
        }
        $this->headerMessage[$array['dir']] = array_merge($this->headerMessageDefault, $array);

        return $this;
    }

    private function errorParam($params) {
        $deb = debug_backtrace()[1];
        $this->notify(2, 'USER_WARNING', $params, $deb['file'], $deb['line']);
    }

    private function sendHeaderMessage($file, $mess, $err = null) {
        if ($this->Dev()) return;

        $arr = $this->headerMessageDefault;

        foreach ($this->headerMessage as $headerMessageKey => $headerMessageValue) {

            if (preg_match('#^'.$headerMessageKey.'.*#', $file)
                ||
                preg_match("#^.*?".$headerMessageValue['space'].".*#", $mess))
            {
                $arr  = $headerMessageValue;
                break;
            }
        }
        $number  = explode(' ', $arr['header'])[0];

        if (self::$mgs['LOCALE'] && self::$mgs['LOCALE'] == 'en') {
            $message = $arr['en'];
        }
        else {
            $message = $arr['ru'];
        }

        if (self::$countHeaders < 1) {
            header($_SERVER['SERVER_PROTOCOL'].' '.$arr['header']);
        }
        self::$countHeaders++;

        echo "
        <style>".
                file_get_contents(__DIR__.'/error.css')
        ."</style>
            <div class=\"width error_box\">
                <div class=\"error_500 error_header\">".$number.$err."</div>
                <div></div>
                <div class=\"error_text error_content\">
                    $message
                </div>
            </div>
        ";
    }

    private function notify($code, $name, $message, $file, $line)
    {
        if ($this->Dev())
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
                $log = self::$mgs['BASE_DIR'].self::$mgs['ERROR_LOG_FILE'];
                $perm = self::$mgs['WEB_DIR'].'/error_permission_storage!_!_!_!_!_!_!.log';
            }
            else {
                $log = __DIR__.
                '/../../../../../../storage/logs/error_SETTINGS_!_!_!_!_!_!_!_!.log';
                $perm = __DIR__.
                '/../../../../../../error_permission_storage!_!_!_!_!_!_!.log';
                $this->sendHeaderMessage($file, $message, ' settings');
            }

            if (!$error = @fopen($log, 'ab')) {
                if (!$error = @fopen($perm, 'ab')) {
                    $this->sendHeaderMessage($file, $message, ' permission');
                    return;
                }
                $this->sendHeaderMessage($file, $message, ' permission');
            } 
            $time = date('Y m d - h:i:s');
            fwrite($error, '---- '.$time." -------- ".'['.$code.'] '.$name." --------\n"
                            .$message."\n"
                            .$file.'::'.$line."\n\n");
            fclose($error);
        }
    }
}
