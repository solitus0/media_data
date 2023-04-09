<?php

declare(strict_types=1);

namespace App\Shared\Serializer\Handler;

use App\Shared\Util\ArrayPropertyUtil;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class EnumHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'enum',
                'method' => 'serialize',
            ],
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'enum',
                'method' => 'deserialize',
            ],
        ];
    }

    public function serialize(
        JsonSerializationVisitor $visitor,
        \BackedEnum $data,
        array $type,
        Context $context
    ): string|int {
        return $data->value;
    }

    public function deserialize(JsonDeserializationVisitor $visitor, mixed $data, array $type, Context $context)
    {
        $params = ArrayPropertyUtil::getProperty($type, 'params');
        $firstParam = ArrayPropertyUtil::getProperty($params, 0);
        $className = ArrayPropertyUtil::getProperty($firstParam, 'name');

        return $className::tryFrom($data);
    }
}
