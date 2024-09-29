<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Typings;

class GeneratorConfig
{
    /**
     * @var string[]
     */
    private array $excludePatterns = [];

    /**
     * @var string[]
     */
    private array $classAliases = [];

    /**
     * @var string[]
     */
    private array $classReplacements = [];

    private bool $onlyExposed = false;

    /**
     * @return string[]
     */
    public function getExcludePatterns(): array
    {
        return $this->excludePatterns;
    }

    /**
     * @param string[] $excludePatterns
     */
    public function setExcludePatterns(array $excludePatterns): void
    {
        $this->excludePatterns = $excludePatterns;
    }

    public function isOnlyExposed(): bool
    {
        return $this->onlyExposed;
    }

    public function setOnlyExposed(bool $onlyExposed): void
    {
        $this->onlyExposed = $onlyExposed;
    }

    /**
     * @return string[]
     */
    public function getClassAliases(): array
    {
        return $this->classAliases;
    }

    /**
     * @param string[] $classAliases
     */
    public function setClassAliases(array $classAliases): void
    {
        $this->classAliases = $classAliases;
    }

    /**
     * @return string[]
     */
    public function getClassReplacements(): array
    {
        return $this->classReplacements;
    }

    /**
     * @param string[] $classReplacements
     */
    public function setClassReplacements(array $classReplacements): void
    {
        $this->classReplacements = $classReplacements;
    }
}
