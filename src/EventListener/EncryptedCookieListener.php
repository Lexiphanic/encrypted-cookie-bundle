<?php

namespace Lexiphanic\EncryptedCookieBundle\EventListener;

use Lexiphanic\EncryptedCookieBundle\Encryption\EncryptionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class EncryptedCookieListener
{
    /**
     * Array of cookies to encrypt/decrypt
     * @var string[]
     */
    private $cookieNames;
    
    /**
     * Used for Encrypting and Decrypting
     * @var EncryptionInterface 
     */
    private $encrypter;
    
    public function __construct(array $cookieNames = [], EncryptionInterface $encrypter = null)
    {
        $this->cookieNames = $cookieNames;
        $this->encrypter = $encrypter;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // Only run on the Master request
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }
        
        // Get the response and encrypt the requested cookies
        $response = $event->getResponse();
        foreach ($response->headers->getCookies() as $cookie) {
            if (!$this->isEncryptedable($cookie->getName())) {
                continue;
            }
            $response->headers->removeCookie($cookie->getName(), $cookie->getPath(), $cookie->getDomain());
            $encryptedCookie = new Cookie(
                $cookie->getName(),
                $this->encrypter->encrypt($cookie->getValue()),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
            $response->headers->setCookie($encryptedCookie, $cookie->getPath(), $cookie->getDomain());
        }
    }

    public function onKernelRequest (GetResponseEvent $e)
    {
        // Only run on the Master request
        if (HttpKernelInterface::MASTER_REQUEST !== $e->getRequestType()) {
            return;
        }
        // Get the request and decrypt the requested cookies
        $request = $e->getRequest();
        foreach ($this->cookieNames as $name) {
            if (!$request->cookies->has($name)) {
                continue;
            }
            $value = $this->encrypter->decrypt($request->cookies->get($name));
            $request->cookies->set($name, $value);
        }
    }

    private function isEncryptedable ($name)
    {
        return in_array($name, $this->cookieNames, true);
    }

    public function setEncrypter(EncryptionInterface $encrypter = null)
    {
        $this->encrypter = $encrypter;
    }

    public function getEncrypter()
    {
        return $this->encrypter;
    }

}
