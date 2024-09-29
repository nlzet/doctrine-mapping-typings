<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Nlzet\DoctrineMappingTypings\Typings\GeneratorConfig;

class EntityReader
{
    public function __construct(
        private readonly GeneratorConfig $generatorConfig,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return \ReflectionProperty[]
     */
    public function getProperties(string $class): array
    {
        if ($this->isExcluded($class)) {
            throw new \InvalidArgumentException(\sprintf('Class %s is excluded', $class));
        }

        $classMetadata = $this->entityManager->getClassMetadata($class);

        $properties = [];
        $reflectionProperties = array_filter($classMetadata->getReflectionProperties());
        foreach ($reflectionProperties as $reflectionProperty) {
            if (0 !== \count($reflectionProperty->getAttributes(Exclude::class))) {
                continue;
            }

            if ($this->generatorConfig->isOnlyExposed() && !$this->isPropertyExposed($reflectionProperty)) {
                continue;
            }

            $properties[] = $reflectionProperty;
        }

        return $properties;
    }

    /**
     * @return array<int, ClassMetadata<object>>
     */
    public function getEntities(): array
    {
        $entities = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $onlyIncluded = array_filter($entities, fn (ClassMetadata $classMetadata) => !$this->isExcluded($classMetadata->getName()));

        uasort($onlyIncluded, static fn (ClassMetadata $a, ClassMetadata $b) => $a->getName() <=> $b->getName());

        return $onlyIncluded;
    }

    public function isValidRegex(string $_input): bool
    {
        try {
            /**
             * todo: psalm fix.
             *
             * @var non-empty-string $attempt
             */
            $attempt = $_input;
            $output = @preg_match($attempt, $attempt);

            if (false === $output) {
                throw new \RuntimeException(\sprintf('Invalid regex pattern: %s', $attempt));
            }

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function isExcluded(string $class): bool
    {
        foreach ($this->generatorConfig->getExcludePatterns() as $excludePattern) {
            if (str_starts_with($excludePattern, '/')) {
                if (!$this->isValidRegex($excludePattern)) {
                    throw new \InvalidArgumentException(\sprintf('Invalid regex pattern: %s', $excludePattern));
                }

                if (1 === preg_match($excludePattern, $class)) {
                    return true;
                }
            } elseif (str_contains($class, $excludePattern)) {
                return true;
            }
        }

        return false;
    }

    private function isPropertyExposed(\ReflectionProperty $reflectionProperty): bool
    {
        if (!$this->generatorConfig->isOnlyExposed() || 'id' === $reflectionProperty->getName()) {
            return true;
        }

        $exclusionPolicies = $reflectionProperty->getDeclaringClass()->getAttributes(ExclusionPolicy::class);
        $exclusionPolicy = (0 === \count($exclusionPolicies))
            ? ExclusionPolicy::NONE
            : strtoupper((string) $exclusionPolicies[0]->getArguments()[0]);

        if (ExclusionPolicy::NONE === $exclusionPolicy) {
            return true;
        }

        return 0 !== \count($reflectionProperty->getAttributes(Expose::class));
    }
}
