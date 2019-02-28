<?php

namespace WebImage\Controllers;

class ExceptionsController extends AbstractController
{
	CONST ATTR_EXCEPTION = 'exception';

	private $exception;

	public function setError(\Exception $e)
	{
		$this->exception = $e;
	}

	public function exception()
	{
		return $this->view(['exception' => $this->getException()]);
	}

	public function getException()
	{
		return $this->getRequest()->getAttribute(self::ATTR_EXCEPTION);
	}

	public function notFound()
	{
//		$factory = $this->getContainer()->get(Factory::class);

//		$view = $factory->create($viewKey, $vars);
		return $this->view([], 'errors/notfound');
	}
}