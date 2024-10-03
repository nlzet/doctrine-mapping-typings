<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Tests\Typings;

use Doctrine\ORM\EntityManager;
use Nlzet\DoctrineMappingTypings\Doctrine\EntityReader;
use Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity\Address;
use Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity\Person;
use Nlzet\DoctrineMappingTypings\Tests\Util\DoctrineConfigurationFactory;
use Nlzet\DoctrineMappingTypings\Typings\GeneratorConfig;
use Nlzet\DoctrineMappingTypings\Typings\ModelTypingGenerator;
use PHPUnit\Framework\TestCase;

class ModelTypingGeneratorTest extends TestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $doctrineFactory = new DoctrineConfigurationFactory();
        $this->entityManager = $doctrineFactory->createEntityManager($doctrineFactory->createConfiguation());
    }

    public function testNullableAndOptional(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setTreatOptionalAsNullable(false);
        $generatorConfig->setTreatNullableAsOptional(true);
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $properties = $reader->getProperties(Address::class);

        $classMetadata = $this->entityManager->getClassMetadata(Address::class);
        $generator = new ModelTypingGenerator($generatorConfig, $classMetadata, $properties);

        $output = $generator->generate();
        static::assertSame('export type NlzetDoctrineMappingTypingsTestsFixtureEntityAddress = {
    id: number;
    houseNumber?: string;
    city: string;
    zip: string;
    country?: string;
    floor?: number;
    latitude?: number;
    longitude?: number;
    isPrivate: boolean;
    createdAt: any;
    updatedAt: any;
    createdDate: number;
};
', $output);

        $generatorConfig->setTreatNullableAsOptional(false);
        $output = $generator->generate();
        static::assertSame('export type NlzetDoctrineMappingTypingsTestsFixtureEntityAddress = {
    id: number;
    houseNumber: string;
    city: string;
    zip: string;
    country: string;
    floor: number;
    latitude: number;
    longitude: number;
    isPrivate: boolean;
    createdAt: any;
    updatedAt: any;
    createdDate: number;
};
', $output);

        $generatorConfig->setAlwaysOptional(true);
        $output = $generator->generate();
        static::assertSame('export type NlzetDoctrineMappingTypingsTestsFixtureEntityAddress = {
    id?: number;
    houseNumber?: string;
    city?: string;
    zip?: string;
    country?: string;
    floor?: number;
    latitude?: number;
    longitude?: number;
    isPrivate?: boolean;
    createdAt?: any;
    updatedAt?: any;
    createdDate?: number;
};
', $output);
    }

    public function testNullable(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setTreatOptionalAsNullable(true);
        $generatorConfig->setTreatNullableAsOptional(true);
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $properties = $reader->getProperties(Address::class);

        $classMetadata = $this->entityManager->getClassMetadata(Address::class);
        $generator = new ModelTypingGenerator($generatorConfig, $classMetadata, $properties);

        $output = $generator->generate();
        static::assertSame('type Nullable<T> = T | null;

export type NlzetDoctrineMappingTypingsTestsFixtureEntityAddress = {
    id: number;
    houseNumber?: Nullable<string>;
    city: string;
    zip: string;
    country?: Nullable<string>;
    floor?: Nullable<number>;
    latitude?: Nullable<number>;
    longitude?: Nullable<number>;
    isPrivate: boolean;
    createdAt: any;
    updatedAt: any;
    createdDate: number;
};
', $output);

        $generatorConfig->setAlwaysOptional(true);
        $output = $generator->generate();
        static::assertSame('type Nullable<T> = T | null;

export type NlzetDoctrineMappingTypingsTestsFixtureEntityAddress = {
    id?: Nullable<number>;
    houseNumber?: Nullable<string>;
    city?: Nullable<string>;
    zip?: Nullable<string>;
    country?: Nullable<string>;
    floor?: Nullable<number>;
    latitude?: Nullable<number>;
    longitude?: Nullable<number>;
    isPrivate?: Nullable<boolean>;
    createdAt?: Nullable<any>;
    updatedAt?: Nullable<any>;
    createdDate?: Nullable<number>;
};
', $output);
    }

    public function testGenerateAddress(): void
    {
        $generatorConfig = new GeneratorConfig();
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $properties = $reader->getProperties(Address::class);

        $classMetadata = $this->entityManager->getClassMetadata(Address::class);
        $generator = new ModelTypingGenerator($generatorConfig, $classMetadata, $properties);
        $output = $generator->generate();

        static::assertSame('export type NlzetDoctrineMappingTypingsTestsFixtureEntityAddress = {
    id: number;
    houseNumber?: string;
    city: string;
    zip: string;
    country?: string;
    floor?: number;
    latitude?: number;
    longitude?: number;
    isPrivate: boolean;
    createdAt: any;
    updatedAt: any;
    createdDate: number;
};
', $output);
    }

    public function testGeneratePerson(): void
    {
        $generatorConfig = new GeneratorConfig();
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $properties = $reader->getProperties(Person::class);

        $classMetadata = $this->entityManager->getClassMetadata(Person::class);
        $generator = new ModelTypingGenerator($generatorConfig, $classMetadata, $properties);
        $output = $generator->generate();

        static::assertSame('export type NlzetDoctrineMappingTypingsTestsFixtureEntityPerson = {
    id: number;
    name: string;
    extraData: any[];
    createdAt: any;
    updatedAt: any;
    createdDate: number;
    addresses: NlzetDoctrineMappingTypingsTestsFixtureEntityAddress[];
};
', $output);
    }

    public function testGeneratePersonWithAliases(): void
    {
        $generatorConfig = new GeneratorConfig();
        $generatorConfig->setClassAliases(['NlzetDoctrineMappingTypingsTestsFixtureEntityAddress' => 'NlzetCustomAddress']);
        $generatorConfig->setClassReplacements(['NlzetDoctrineMappingTypingsTestsFixtureEntity' => 'Nlzet']);
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $properties = $reader->getProperties(Person::class);

        $classMetadata = $this->entityManager->getClassMetadata(Person::class);
        $generator = new ModelTypingGenerator($generatorConfig, $classMetadata, $properties);
        $output = $generator->generate();

        static::assertSame('export type NlzetPerson = {
    id: number;
    name: string;
    extraData: any[];
    createdAt: any;
    updatedAt: any;
    createdDate: number;
    addresses: NlzetCustomAddress[];
};
', $output);
    }
}
