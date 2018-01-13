<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 13.01.18
 * Time: 16:11
 */

namespace Test\TestBundle\Exception;



use Symfony\Component\HttpKernel\Exception\HttpException;

class MultipleRouteException extends HttpException
{
    private $routes;

    public function __construct(array $routes, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $message = 'collision in routes: '.json_encode($routes);
        parent::__construct(500, $message, $previous, $headers, $code);
    }


}