<?php

declare(strict_types=1);

namespace App\Shared\ParamConverter\EventSubscriber;

use App\Shared\Exception\Trait\ApiExceptionTrait;
use App\Shared\ParamConverter\Event\ParamConverterDeserializationEvent;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\ReadOnlyProperty;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;


class ExtraFieldValidatorSubscriber implements EventSubscriberInterface
{
    use ApiExceptionTrait;

    private string $env;

    public function __construct(#[Autowire('%env(APP_ENV)%')] string $env)
    {
        $this->env = $env;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ParamConverterDeserializationEvent::class => ['onDeserialization'],
        ];
    }

    public function onDeserialization(ParamConverterDeserializationEvent $event): void
    {
        $className = $event->getObjectClass();
        $content = $event->getData();

        if ($this->env === 'dev' && isset($content['XDEBUG_SESSION_START'])) {
            unset($content['XDEBUG_SESSION_START']);
        }

        $deserializableProperties = [];
        $reflectionClass = new \ReflectionClass($className);
        $exclusionPolicy = $this->getExclusionPolicy($reflectionClass);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if ($this->shouldDeserializeProperty($property, $exclusionPolicy)) {
                $deserializableProperties[] = $propertyName;
            }
        }

        $extraFields = array_diff(array_keys($content), $deserializableProperties);
        if (count($extraFields) > 0) {
            throw $this->createApiException(
                status: Response::HTTP_BAD_REQUEST,
                detail: 'Extra fields are not allowed',
                arbitraryData: [
                    'extraFields' => $extraFields,
                ],
            );
        }
    }

    private function shouldDeserializeProperty(\ReflectionProperty $property, ?string $exclusionPolicy): bool
    {
        $attributes = $property->getAttributes();

        $isExposed = $this->isPropertyExposed($exclusionPolicy, $attributes);
        $isReadOnly = $this->isPropertyReadOnly($attributes);
        $isExcluded = $this->isPropertyExcluded($attributes);

        return $isExposed && !$isReadOnly && !$isExcluded;
    }

    private function isPropertyExposed(?string $exclusionPolicy, array $attributes): bool
    {
        if ($exclusionPolicy === ExclusionPolicy::ALL) {
            $exposeAttribute = array_filter($attributes, static function (\ReflectionAttribute $attribute) {
                return $attribute->getName() === Expose::class;
            });

            return !empty($exposeAttribute);
        }

        return true;
    }

    private function isPropertyReadOnly(array $attributes): bool
    {
        $readOnlyAttribute = array_filter($attributes, static function (\ReflectionAttribute $attribute) {
            return $attribute->getName() === ReadOnlyProperty::class;
        });

        return !empty($readOnlyAttribute);
    }

    private function isPropertyExcluded(array $attributes): bool
    {
        $excludeAttribute = array_filter($attributes, static function (\ReflectionAttribute $attribute) {
            return $attribute->getName() === Exclude::class;
        });

        return !empty($excludeAttribute);
    }

    private function getExclusionPolicy(\ReflectionClass $reflectionClass): ?string
    {
        $attributes = $reflectionClass->getAttributes(ExclusionPolicy::class);
        if (empty($attributes)) {
            return null;
        }

        $exclusionPolicyAttribute = $attributes[0];

        return $exclusionPolicyAttribute->newInstance()->policy;
    }
}
