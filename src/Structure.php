<?php
declare(strict_types=1);

namespace Mnemesong\Structure;

use Webmozart\Assert\Assert;

/**
 * ENG: An auxiliary object for storing data sets of the form: [<field> => <scalar value | null>], as well as for
 * fast performance of comparative and transformation operations with data sets of this type.
 * Used as a representation of the data stored in the storage / table or received from there.
 *
 * RUS: Вспомогательный объект для хранения наборов данных вида: [<поле> => <скалярное значение | null>], а так-же для
 * быстрого выполнения сравнительных и преобразовательных операций с наборами данных подобного типа.
 * Используется как представление сохраняемых в хранилище/таблицу или получаемых оттуда данных.
 *
 * @author Analoty Starodubtsev "Pantagruel74" Tostar74@mail.ru
 */
final class Structure implements StructureInterface
{
    /* @var array<scalar|null> $fields */
    /* @phpstan-ignore-next-line */
    protected array $fields;

    /**
     * @param array<null|scalar> $attributesAndValues
     */
    public function __construct(array $attributesAndValues = [])
    {
        foreach ($attributesAndValues as $key => $value) {
            Assert::string($key, 'Structures may be constructed by arrays with only string-typed keys');
            $this->$key = $value;
        }
        $this->fields = $attributesAndValues;
    }


    /**
     * @param string $attributeName
     * @return scalar|null
     */
    public function get(string $attributeName)
    {
        return $this->fields[$attributeName] ?? null;
    }

    /**
     * @param string $attributeName
     * @param scalar|null $value
     * @return self
     */
    public function with(string $attributeName, $value): self
    {
        Assert::nullOrScalar($value, "All values in array should be scalar");
        $clone = clone $this;
        $clone->fields[$attributeName] = $value;
        return $clone;
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function isset(string $attributeName): bool
    {
        return isset($this->fields[$attributeName]);
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function isEmpty(string $attributeName): bool
    {
        return empty($this->fields[$attributeName]);
    }

    /**
     * @return array<null|scalar>
     */
    public function toArray(): array
    {
        return $this->fields;
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function has(string $attributeName): bool
    {
        return array_key_exists($attributeName, $this->fields);
    }

    /**
     * @param string[] $attributes
     * @return self
     */
    public function withOnly(array $attributes): self
    {
        return new self(array_filter(
            $this->fields,
            fn(string $attrName) => (in_array($attrName, $attributes)),
            ARRAY_FILTER_USE_KEY
        ));
    }

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isIncludedStrictlyIn(Structure $structure): bool
    {
        foreach ($this->fields as $fKey => $fVal)
        {
            if($structure->has($fKey) === false) {
                return false;
            }
            if($structure->fields[$fKey] !== $fVal)
            {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isIncludedRudeIn(Structure $structure): bool
    {
        foreach ($this->fields as $fKey => $fVal)
        {
            if($structure->has($fKey) === false) {
                return false;
            }
            if($structure->fields[$fKey] != $fVal) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isRudeEquals(Structure $structure): bool
    {
        $toArray = $structure->toArray();
        return $this->isIncludedRudeIn($structure) && $structure->isIncludedRudeIn($this);
    }

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isStrictlyEquals(Structure $structure): bool
    {
        return $this->isIncludedStrictlyIn($structure) && $structure->isIncludedStrictlyIn($this);
    }

    /**
     * @param callable $mapFunction
     * @return array
     */
    /* @phpstan-ignore-next-line */
    public function map(callable $mapFunction):array
    {
        $res = [];
        foreach ($this->fields as $key => $val)
        {
            $res[$key] = $mapFunction($val, $key);
        }
        return $res;
    }

    /**
     * @param string $attr
     * @return self
     */
    public function without(string $attr): self
    {
        Assert::keyExists($this->fields, $attr, "Removing attribute " . $attr . " not exist");
        $array = $this->fields;
        unset($array[$attr]);
        return new self($array);
    }

    /**
     * @param string[] $attrs
     * @return self
     */
    public function getOnly(array $attrs): self
    {
        Assert::allString($attrs, "Parameter should be array of strings");
        foreach ($attrs as $attr)
        {
            Assert::keyExists($this->fields, $attr, "Try to get attribute " . $attr . " that not exist");
        }

        $res = [];
        foreach ($this->fields as $key => $val)
        {
            if(in_array($key, $attrs)) {
                $res[$key] = $val;
            }
        }

        return new self($res);
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return array_keys($this->fields);
    }

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isAttributesEquals(Structure $structure): bool
    {
        $myAttributes = array_keys($this->fields);
        $paramAttributes = $structure->attributes();

        return (empty(array_diff($myAttributes, $paramAttributes)) && empty(array_diff($paramAttributes, $myAttributes)));
    }
}