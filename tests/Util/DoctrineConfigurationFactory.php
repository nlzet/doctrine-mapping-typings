<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Tests\Util;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;

class DoctrineConfigurationFactory
{
    public function createConfiguation(): Configuration
    {
        $configuration = new Configuration();
        $mappingDriver = new MappingDriverChain();

        $fixturePath = (__DIR__.'/../Fixture/Entity/');
        $realpath = realpath($fixturePath);
        if (false === $realpath) {
            throw new \RuntimeException(\sprintf('Fixture path %s does not exist', $fixturePath));
        }

        $attributeDriver = new AttributeDriver([$realpath]);
        $mappingDriver->addDriver($attributeDriver, 'Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity');
        $configuration->setMetadataDriverImpl($mappingDriver);
        $configuration->setProxyDir(__DIR__.'/../__proxy__');
        $configuration->setProxyNamespace('Nlzet\DoctrineMappingTypings\Proxy');
        $configuration->setAutoGenerateProxyClasses(true);

        return $configuration;
    }

    public function createEntityManager(Configuration $configuration): EntityManager
    {
        $connection = new Connection([], new Driver());
        $eventManager = new EventManager();

        return new EntityManager($connection, $configuration, $eventManager);
    }
}
