<?php
/*
 * https://github.com/qandidate-labs/symfony-json-request-transformer/blob/master/src/Qandidate/Common/Symfony/HttpKernel/EventListener/JsonRequestTransformerListener.php
 *
 * This file is part of the qandidate/symfony-json-request-transformer package.
 *
 * (c) Qandidate.com <opensource@qandidate.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Transforms the body of a json request to POST parameters.
 */
class JsonRequestListener
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$this->isJsonRequest($request)) {
            return;
        }

        $content = $request->getContent();
        if (empty($content)) {
            return;
        }
        if (!$this->transformJsonBody($request)) {
            $response = Response::create('Unable to parse request.', 400);
            $event->setResponse($response);
        }
    }

    private function isJsonRequest(Request $request)
    {
        return 'json' === $request->getContentType();
    }

    private function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
        if ($data === null) {
            return true;
        }
        $request->request->replace($data);
        return true;
    }
}