<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Typings;

use Doctrine\ORM\Mapping\ClassMetadata;

interface ModelTypingGeneratorInterface
{
    /**
     * @param ClassMetadata<object> $classMeta
     * @param \ReflectionProperty[] $properties
     */
    public function generate(ClassMetadata $classMeta, array $properties): string;
}
