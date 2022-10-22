<?php

declare(strict_types=1);

namespace Jefero\Bot\Common\DTO;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionNamedType;
use ReflectionProperty;

/**
 * @psalm-consistent-constructor
 */
abstract class DTO implements \JsonSerializable
{
    public function __construct(array $data = [])
    {
        if (empty($data)) {
            return;
        }

        $properties = $this->getProperties();

        foreach ($data as $propertyName => $value) {
            if (!isset($properties[$propertyName])) {
                continue;
            }

            $reflectionProperty = $properties[$propertyName];
            $reflectionPropertyType = $reflectionProperty->getType();

            // Если свойство без типа или mixed => $this->$propertyName = $value

            if (($reflectionPropertyType instanceof ReflectionNamedType)) {
                switch ($reflectionPropertyType->getName()) {
                    case UuidInterface::class:
                        if ($reflectionPropertyType->allowsNull() && empty($value)) {
                            $value = null;
                        } else {
                            $value = Uuid::fromString((string)$value);
                        }
                        break;
                    case 'int':
                        if ($reflectionPropertyType->allowsNull() && is_null($value)) {
                            $value = null;
                        } else {
                            $value = (int)$value;
                        }
                        break;
                    case 'bool':
                        if ($reflectionPropertyType->allowsNull() && is_null($value)) {
                            $value = null;
                        } else {
                            $value = (bool)$value;
                        }
                        break;
                    case 'float':
                        if ($reflectionPropertyType->allowsNull() && is_null($value)) {
                            $value = null;
                        } else {
                            $value = (float)$value;
                        }
                        break;
                    default:
                        // DTO
                        if (class_exists($reflectionPropertyType->getName())
                            && is_subclass_of($reflectionPropertyType->getName(), DTO::class)
                            && !($value instanceof DTO)
                        ) {
                            if ($reflectionPropertyType->allowsNull() && empty($value)) {
                                $value = null;
                            } else {
                                /** @var class-string<DTO> $dtoClassName */
                                $dtoClassName = $reflectionPropertyType->getName();
                                $value = DTOFactory::createDtoFromData($dtoClassName, (array)$value);
                            }
                        }

                        // --

                        // DTOCollection
                        if (class_exists($reflectionPropertyType->getName())
                            && is_subclass_of($reflectionPropertyType->getName(), DTOCollection::class)
                            && !($value instanceof DTOCollection)
                        ) {
                            if ($reflectionPropertyType->allowsNull() && empty($value)) {
                                $value = null;
                            } else {
                                /** @var class-string<DTOCollection> $dtoCollectionClassName */
                                $dtoCollectionClassName = $reflectionPropertyType->getName();
                                $value = DTOFactory::createDtoCollectionTypedFromData(
                                    $dtoCollectionClassName,
                                    (array)$value
                                );
                            }
                        }
                    // --
                }
            }

            $this->$propertyName = $value;
        }
    }

    /**
     * @return array<string, ReflectionProperty>
     */
    protected function getProperties(): array
    {
        static $cache = null;
        if (is_array($cache)) {
            return $cache;
        }

        $cache = [];
        foreach ((new \ReflectionClass($this))->getProperties() as $property) {
            if ($property->isPrivate()) {
                continue;
            }
            // так как nelmio отображает в документации только если есть геттер

            /*if ($property->getName()==='customer_image_url') {
                var_dump($property->getType()->allowsNull());die();
            }*/

            $cache[$property->getName()] = $property;
        }

        return $cache;
    }

    public function jsonSerialize(): array
    {
        $properties = $this->getProperties();
        $result = [];
        foreach ($properties as $propertyName => $property) {
            $result[$propertyName] = $this->$propertyName;
        }

        return $result;
    }
}
