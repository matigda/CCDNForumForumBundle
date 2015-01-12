<?php

/*
 * This file is part of the CCDNForum ForumBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\ForumBundle;
use CCDNForum\ForumBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 *
 * @category CCDNForum
 * @package  ForumBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNForumForumBundle
 *
 * Support for inherited entities:
 *
 * Doctrine resolve target entities code and orm metadata listener based on the code of the Sylius ResourceBundle
 * by (c) Paweł Jędrzejewski and Ivan Molchanov <ivan.molchanov@opensoftdev.ru>.
 *
 * It allows us to define the entities as type "mappedSuperclass" instead of as "entity" and this way it is possible
 * to create inherited entities in other bundles and extend the existing ones.
 */
class CCDNForumForumBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = $this->getModelInterfaces();
        if (!empty($interfaces)) {
            $container->addCompilerPass(
                new ResolveDoctrineTargetEntitiesPass(
                    $interfaces
                )
            );
        }
    }

    /**
     * Target entities resolver configuration (Mapped superclass => Config key pointing to actually used class)
     *
     * @return array
     */
    protected function getModelInterfaces()
    {
        return array(
            'CCDNForum\ForumBundle\Entity\Board'            => 'ccdn_forum_forum.entity.board.class',
            'CCDNForum\ForumBundle\Entity\Category'         => 'ccdn_forum_forum.entity.category.class',
            'CCDNForum\ForumBundle\Entity\Forum'            => 'ccdn_forum_forum.entity.forum.class',
            'CCDNForum\ForumBundle\Entity\Post'             => 'ccdn_forum_forum.entity.post.class',
            'CCDNForum\ForumBundle\Entity\Registry'         => 'ccdn_forum_forum.entity.registry.class',
            'CCDNForum\ForumBundle\Entity\Subscription'     => 'ccdn_forum_forum.entity.subscription.class',
            'CCDNForum\ForumBundle\Entity\Topic'            => 'ccdn_forum_forum.entity.topic.class',
        );
    }
}
