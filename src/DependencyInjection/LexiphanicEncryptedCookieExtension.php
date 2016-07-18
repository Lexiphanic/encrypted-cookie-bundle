<?php

namespace Lexiphanic\EncryptedCookieBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LexiphanicEncryptedCookieExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('lexiphanic_encrypted_cookie.cookies', $config['cookies']);
        $container->setParameter('lexiphanic_encrypted_cookie.encryption.service', $config['encryption']['service']);
        
        $listener = new Definition(
            'Lexiphanic\EncryptedCookieBundle\EventListener\EncryptedCookieListener',
            ['%lexiphanic_encrypted_cookie.cookies%', new Reference($config['encryption']['service'])]
        );
        $listener->addTag('kernel.event_listener', ['event' => 'kernal.request', 'method' => 'onKernelRequest', 'priority' => 8192]);
        $listener->addTag('kernel.event_listener', ['event' => 'kernal.response', 'method' => 'onKernelResponse', 'priority' => -8192]);
        $container->setDefinition('lexiphanic.encrypted_cookie_listener', $listener);
    }
}