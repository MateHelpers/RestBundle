<?php


namespace Mate\RestBundle\Service;

use Mate\RestBundle\Request\RequestInterface;
use Rakit\Validation\Validator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Adbar\Dot as DotArray;
use Symfony\Component\Process\Exception\RuntimeException;

class Request {

	/** @var RequestStack */
	protected $requestStack;

	/** @var Validator */
	protected $validator;

	/**
	 * RequestService constructor.
	 * @param RequestStack $requestStack
	 */
	public function __construct(RequestStack $requestStack)
	{
		$this->requestStack = $requestStack;
		$this->validator    = new Validator();
	}

	/**
	 * @param RequestInterface $requestClass
	 * @return $this
	 * @throws \Exception
	 */
	public function handleFromClass($requestClass)
	{
		/** @var RequestInterface $requestInstance */
		$request = new $requestClass();

		if (!$request instanceof RequestInterface) {
			throw new RuntimeException('Request class must be instance of ' . RequestInterface::class);
		}

		$rules = $request->rules();

		$validation = $this->validator->validate($this->getRequest()->all(), $rules);

		if ($validation->fails()) {
			$error = $validation->errors()->firstOfAll();
			throw new BadRequestHttpException($error[0]);
		}

		return $this;
	}

	/**
	 * @param $key
	 * @return mixed
	 * @throws \Exception
	 */
	public function get($key)
	{
		$requestDotArray = $this->getDotArrayRequest();

		if (!$requestDotArray->has($key))
			throw new BadRequestHttpException("$key not found in the request");

		return $requestDotArray->get($key);
	}

	/**
	 * @return DotArray
	 * @throws \Exception
	 */
	public function getRequest()
	{
		return $this->getDotArrayRequest();
	}

	/**
	 * @return DotArray
	 * @throws \Exception
	 */
	private function getDotArrayRequest(){
		$content = $this->getCurrentRequest()->getContent();

		if(empty($content)){
			throw new BadRequestHttpException("Request content is empty");
		}

		return new DotArray(json_decode($content, true));
	}

	/**
	 * @return null|\Symfony\Component\HttpFoundation\Request
	 */
	private function getCurrentRequest()
	{
		return $this->requestStack->getCurrentRequest();
	}

}