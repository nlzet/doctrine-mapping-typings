<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Nlzet\DoctrineMappingTypings\Doctrine\EntityReader;
use Nlzet\DoctrineMappingTypings\Typings\GeneratorConfig;
use Nlzet\DoctrineMappingTypings\Typings\ModelTypingGenerator;

require_once __DIR__.'/../vendor/autoload.php';
$generatorConfig = new GeneratorConfig();

// config
$entitiesPath = __DIR__.'/../tests/Fixture/Entity/';
$entityNamespace = 'Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity';
$generatorConfig->setExcludePatterns([]);
$generatorConfig->setOnlyExposed(true);
$generatorConfig->setClassAliases(['NlzetDoctrineMappingTypingsTestsFixtureEntityAddress' => 'NlzetCustomAddress']);
$generatorConfig->setClassReplacements(['NlzetDoctrineMappingTypingsTestsFixtureEntity' => 'Nlzet']);

// doctrine setup
$configuration = new Configuration();
$mappingDriver = new MappingDriverChain();
$realpath = realpath($entitiesPath);
if (false === $realpath) {
    throw new RuntimeException(sprintf('Entity mapping path %s does not exist', $entitiesPath));
}
$attributeDriver = new AttributeDriver([$realpath]);
$mappingDriver->addDriver($attributeDriver, $entityNamespace);
$configuration->setMetadataDriverImpl($mappingDriver);
$configuration->setProxyDir(__DIR__.'/__proxy__');
$configuration->setProxyNamespace('Nlzet\DoctrineMappingTypings\Proxy');
$configuration->setAutoGenerateProxyClasses(true);
$connection = new Connection([], new Driver());
$eventManager = new EventManager();
$entityManager = new EntityManager($connection, $configuration, $eventManager);

// generate typings.
$outputs = [];
$reader = new EntityReader($generatorConfig, $entityManager);
foreach ($reader->getEntities() as $classMeta) {
    $outputs[] = (new ModelTypingGenerator($generatorConfig, $classMeta, $reader->getProperties($classMeta->getName())))->generate();
}

// output or save to file.
$final = implode(\PHP_EOL, $outputs);
echo $final.\PHP_EOL;
// file_put_contents(__DIR__ . '/output.ts', $final.PHP_EOL);
