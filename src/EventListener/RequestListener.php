<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();

        $contentType = $request->getContentType();

        $payload = $event->getRequest()->getContent();

        $headers = $request->headers;

        $bearer = $request->headers->get('Authorization');

        $contentTypea = $request->headers->get('content-type');


        if ($contentType == 'application/json') {

            $payload = json_decode($event->getRequest()->getContent());
        }


    }
}