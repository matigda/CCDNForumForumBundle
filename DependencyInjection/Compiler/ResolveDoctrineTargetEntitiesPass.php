<?php

/*
 * This file is based on the code of the Sylius ResourceBundle.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\ForumBundle\DependencyInjection\Compiler;

use CCDNForum\ForumBundle\DependencyInjection\DoctrineTargetEntitiesResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Resolves given target entities with container parameters.
 * Usable only with *doctrine/orm* driver.
 *
 * Based on the code of the Sylius ResourceBundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResolveDoctrineTargetEntitiesPass implements CompilerPassInterface
{
    /**
     * @var array $interfaces
     */
    private $interfaces;

    public function __construct(array $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $resolver = new DoctrineTargetEntitiesResolver();
        $resolver->resolve($container, $this->interfaces);
    }
}
