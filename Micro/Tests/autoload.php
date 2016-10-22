<?php
// require MICRO_DIR.'/vendor/autoload.php';

require __DIR__.'/../Autoload/Autoloader.php';

(new Autoloader(dirname(dirname(dirname(dirname(dirname(__DIR__))))))) #-----------------------

	->space( 'MicroMir'     		, '/vendor/zdorovo/micro/Micro')

->globalClass('/vendor/zdorovo/micro/Micro/Debug/d.php', ['d'])



	->space( 'Psr\Http\Message'	, '/vendor/psr/http-message/src')
	->space( 'Zend\Diactoros'	, '/vendor/zendframework/zend-diactoros/src')



;# Autoloader .................................................................