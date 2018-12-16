<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Handles kernel events
 * date: 16.12.2018 12:07
 *
 * @author: Markus Buscher
 * @see     https://symfony.com/doc/current/event_dispatcher.html
 */
class ExceptionListener
{
    /**
     * Handles all errors of the application
     *
     * @param   GetResponseForExceptionEvent        $event
     * @return  void
     * @author  Markus Buscher
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();
    
        $json = array(
            'error'   => array(
                'message' => $exception->getMessage(),
                'code'    => $exception->getCode(),
                'file'    => $exception->getFile() . '::' . $exception->getLine(),
                'stack'   => $exception->getTraceAsString()
            ),
            'success' => false,
            'total'   => 0
        );
        
        $response = new JsonResponse($json);
        
        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface)
        {
            $response->setStatusCode($exception->getStatusCode());
        }
        else
        {
            $response->setStatusCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
