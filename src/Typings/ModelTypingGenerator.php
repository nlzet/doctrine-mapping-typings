<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Typings;

use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;

class ModelTypingGenerator implements ModelTypingGeneratorInterface
{
    public function __construct(
        private readonly GeneratorConfig $generatorConfig,
    ) {
    }

    public function generate(ClassMetadata $classMeta, array $properties): string
    {
        $className = $classMeta->getName();

        $result = '';
        if ($this->generatorConfig->isTreatOptionalAsNullable()) {
            $result .= 'type Nullable<T> = T | null;'.\PHP_EOL.\PHP_EOL;
        }

        $result .= 'export type '.$this->getClassAlias($className).' = {'.\PHP_EOL;
        foreach ($properties as $property) {
            $result .= $this->generateProperty($classMeta, $property);
        }
        $result .= '};'.\PHP_EOL;

        return $result;
    }

    public function getClassAlias(string $class): string
    {
        $stripped = str_replace('\\', '', $class);

        $classAliases = $this->generatorConfig->getClassAliases();
        if (isset($classAliases[$stripped])) {
            return $classAliases[$stripped];
        }

        if (str_contains($class, '\\')) {
            foreach ($this->generatorConfig->getClassReplacements() as $search => $replacement) {
                $stripped = str_replace($search, $replacement, $stripped);
            }
        }

        return $stripped;
    }

    /**
     * @param ClassMetadata<object> $classMeta
     */
    public function generateProperty(ClassMetadata $classMeta, \ReflectionProperty $property): string
    {
        $nullableStart = '';
        $nullableEnd = '';
        if ($this->generatorConfig->isTreatOptionalAsNullable() && '?' === $this->getOptional($classMeta, $property)) {
            $nullableStart = 'Nullable<';
            $nullableEnd = '>';
        }

        return \sprintf(
            "    %s%s: %s%s%s;\n",
            $property->getName(),
            $this->getOptional($classMeta, $property),
            $nullableStart,
            $this->getType($classMeta, $property),
            $nullableEnd
        );
    }

    /**
     * Type support helper for older doctrine versions returning an array.
     *
     * @param ClassMetadata<object> $classMeta
     *
     * @return array<string, mixed>|AssociationMapping
     */
    public function getAssociationMapping(ClassMetadata $classMeta, string $class): mixed/* : array|AssociationMapping */
    {
        /** @var array<string, mixed>|AssociationMapping $associationMapping */
        $associationMapping = $classMeta->getAssociationMapping($class);

        return $associationMapping;
    }

    /**
     * @param ClassMetadata<object> $classMeta
     */
    public function isAssociationToOne(ClassMetadata $classMeta, \ReflectionProperty $property): bool
    {
        $associationMapping = $this->getAssociationMapping($classMeta, $property->getName());

        // @codeCoverageIgnoreStart
        return \is_array($associationMapping)
            ? ClassMetadata::ONE_TO_ONE === $associationMapping['type'] || ClassMetadata::MANY_TO_ONE === $associationMapping['type']
            : $associationMapping->isManyToOne() || $associationMapping->isOneToOne();
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param ClassMetadata<object> $classMeta
     */
    public function getTargetEntity(ClassMetadata $classMeta, \ReflectionProperty $property): string
    {
        $associationMapping = $this->getAssociationMapping($classMeta, $property->getName());
        $targetEntity = \is_array($associationMapping) ? $associationMapping['targetEntity'] : $associationMapping->targetEntity;

        // @codeCoverageIgnoreStart
        if (!\is_string($targetEntity)) {
            throw new \RuntimeException(\sprintf('Missing target entity for association mapping "%s" in class "%s"', $property->getName(), $classMeta->getName()));
        }
        // @codeCoverageIgnoreEnd

        return $targetEntity;
    }

    /**
     * @param ClassMetadata<object> $classMeta
     */
    public function getType(ClassMetadata $classMeta, \ReflectionProperty $property): string
    {
        if ($classMeta->hasAssociation($property->getName())) {
            $suffix = $this->isAssociationToOne($classMeta, $property) ? '' : '[]';

            return $this->getClassAlias($this->getTargetEntity($classMeta, $property)).$suffix;
        }

        $doctrineType = $classMeta->getTypeOfField($property->getName());

        return match ($doctrineType) {
            'int', 'integer', 'float', 'decimal', 'time', 'time_immutable', 'timestamp', 'timestamp_immutable' => 'number',
            'bool', 'boolean' => 'boolean',
            'string', 'text', 'guid' => 'string',
            null, 'datetime', 'datetime_immutable', 'date', 'date_immutable', 'object', 'blob' => 'any',
            'array', 'simple_array', 'json', 'json_array' => 'any[]',
            default => throw new \RuntimeException(\sprintf('Unsupported type doctrine property "%s" (%s) in class "%s"', $doctrineType, $property->getName(), $classMeta->getName())),
        };
    }

    /**
     * @param ClassMetadata<object> $classMeta
     */
    public function getOptional(ClassMetadata $classMeta, \ReflectionProperty $property): string
    {
        if ($this->generatorConfig->isAlwaysOptional()) {
            return '?';
        }

        // treat associations differently than fields.
        if ($classMeta->hasAssociation($property->getName())) {
            // always nullable for to-one associations
            return $this->isAssociationToOne($classMeta, $property) ? '?' : '';
        }

        // any type is always nullable.
        if ('any' === $this->getType($classMeta, $property)) {
            return '';
        }

        return $this->generatorConfig->isTreatNullableAsOptional() && $classMeta->isNullable($property->getName()) ? '?' : '';
    }

    public function getGeneratorConfig(): GeneratorConfig
    {
        return $this->generatorConfig;
    }
}
