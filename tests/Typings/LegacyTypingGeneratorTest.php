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
use Doctrine\ORM\Mapping\ClassMetadata;
use Nlzet\DoctrineMappingTypings\Doctrine\EntityReader;
use Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity\Address;
use Nlzet\DoctrineMappingTypings\Tests\Util\DoctrineConfigurationFactory;
use Nlzet\DoctrineMappingTypings\Typings\GeneratorConfig;
use Nlzet\DoctrineMappingTypings\Typings\ModelTypingGenerator;
use PHPUnit\Framework\TestCase;

class LegacyTypingGeneratorTest extends TestCase
{
    private EntityManager $entityManager;
    private bool $isLegacyDoctrine;

    protected function setUp(): void
    {
        parent::setUp();

        $doctrineFactory = new DoctrineConfigurationFactory();
        $this->entityManager = $doctrineFactory->createEntityManager($doctrineFactory->createConfiguation());

        $reflection = new \ReflectionClass(ClassMetadata::class);
        $reflectionIntersectionType = $reflection->getMethod('getAssociationMapping')->getReturnType();
        if (null !== $reflectionIntersectionType && (!method_exists($reflectionIntersectionType, 'getName') || 'Doctrine\ORM\Mapping\AssociationMapping' === $reflectionIntersectionType->getName())) {
            $this->isLegacyDoctrine = false;
        } else {
            $this->isLegacyDoctrine = true;
        }
    }

    public function testLegacyDoctrineSupport(): void
    {
        if (!$this->isLegacyDoctrine) {
            static::markTestSkipped('Doctrine version is too new');
        }

        $generatorConfig = new GeneratorConfig();
        $reader = new EntityReader($generatorConfig, $this->entityManager);
        $properties = $reader->getProperties(Address::class);

        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->method('getName')->willReturn(Address::class);
        $classMetadata->method('hasAssociation')->willReturn(false);
        $classMetadata->method('getAssociationMapping')->willReturn([
            'type' => /* ClassMetadata::ONE_TO_ONE */ 1,
            'targetEntity' => Address::class,
        ]);

        $generator = new ModelTypingGenerator($generatorConfig, $classMetadata, $properties);

        static::assertStringContainsString('type', $generator->generate());
    }
}
