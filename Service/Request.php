 <?php

 namespace Mate\RestBundle\Service;
 
 use Mate\RestBundle\Request\RequestInterface;
 use Rakit\Validation\Validator;
 use Symfony\Component\HttpFoundation\RequestStack;
 use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
 use Adbar\Dot as DotArray;
 use Symfony\Component\HttpKernel\Exception\HttpException;
 use Symfony\Component\Process\Exception\ProcessFailedException;
 use Symfony\Component\Process\Exception\RuntimeException;
 
 abstract class Request {
 
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
	 
	 
	abstract protected function rules(): array;
 
  	/**
  	 * @param string $group
 	 * @return $this
 	 * @return DotArray
  	 * @throws \Exception
  	 */
  	public function handle($group = null)
 	{
 		$rules = $this->rules();
 
 		if ($group && !array_key_exists($group, $rules))
 			throw new HttpException(sprintf('Undefined %s group', $group));
 
 		$validation = $this->validator->validate($this->getRequest()->all(), $group ? $rules[$group] : $rules);
 
 		if ($validation->fails()) {
 			$error = $validation->errors()->firstOfAll();
  			throw new BadRequestHttpException($error[0]);
  		}
  
 		return $this;
 		return $this->getRequest();
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
