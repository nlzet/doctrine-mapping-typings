<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Tests\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Nlzet\DoctrineMappingTypings\Doctrine\EntityReader;
use Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity\Address;
use Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity\ExamplePropertyTypes;
use Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity\Person;
use Nlzet\DoctrineMappingTypings\Tests\Util\DoctrineConfigurationFactory;
use Nlzet\DoctrineMappingTypings\Typings\GeneratorConfig;
use PHPUnit\Framework\TestCase;

class EntityReaderTest extends TestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $doctrineFactory = new DoctrineConfigurationFactory();

        $configuration = $doctrineFactory->createConfiguation();
        $this->entityManager = $doctrineFactory->createEntityManager($configuration);
    }

    public function testGetEntities(): void
    {
        $generatorConfig = new GeneratorConfig();
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $entities = array_map(
            static fn (ClassMetadata $classMetadata) => $classMetadata->getName(),
            $reader->getEntities()
        );
        sort($entities);

        static::assertEqualsCanonicalizing([
            Address::class,
            Person::class,
            ExamplePropertyTypes::class,
        ], $entities);
    }

    public function testGetEntitiesFiltered(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setExcludePatterns(['Person']);
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $entities = array_map(
            static fn (ClassMetadata $classMetadata) => $classMetadata->getName(),
            $reader->getEntities()
        );
        sort($entities);

        static::assertEqualsCanonicalizing([
            Address::class,
            ExamplePropertyTypes::class,
        ], $entities);
    }

    public function testGetEntitiesFilteredWithRegex(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setExcludePatterns(['/.*[P[axe]rson.*/']);
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $entities = array_map(
            static fn (ClassMetadata $classMetadata) => $classMetadata->getName(),
            $reader->getEntities()
        );
        sort($entities);

        static::assertEqualsCanonicalizing([
            Address::class,
            ExamplePropertyTypes::class,
        ], $entities);
    }

    public function testGetEntitiesFilteredWithInvalidRegex(): void
    {
        $this->expectExceptionMessage('Invalid regex pattern: /.*[P[ax');
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setExcludePatterns(['/.*[P[ax']);
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $reader->getEntities();
    }

    public function testReadAddress(): void
    {
        $generatorConfig = new GeneratorConfig();
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $properties = $reader->getProperties(Address::class);
        $propertyNames = array_map(static fn ($property) => $property->getName(), $properties);
        static::assertSame([
            'id',
            'houseNumber',
            'city',
            'zip',
            'country',
            'floor',
            'latitude',
            'longitude',
            'isPrivate',
            'createdAt',
            'updatedAt',
            'createdDate',
        ], $propertyNames);
    }

    public function testReadAddressOnlyExposed(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setOnlyExposed(true);
        $reader = new EntityReader($generatorConfig, $this->entityManager);

        $properties = $reader->getProperties(Address::class);
        $propertyNames = array_map(static fn ($property) => $property->getName(), $properties);
        static::assertSame([
            'id',
            'houseNumber',
        ], $propertyNames);
    }

    public function testReadPerson(): void
    {
        $generatorConfig = new GeneratorConfig();
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $properties = $reader->getProperties(Person::class);
        $propertyNames = array_map(static fn ($property) => $property->getName(), $properties);
        static::assertSame([
            'id',
            'name',
            'extraData',
            'createdAt',
            'updatedAt',
            'createdDate',
            'addresses',
        ], $propertyNames);
    }

    public function testReadPersonOnlyExposed(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setOnlyExposed(true);
        $reader = new EntityReader($generatorConfig, $this->entityManager);

        $properties = $reader->getProperties(Person::class);
        $propertyNames = array_map(static fn ($property) => $property->getName(), $properties);

        // person has no exclusion policy.
        static::assertSame([
            'id',
            'name',
            'extraData',
            'createdAt',
            'updatedAt',
            'createdDate',
            'addresses',
        ], $propertyNames);
    }

    public function testReadExcludedPerson(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setExcludePatterns(['Person']);
        $generatorConfig->setOnlyExposed(true);
        $reader = new EntityReader($generatorConfig, $this->entityManager);

        $this->expectExceptionMessage(\sprintf('Class %s is excluded', Person::class));
        $reader->getProperties(Person::class);
    }

    public function testReadExcludedRegex(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setExcludePatterns(['/.*[P[axe]rson.*/']);
        $generatorConfig->setOnlyExposed(true);
        $reader = new EntityReader($generatorConfig, $this->entityManager);

        $this->expectExceptionMessage(\sprintf('Class %s is excluded', Person::class));
        $reader->getProperties(Person::class);
    }
}
