<?php
namespace MicroMir\Providers;

use Zend\Diactoros\Response;
use Zend\Diactoros\Response\{
				HtmlResponse,
				JsonResponse,
				TextResponse,
				EmptyResponse,
				RedirectResponse
};

class ResponseFactory
{
	public function get($body = 'php://memory', $status = 200, $type = 'html', array $headers = [])
	{
		switch ($type) {
			case 'html':
				return new HtmlResponse($body, $status, $headers);
				break;

			case 'json':
				return new JsonResponse($body, $status, $headers);
				break;

			case 'redirect':
				return new RedirectResponse($body, $status, $headers);
				break;

			case 'empty':
				return new EmptyResponse($status, $headers);
				break;

			case 'text':
				return new TextResponse($body, $status, $headers);
				break;

			case 'stream':
				return new Response($body, $status, $headers);
				break;
			
			default:
				return new HtmlResponse($body, $status, $headers);
				break;
		}
	}
}