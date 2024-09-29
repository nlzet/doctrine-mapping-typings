<?php

declare(strict_types=1);

/*
 * (c) Niels Verbeek <niels@kreable.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nlzet\DoctrineMappingTypings\Tests\Fixture\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

#[Entity]
#[ExclusionPolicy('ALL')]
class Address
{
    #[Id]
    #[Column(type: 'integer')]
    #[Expose]
    protected ?int $id = null;

    #[ManyToOne(targetEntity: Person::class, inversedBy: 'addresses')]
    #[Exclude]
    protected ?Person $person = null;

    #[Column(type: 'string')]
    #[Exclude]
    protected ?string $street = null;

    #[Column(type: 'string', nullable: true)]
    #[Expose]
    protected ?string $houseNumber = null;

    #[Column(type: 'string')]
    protected ?string $city = null;

    #[Column(type: 'string')]
    protected ?string $zip = null;

    #[Column(type: 'string', nullable: true)]
    protected ?string $country = null;

    #[Column(type: 'integer', nullable: true)]
    protected ?string $floor = null;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    protected ?string $latitude = null;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    protected ?string $longitude = null;

    #[Column(type: 'boolean')]
    protected bool $isPrivate = false;

    #[Column(type: 'datetime')]
    protected ?\DateTime $createdAt = null;

    #[Column(type: 'datetime')]
    protected ?\DateTime $updatedAt = null;

    #[Column(type: 'timestamp')]
    protected ?int $createdDate = null;

    protected ?string $notMapped = null;
}
