<?php
use MicroMir\Routing\Router;
use MicroMir\Routing\RouterException;


class RouterTest extends PHPUnit_Framework_TestCase
{
	private $router;

	// protected function setUp()
	// {
	// 	$this->router = new Router(['routes.php']);
	// }

	// protected function tearDown()
	// {
	// 	$this->router = NULL;
	// }

	public function testAdd()
	{
		$result = $this->router->matchUrl('/', 'GET');
		$this->assertEquals(array(), $result);
	}
} 
