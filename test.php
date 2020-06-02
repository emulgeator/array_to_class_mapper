<?php
declare(strict_types=1);

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

echo '<pre>';
var_dump($dto);
echo '</pre>';
exit;
