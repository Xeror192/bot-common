<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\DTO;

use Jefero\Bot\Common\DTO\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadataInterface;
use Symfony\Component\Validator\Validation;

class DTOFactory
{
    /**
     * @template T of DTO
     * @param class-string<T> $classname
     * @param Request $request
     * @return T
     */
    public static function createDtoFromRequest(string $classname, Request $request)
    {
        $data = $request->request->all();
        $files = $request->files->all();
        $data = array_merge($data, $files);

        return self::createDtoFromData($classname, $data);
    }

    /**
     * @template T of DTO
     * @param class-string<T> $classname
     * @param Request $request
     * @return T
     */
    public static function createDtoFromQuery(string $classname, Request $request)
    {
        return self::createDtoFromData($classname, $request->query->all());
    }

    /**
     * @template T of DTO
     * @param class-string<T> $classname
     * @param array $data
     * @return T
     */
    public static function createDtoFromData(string $classname, array $data)
    {
        self::validate($classname, $data);
        $dto = new $classname($data);

        return $dto;
    }

    /**
     * @template T
     * @param class-string<T> $collectionTypedClassName
     * @param Request $request
     * @return T
     */
    public static function createDtoCollectionFromRequest(string $collectionTypedClassName, Request $request)
    {
        $data = $request->request->all();
        $files = $request->files->all();
        $data = array_merge($data, $files);

        return self::createDtoCollectionTypedFromData($collectionTypedClassName, $data);
    }

    /**
     * @template T
     * @template T2 of DTO
     * @param class-string<T> $collectionTypedClassName
     * @param array $data
     * @return T
     */
    public static function createDtoCollectionTypedFromData(string $collectionTypedClassName, array $data)
    {
        /** @var class-string<T2> $elementClassName */
        $elementClassName = $collectionTypedClassName::getElementClassName();

        $elementObjects = [];
        foreach ($data as $element) {
            $elementObjects[] = self::createDtoFromData($elementClassName, $element);
        }

        return new $collectionTypedClassName($elementObjects);
    }

    private static function validate(string $classname, array $data): void
    {
        /** @psalm-suppress TooManyArguments */
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping(true)
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        /** @var ClassMetadata $metadata */
        $metadata = $validator->getMetadataFor($classname);
        $constraints = [];

        foreach ($metadata->getConstrainedProperties() as $propertyName) {
            $propertyMetadata = $metadata->getPropertyMetadata($propertyName);
            if (!empty($propertyMetadata)) {
                $propertyMetadata = current($propertyMetadata);
            }
            if (!($propertyMetadata instanceof PropertyMetadataInterface)) {
                continue;
            }
            $constraints[$propertyMetadata->getPropertyName()] = $propertyMetadata->getConstraints();
        }

        $constraintCollection = new Collection($constraints);
        $constraintCollection->allowExtraFields = true;

        $errors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($validator->validate($data, $constraintCollection) as $violation) {
            $field = preg_replace(['/\]\[/', '/\[|\]/'], ['.', ''], $violation->getPropertyPath());
            $errors[$field] = $violation->getMessage();
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
