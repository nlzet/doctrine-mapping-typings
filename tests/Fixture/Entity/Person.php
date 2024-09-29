<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use JMS\Serializer\Annotation\Expose;

#[Entity]
class Person
{
    #[Id]
    #[Column(type: 'integer')]
    #[Expose]
    protected ?int $id = null;

    #[Column(type: 'string')]
    #[Expose]
    protected ?string $name = null;

    #[Column(type: 'simple_array', nullable: false)]
    #[Expose]
    protected array $extraData = [];

    #[OneToMany(targetEntity: Address::class, mappedBy: 'person')]
    #[JoinColumn(name: 'person_id', referencedColumnName: 'id')]
    #[Expose]
    protected Collection $addresses;

    #[Column(type: 'datetime')]
    protected ?\DateTime $createdAt = null;

    #[Column(type: 'datetime')]
    protected ?\DateTime $updatedAt = null;

    #[Column(type: 'timestamp')]
    protected ?int $createdDate = null;

    protected ?string $notMapped = null;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }
}
