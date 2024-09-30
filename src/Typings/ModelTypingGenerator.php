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

class ModelTypingGenerator
{
    /**
     * @param ClassMetadata<object> $classMeta
     * @param \ReflectionProperty[] $properties
     */
    public function __construct(
        private readonly GeneratorConfig $generatorConfig,
        private readonly ClassMetadata $classMeta,
        private readonly array $properties,
    ) {
    }

    public function generate(): string
    {
        $className = $this->classMeta->getName();
        $result = 'export type '.$this->getClassAlias($className).' = {'.\PHP_EOL;
        foreach ($this->properties as $property) {
            $result .= $this->generateProperty($property);
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

    public function generateProperty(\ReflectionProperty $property): string
    {
        return \sprintf(
            "    %s%s: %s;\n",
            $property->getName(),
            $this->getNullable($property),
            $this->getType($property)
        );
    }

    /**
     * Type support helper for older doctrine versions returning an array.
     *
     * @return array<string, mixed>|AssociationMapping
     */
    public function getAssociationMapping(string $class): mixed/* : array|AssociationMapping */
    {
        /** @var array<string, mixed>|AssociationMapping $associationMapping */
        $associationMapping = $this->classMeta->getAssociationMapping($class);

        return $associationMapping;
    }

    public function isAssociationToOne(\ReflectionProperty $property): bool
    {
        $associationMapping = $this->getAssociationMapping($property->getName());

        // @codeCoverageIgnoreStart
        return \is_array($associationMapping)
            ? ClassMetadata::ONE_TO_ONE === $associationMapping['type'] || ClassMetadata::MANY_TO_ONE === $associationMapping['type']
            : $associationMapping->isManyToOne() || $associationMapping->isOneToOne();
        // @codeCoverageIgnoreEnd
    }

    public function getTargetEntity(\ReflectionProperty $property): string
    {
        $associationMapping = $this->getAssociationMapping($property->getName());
        $targetEntity = \is_array($associationMapping) ? $associationMapping['targetEntity'] : $associationMapping->targetEntity;

        // @codeCoverageIgnoreStart
        if (!\is_string($targetEntity)) {
            throw new \RuntimeException(\sprintf('Missing target entity for association mapping "%s" in class "%s"', $property->getName(), $this->classMeta->getName()));
        }
        // @codeCoverageIgnoreEnd

        return $targetEntity;
    }

    private function getType(\ReflectionProperty $property): string
    {
        if ($this->classMeta->hasAssociation($property->getName())) {
            $suffix = $this->isAssociationToOne($property) ? '' : '[]';

            return $this->getClassAlias($this->getTargetEntity($property)).$suffix;
        }

        $doctrineType = $this->classMeta->getTypeOfField($property->getName());

        return match ($doctrineType) {
            'int', 'integer', 'float', 'decimal', 'time', 'time_immutable', 'timestamp', 'timestamp_immutable' => 'number',
            'bool', 'boolean' => 'boolean',
            'string', 'text', 'guid' => 'string',
            null, 'datetime', 'datetime_immutable', 'date', 'date_immutable', 'object', 'blob' => 'any',
            'array', 'simple_array', 'json', 'json_array' => 'any[]',
            default => throw new \RuntimeException(\sprintf('Unsupported type doctrine property "%s" (%s) in class "%s"', $doctrineType, $property->getName(), $this->classMeta->getName())),
        };
    }

    private function getNullable(\ReflectionProperty $property): string
    {
        if ($this->classMeta->hasAssociation($property->getName())) {
            return $this->isAssociationToOne($property) ? '?' : '';
        }

        if ('any' === $this->getType($property)) {
            return '';
        }

        return $this->classMeta->isNullable($property->getName()) ? '?' : '';
    }
}
