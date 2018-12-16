<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class BasicController extends AbstractController
{
    
    /**
     * Returns the data of the request json as an associative array
     *
     * @param   Request     $request
     * @return  array|null
     * @author  Markus Buscher
     */
    protected function getRequestData(Request $request): array
    {
        $content_raw = $request->getContent();
        $content = strlen($content_raw) ? json_decode($content_raw, true) : null;
        
        return $content;
    }
    
    
    /**
     * Fetches the value of the request parameter, which might be given through json body or query parameter
     *
     * @param   Request $request
     * @param   string  $keyname
     * @param   array   $options
     * @return  mixed
     * @author  Markus Buscher
     */
    protected function getParam(Request $request, string $keyname, array $options = [])
    {
        // which value should be used as default, if the param is not present
        $default  = $options['default'] ?? null;
        
        // is it mandatory to pass this parameter on the request
        $required = (bool)($options['required'] ?? false);
        
        // read the body
        $content = $this->getRequestData($request);
        
        if (is_array($content) && isset($content[$keyname]))
        {
            return $content[$keyname];
        }
        else if ($request->request->has($keyname))
        {
            $result = $request->request->get($keyname);
            
            if (!strlen($required))
            {
                throw new BadRequestHttpException("The mandatory param $keyname is empty");
            }
            
            return $result;
        }
        else if(!$required)
        {
            return $request->query->get($keyname, $default);
        }
        else
        {
            throw new BadRequestHttpException("The mandatory param $keyname is missing");
        }
    }
    
    
    /**
     * Fetches a DateTime object from the request parameters
     *
     * @param   Request $request
     * @param   string  $keyname
     * @param   array   $options
     * @return  \DateTime
     * @author  Markus Buscher
     */
    protected function getDatetimeParam(Request $request, string $keyname, array $options = []): ?\DateTime
    {
        $result = $this->getParam($request, $keyname, $options);
    
        try
        {
            if(strlen($result) > 0)
            {
                return new \DateTime($result);
            }
            else
            {
                return $result;
            }
        }
        catch (\Exception $e)
        {
            throw new BadRequestHttpException("The param $keyname is not formatted as DateTime");
        }
    }
    
    
    /**
     * Builds the json response for successful requests
     *
     * @param   array   $rows
     * @param   int     $http_status_code
     * @return  JsonResponse
     * @author  Markus Buscher
     */
    protected function successJson(array $rows, int $http_status_code = JsonResponse::HTTP_OK): JsonResponse
    {
        $response_body = array(
            'rows'    => $rows,
            'success' => true,
            'total'   => count($rows)
        );
        
        return parent::json($rows, $http_status_code);
    }
}
