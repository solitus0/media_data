<?php

declare(strict_types=1);

namespace App\Shared\Validator;

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\ReadOnlyProperty;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExtraFieldValidator extends ConstraintValidator
{
    public const DEFAULT_EXCLUDE = [];

    private string $env;

    public function __construct(#[Autowire('%env(APP_ENV)%')] string $env, private readonly RequestStack $requestStack)
    {
        $this->env = $env;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExtraField) {
            return;
        }

        $object = $this->context->getObject();
        $data = $this->getRequestData();
        if (!$data) {
            return;
        }

        $exclude = self::DEFAULT_EXCLUDE;
        if ($this->env === 'dev' && isset($data['XDEBUG_SESSION_START'])) {
            $exclude[] = 'XDEBUG_SESSION_START';
        }

        $deserializableProperties = [];
        $reflectionClass = new \ReflectionClass($object);
        $exclusionPolicy = $this->getExclusionPolicy($reflectionClass);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            if ($this->shouldDeserializeProperty($property, $exclusionPolicy)) {
                $deserializableProperties[] = $propertyName;
            }
        }

        $extraFields = array_diff(array_keys($data), $deserializableProperties, $exclude);
        if (count($extraFields) > 0) {
            $this->context
                ->buildViolation(ExtraField::EXTRA_FIELD)
                ->setParameter('{{ extra_fields }}', implode(', ', $extraFields))
                ->addViolation()
            ;
        }
    }

    private function getRequestData(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            return [];
        }

        $query = $request->query->all();
        $body = $request->request->all();

        return array_merge($query, $body);
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
}
