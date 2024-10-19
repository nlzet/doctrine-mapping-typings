Doctrine mapping typings generator
==================================

## About

This is a library to create typescript typings based on your doctrine mapping.

## Installation

Install with composer:
```bash
composer require nlzet/doctrine-mapping-typings
```

## Configuration

```php
// Exclude patterns: Add regex or string patterns of FQCNs to exclude from the typings (don't add \ in the search patterns).
$generatorConfig->setExcludePatterns(['/P[ea]rson/', 'Keyword']);
// Class aliases: Add class aliases to replace the original class name with a custom name. Class aliases take precedence over class replacements.
$generatorConfig->setClassAliases(['NlzetDoctrineMappingTypingsTestsFixtureEntityAddress' => 'NlzetCustomAddress']);
// Class replacements: Add class replacements key-value pairs to replace the original class name with a custom name.
$generatorConfig->setClassReplacements(['NlzetDoctrineMappingTypingsTestsFixtureEntity' => 'Nlzet']);
// Only exposed: Set to true to only generate typings for exposed properties, defined by JMS Serializer Expose/Exclude and ExclusionPolicy.
$generatorConfig->setOnlyExposed(true);
// Only exposed: Set to true to always set properties as optional.
$generatorConfig->setAlwaysOptional(true);
// Only exposed: Set to true to treat nullable properties as optional type properties.
$generatorConfig->setTreatNullableAsOptional(true);
```

## Example

See the [example](example/) directory for a full example, using [these example entities](tests/Fixture/Entity/).
Run the example yourself with `php example/basic.php`

### Input code
```php
use Doctrine\ORM\EntityManager;
use Nlzet\DoctrineMappingTypings\Doctrine\EntityReader;
use Nlzet\DoctrineMappingTypings\Typings\GeneratorConfig;
use Nlzet\DoctrineMappingTypings\Typings\ModelTypingGenerator;

$generatorConfig = new GeneratorConfig();
$generatorConfig->setExcludePatterns([]);
$generatorConfig->setOnlyExposed(false);
$generatorConfig->setTreatOptionalAsNullable(false);
$generatorConfig->setTreatNullableAsOptional(false);
$generatorConfig->setClassAliases(['NlzetDoctrineMappingTypingsTestsFixtureEntityAddress' => 'NlzetCustomAddress']);
$generatorConfig->setClassReplacements(['NlzetDoctrineMappingTypingsTestsFixtureEntity' => 'Nlzet']);
$reader = new EntityReader($generatorConfig, $entityManager);

$generator = new ModelTypingGenerator($generatorConfig);
foreach ($reader->getEntities() as $classMeta) {
    $outputs[] = $generator->generate($classMeta, $reader->getProperties($classMeta->getName()));
}

echo implode(\PHP_EOL, $outputs).\PHP_EOL;
```

### Output example

```typescript
export type NlzetCustomAddress = {
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

export type NlzetExamplePropertyTypes = {
    id: number;
    stringDefault: string;
    stringNullable?: string;
    integerDefault: number;
    integerNullable?: number;
    floatDefault: number;
    floatNullable?: number;
    decimalDefault: number;
    decimalNullable?: number;
    booleanDefault: boolean;
    booleanNullable?: boolean;
    datetimeDefault: any;
    datetimeNullable: any;
    timestampDefault: number;
    timestampNullable?: number;
    arrayDefault: any[];
    arrayNullable?: any[];
    simpleArrayDefault: any[];
    simpleArrayNullable?: any[];
    jsonDefault: any[];
    jsonNullable?: any[];
    objectDefault: any;
    objectNullable: any;
    blobDefault: any;
    blobNullable: any;
    guidDefault: string;
    guidNullable?: string;
    dateDefault: any;
    dateNullable: any;
    timeDefault: number;
    timeNullable?: number;
    datetimeImmutableDefault: any;
    datetimeImmutableNullable: any;
    timestampImmutableDefault: number;
    timestampImmutableNullable?: number;
    dateImmutableDefault: any;
    dateImmutableNullable: any;
    timeImmutableDefault: number;
    timeImmutableNullable?: number;
};

export type NlzetPerson = {
    id: number;
    name: string;
    extraData: any[];
    createdAt: any;
    updatedAt: any;
    createdDate: number;
    addresses: NlzetCustomAddress[];
};
```
