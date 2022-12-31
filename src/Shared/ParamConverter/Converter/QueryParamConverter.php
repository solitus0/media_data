<?php

declare(strict_types=1);

namespace App\Shared\ParamConverter\Converter;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AutoconfigureTag('request.param_converter', attributes: ['converter' => 'app.query'])]
class QueryParamConverter implements ParamConverterInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {}

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $convertVarName = $configuration->getName();
        $content = $request->query->all();
        $objectClass = $configuration->getClass();
        $deserializationContext = new DeserializationContext();
        $serializerGroups = ['Default'];
        $deserializationContext->setGroups($serializerGroups);

        $object = $this->serializer->deserialize(json_encode($content), $objectClass, 'json', $deserializationContext);
        $request->attributes->set($convertVarName, $object);

        $validatorGroups = ['Default'];
        $options = $configuration->getOptions();
        if (isset($options['validator']['groups'])) {
            $validatorGroups = $options['validator']['groups'];
        }

        $errors = $this->validator->validate($object, null, $validatorGroups);
        $request->attributes->set('validationErrors', $errors);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getConverter() === 'app.query';
    }
}
