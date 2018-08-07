<?php
namespace App\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * https://github.com/nelmio/NelmioCorsBundle/blob/master/EventListener/CorsListener.php
 */
class CorsListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        ## https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#Requests_with_credentials

        $responseHeaders = $event->getResponse()->headers;

        $responseHeaders->set('Access-Control-Allow-Origin', 'http://localhost:4200');
        $responseHeaders->set('Access-Control-Allow-Credentials', 'true');
        $responseHeaders->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
        $responseHeaders->set('Access-Control-Allow-Headers', 'origin, content-type, accept');
        $responseHeaders->set('Access-Control-Expose-Headers', 'x-chromelogger-data'); ## on in dev mode
    }
}
