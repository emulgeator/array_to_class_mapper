# Array to Class Mapper
A simple library which automatically maps a given multi dimensional array to a given class.

## Getting Started

### Installing
Run `composer require emulgeator/array-to-class-mapper` to add this library as a dependency to your project


## Usage

### Simple scalar type mapping
```php
use Emul\ArrayToClassMapper\MapperFactory;

class DTO
{
    private int $id;

    private float $value;

    public function __construct() {
        // Constructor wont be called
    }
}

$arrayToMap = [
    'id' => '1',
    'value' => '1.2'
];

$mapper = (new MapperFactory())->getMapper();

/** @var DTO $dto */
$dto = $mapper->map($arrayToMap, DTO::class);
```

As you'll see the library casts the values from the given array.
So for example: `$dto->id` will be an integer indeed.

The mapper does that by checking the type of the property.
If it's not set it tries to fetch this information from the **DocBlock**

### Class mapping
```php
use Emul\ArrayToClassMapper\MapperFactory;

require_once 'vendor/autoload.php';

class InnerDTO
{
    private string $key;
    private string $value;
}

class DTO
{
    private InnerDTO $inner;
}

$arrayToMap = [
    'inner' => [
        'key'   => 'first_key',
        'value' => 'value'
    ]
];

$mapper = (new MapperFactory())->getMapper();

/** @var DTO $dto */
$dto = $mapper->map($arrayToMap, DTO::class);
```

### Complex type mapping
In case of complex types you both DocBlock and type hinting can be used. See the previous example modified to have a series of inner objects:
```php
use Emul\ArrayToClassMapper\MapperFactory;

require_once 'vendor/autoload.php';

class InnerDTO
{
    private string $key;
    private string $value;
}

class DTO
{
    private int $id;

    /** @var InnerDTO[] */
    private array $inner = [];
}

$arrayToMap = [
    'id' => 1,
    'inner' => [
        [
            'key'   => 'first_key',
            'value' => 'value'
        ],
        [
            'key'   => 'second_key',
            'value' => 'value'
        ],
    ]
];

$mapper = (new MapperFactory())->getMapper();

/** @var DTO $dto */
$dto = $mapper->map($arrayToMap, DTO::class);
```

### Defining custom mapper for specific type

If a data mapping is not possible by simply casting the stored value, custom mappers can be set:

```php
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Emul\ArrayToClassMapper\MapperFactory;

require_once 'vendor/autoload.php';

class InnerDTO
{
    private string $key;
    private string $value;

    public function __construct(string $keyPrefix, string $key, string $value)
    {
        $this->key   = $keyPrefix . $key;
        $this->value = $value;
    }
}

class DTO
{
    private int $id;

    /** @var InnerDTO[] */
    private array $values;
}

$arrayToMap = [
    'id'        => 1,
    'values' => [
        [
            'key' => 'first',
            'value' => '1'
        ],
    ],
];

$mapper = (new MapperFactory())->getMapper();

$innerDTOMapper = \Closure::fromCallable(function (array $data) {
    return new InnerDTO('prefix_', $data['key'], $data['value']);
});

$mapper->addCustomMapper(InnerDTO::class, $innerDTOMapper);
/** @var DTO $dto */
$dto = $mapper->map($arrayToMap, DTO::class);
```

The same works if the Custom type is in an array but in this case the mapper function will receive the subarray as is:

```php

```

## Limitations
The library currently is not able to import and auto load a class defined in a DocBlock.
The following example will not work because the class is not imported neither we know the FQCN:
```php
/** @var Value[] */
private array $values = [];
```

To fix this you need to use the FQCN of the class:
```php
/** @var \NameSpace\Value[] */
private array $values = [];
```
