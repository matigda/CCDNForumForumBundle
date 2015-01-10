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
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
 * Doctrine mapping and resolve target entities code based on the code of the Sylius ResourceBundle
 * (c) Paweł Jędrzejewski.
 */
class CCDNForumForumBundle extends Bundle
{
    const DRIVER_DOCTRINE_ORM         = 'doctrine/orm';
    const DRIVER_DOCTRINE_MONGODB_ODM = 'doctrine/mongodb-odm';
    const DRIVER_DOCTRINE_PHPCR_ODM   = 'doctrine/phpcr-odm';

    const MAPPING_XML = 'xml';
    const MAPPING_YAML = 'yml';
    const MAPPING_ANNOTATION = 'annotation';

    /**
     * Configure format of mapping files.
     *
     * @var string
     */
    protected $mappingFormat = self::MAPPING_YAML;

    public static function getSupportedDrivers()
    {
        return array(
            self::DRIVER_DOCTRINE_ORM,
        );
    }

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
        if (null !== $this->getModelNamespace()) {
            foreach (self::getSupportedDrivers() as $driver) {
                $mappingsPassClassName = $this->getMappingDriverInfo($driver);
                if (class_exists($mappingsPassClassName)) {
                    switch ($this->mappingFormat){
                        case self::MAPPING_XML:
                            $container->addCompilerPass($mappingsPassClassName::createXmlMappingDriver(
                                array($this->getConfigFilesPath() => $this->getModelNamespace()),
                                array(sprintf('%s.object_manager', $this->getBundlePrefix())),
                                false //sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));
                            break;
                        case self::MAPPING_YAML:
                            $container->addCompilerPass($mappingsPassClassName::createYamlMappingDriver(
                                array($this->getConfigFilesPath() => $this->getModelNamespace()),
                                array(sprintf('%s.object_manager', $this->getBundlePrefix())),
                                false //sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));
                            break;
                        case self::MAPPING_ANNOTATION:
                            $container->addCompilerPass($mappingsPassClassName::createAnnotationMappingDriver(
                                array($this->getModelNamespace()),
                                array($this->getConfigFilesPath()),
                                array(sprintf('%s.object_manager', $this->getBundlePrefix())),
                                false //sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));
                            break;
                        default:
                            throw new InvalidConfigurationException("The 'mappingFormat' value is invalid, must be 'xml', 'yml' or 'annotation'.");
                    }
                }
            }
        }
    }

    /**
     * Return the prefix of the bundle.
     *
     * @return string
     */
    protected function getBundlePrefix()
    {
        return Container::underscore(substr(strrchr(get_class($this), '\\'), 1, -6));
    }

    /**
     * Target entities resolver configuration (Interface - Model).
     *
     * @return array
     */
    protected function getModelInterfaces()
    {
        return array(
            'CCDNForum\ForumBundle\Entity\Board'            => 'ccdn_forum_forum.entity.board.class',
            ////'CCDNForum\ForumBundle\Entity\Topic'            => 'ccdn_forum_forum.entity.topic.class',
        );
    }

    /**
     * Return the directory where are stored the doctrine mapping.
     *
     * @return string
     */
    protected function getDoctrineMappingDirectory()
    {
        return 'model';
    }

    protected function getModelNamespace()
    {
        return 'CCDNForum\ForumBundle\Entity';
    }

    /**
     * Return information's used to initialize mapping driver.
     *
     * @param string $driverType
     *
     * @return array
     *
     * @throws UnknownDriverException
     */
    protected function getMappingDriverInfo($driverType)
    {
        switch ($driverType) {
            case self::DRIVER_DOCTRINE_MONGODB_ODM:
                return 'Doctrine\\Bundle\\MongoDBBundle\\DependencyInjection\\Compiler\\DoctrineMongoDBMappingsPass';
            case self::DRIVER_DOCTRINE_ORM:
                return 'Doctrine\\Bundle\\DoctrineBundle\\DependencyInjection\\Compiler\\DoctrineOrmMappingsPass';
            case self::DRIVER_DOCTRINE_PHPCR_ODM:
                return 'Doctrine\\Bundle\\PHPCRBundle\\DependencyInjection\\Compiler\\DoctrinePhpcrMappingsPass';
        }
        throw new UnknownDriverException($driverType);
    }
    /**
     * Return the absolute path where are stored the doctrine mapping.
     *
     * @return string
     */
    protected function getConfigFilesPath()
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            strtolower($this->getDoctrineMappingDirectory())
        );
    }
}
