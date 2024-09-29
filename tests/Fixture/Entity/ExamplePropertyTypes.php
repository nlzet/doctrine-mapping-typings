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
use JMS\Serializer\Annotation\ExclusionPolicy;

#[Entity]
#[ExclusionPolicy('NONE')]
class ExamplePropertyTypes
{
    #[Id]
    #[Column(type: 'integer')]
    protected ?int $id = null;

    #[Column(type: 'string', nullable: false)]
    protected string $stringDefault = '';

    #[Column(type: 'string', nullable: true)]
    protected ?string $stringNullable = null;

    #[Column(type: 'integer', nullable: false)]
    protected int $integerDefault = 0;

    #[Column(type: 'integer', nullable: true)]
    protected ?int $integerNullable = null;

    #[Column(type: 'float', nullable: false)]
    protected float $floatDefault = 0.0;

    #[Column(type: 'float', nullable: true)]
    protected ?float $floatNullable = null;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    protected ?float $decimalDefault;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    protected ?float $decimalNullable = null;

    #[Column(type: 'boolean', nullable: false)]
    protected bool $booleanDefault = false;

    #[Column(type: 'boolean', nullable: true)]
    protected ?bool $booleanNullable = null;

    #[Column(type: 'datetime', nullable: false)]
    protected \DateTime $datetimeDefault;

    #[Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $datetimeNullable = null;

    #[Column(type: 'timestamp', nullable: false)]
    protected int $timestampDefault = 0;

    #[Column(type: 'timestamp', nullable: true)]
    protected ?int $timestampNullable = null;

    #[Column(type: 'array', nullable: false)]
    protected array $arrayDefault = [];

    #[Column(type: 'array', nullable: true)]
    protected ?array $arrayNullable = null;

    #[Column(type: 'simple_array', nullable: false)]
    protected array $simpleArrayDefault = [];

    #[Column(type: 'simple_array', nullable: true)]
    protected ?array $simpleArrayNullable = null;

    #[Column(type: 'json', nullable: false)]
    protected array $jsonDefault = [];

    #[Column(type: 'json', nullable: true)]
    protected ?array $jsonNullable = null;

    #[Column(type: 'object', nullable: false)]
    protected object $objectDefault;

    #[Column(type: 'object', nullable: true)]
    protected ?object $objectNullable = null;

    #[Column(type: 'blob', nullable: false)]
    protected string $blobDefault = '';

    #[Column(type: 'blob', nullable: true)]
    protected ?string $blobNullable = null;

    #[Column(type: 'guid', nullable: false)]
    protected string $guidDefault = '';

    #[Column(type: 'guid', nullable: true)]
    protected ?string $guidNullable = null;

    #[Column(type: 'date', nullable: false)]
    protected \DateTime $dateDefault;

    #[Column(type: 'date', nullable: true)]
    protected ?\DateTime $dateNullable = null;

    #[Column(type: 'time', nullable: false)]
    protected \DateTime $timeDefault;

    #[Column(type: 'time', nullable: true)]
    protected ?\DateTime $timeNullable = null;

    #[Column(type: 'datetime_immutable', nullable: false)]
    protected \DateTimeImmutable $datetimeImmutableDefault;

    #[Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $datetimeImmutableNullable = null;

    #[Column(type: 'timestamp_immutable', nullable: false)]
    protected int $timestampImmutableDefault = 0;

    #[Column(type: 'timestamp_immutable', nullable: true)]
    protected ?int $timestampImmutableNullable = null;

    #[Column(type: 'date_immutable', nullable: false)]
    protected \DateTimeImmutable $dateImmutableDefault;

    #[Column(type: 'date_immutable', nullable: true)]
    protected ?\DateTimeImmutable $dateImmutableNullable = null;

    #[Column(type: 'time_immutable', nullable: false)]
    protected \DateTimeImmutable $timeImmutableDefault;

    #[Column(type: 'time_immutable', nullable: true)]
    protected ?\DateTimeImmutable $timeImmutableNullable = null;

    public function __construct()
    {
        $this->datetimeDefault = new \DateTime();
        $this->datetimeImmutableDefault = new \DateTimeImmutable();
        $this->dateDefault = new \DateTime();
        $this->dateImmutableDefault = new \DateTimeImmutable();
        $this->timeDefault = new \DateTime();
        $this->timeImmutableDefault = new \DateTimeImmutable();
        $this->objectDefault = new \stdClass();
        $this->decimalDefault = 0.0;
    }
}
