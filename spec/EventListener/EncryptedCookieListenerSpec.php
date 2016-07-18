<?php

namespace spec\Lexiphanic\EncryptedCookieBundle\EventListener;

use Lexiphanic\EncryptedCookieBundle\EventListener\EncryptedCookieListener;
use Lexiphanic\EncryptedCookieBundle\Encryption\EncryptionInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class EncryptedCookieListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EncryptedCookieListener::class);
    }
    
    function let(EncryptionInterface $encryption) {
        $encryption->encrypt('test')->willReturn('dGVzdA==');
        $encryption->decrypt('dGVzdA==')->willReturn('test');
        $this->beConstructedWith(['session'], $encryption);
    }
    
    function it_should_return_null_if_not_master_request_on_filter_response_event (
            FilterResponseEvent $event
    ) {
        $event->getRequestType()->willReturn(HttpKernelInterface::SUB_REQUEST);
        $this->onKernelResponse($event)->shouldReturn(null);
    }
    
    function it_should_encrypt_a_cookie (
            Request $request,
            FilterResponseEvent $event,
            ResponseHeaderBag $headers,
            Response $response
    ) {
        $currentCookie = new Cookie('session', 'test', 3600, '/account', 'example.com', true, false);
        $newCookie = new Cookie('session', 'dGVzdA==', 3600, '/account', 'example.com', true, false);
        $headers->getCookies()->willReturn([$currentCookie]);
        $headers->removeCookie("session", "/account", "example.com")->shouldBeCalled();
        $headers->setCookie($newCookie, "/account", "example.com")->shouldBeCalled();
        $response->headers = $headers;
        
        $event->getRequest()->willReturn($request);
        $event->getRequestType()->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $event->getResponse()->willReturn($response);
        
        $this->onKernelResponse($event)->shouldReturn(null);
    }
    
    function it_should_not_encrypt_a_cookie (
            Request $request,
            FilterResponseEvent $event,
            ResponseHeaderBag $headers,
            Response $response
    ) {
        $currentCookie = new Cookie('not_session', 'test', 3600, '/account', 'example.com', true, false);
        $headers->getCookies()->willReturn([$currentCookie]);
        $response->headers = $headers;
        
        $event->getRequest()->willReturn($request);
        $event->getRequestType()->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $event->getResponse()->willReturn($response);
        
        $this->onKernelResponse($event)->shouldReturn(null);
    }
    
    function it_should_return_null_if_not_master_request_on_get_response_event (
            GetResponseEvent $event
    ) {
        $event->getRequestType()->willReturn(HttpKernelInterface::SUB_REQUEST);
        $this->onKernelRequest($event)->shouldReturn(null);
    }
    
    function it_should_decrypt_a_cookie (
            GetResponseEvent $event,
            Request $request,
            ParameterBag $cookies
    ) {
        $cookies->get('session')->willReturn('dGVzdA==');
        $cookies->has('session')->willReturn(true);
        $cookies->set('session', 'test')->shouldBeCalled();
        
        $request->cookies = $cookies;
        $event->getRequest()->willReturn($request);
        $event->getRequestType()->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $this->onKernelRequest($event)->shouldReturn(null);
    }
}
