<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\SerializerInterface;

class BaseControllerApi extends Controller
{
	protected $logger;
	protected $serializer;

	public function __construct(LoggerInterface $logger, SerializerInterface $serializer)
	{
		$this->logger = $logger;

		$this->serializer = $serializer;
	}
}