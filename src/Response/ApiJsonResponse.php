<?php

namespace App\Response;

class ApiJsonResponse extends \Symfony\Component\HttpFoundation\Response
{

	//https://labs.omniti.com/labs/jsend
	public function __construct($data = null, int $statusCode = 200, array $headers = array(), bool $json = false)
	{
		parent::__construct($data, $statusCode, $headers, $json);
	}
}