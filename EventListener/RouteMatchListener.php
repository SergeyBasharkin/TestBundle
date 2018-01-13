<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 13.01.18
 * Time: 14:42
 */

namespace Test\TestBundle\EventListener;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;

class RouteMatchListener
{

    private $router;

    /**
     * RouteMatchListener constructor.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        dump($this->router->getRouteCollection());
    }
}