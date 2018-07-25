<?php

namespace App\Response;

class ErrorJsonResponse extends ApiJsonResponse
{
	//https://labs.omniti.com/labs/jsend

	public function __construct(string $message = null)
	{
		parent::__construct(['status' => 'fail', 'message' => $message], 400);
	}
}