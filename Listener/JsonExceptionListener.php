<?php


namespace Mate\RestBundle\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class JsonExceptionListener {

	/**
	 * @param GetResponseForExceptionEvent $event
	 */
	public function onKernelException(GetResponseForExceptionEvent $event)
	{
		$exception = $event->getException();
		$data = array(
			'code' => 42,
			'type' => 'exception',
			'data' => array(
				'message' => $exception->getMessage()
			)
		);

		$response = new JsonResponse($data);
		$event->setResponse($response);
	}
}