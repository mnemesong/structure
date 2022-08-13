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
class Structure implements StructureInterface
{
    protected array $fields;

    /**
     * @param array $attributesAndValues
     */
    public function __construct(array $attributesAndValues = [])
    {
        foreach ($attributesAndValues as $key => $value) {
            $this->checkAttributeName(strval($key));
            Assert::false(is_array($value), "All values in array should be scalar");
            Assert::false(is_object($value), "All values in array should be scalar");
        }
        $this->fields = $attributesAndValues;
    }

    /**
     * @param string $attributeName
     * @return mixed|null
     */
    public function __get(string $attributeName)
    {
        $this->checkAttributeName(strval($attributeName));
        return $this->fields[$attributeName] ?? null;
    }

    /**
     * @param string $attributeName
     * @param $value
     * @return void
     */
    public function __set(string $attributeName, $value): void
    {
        $this->checkAttributeName(strval($attributeName));
        Assert::false(is_array($value), "All values in array should be scalar");
        Assert::false(is_object($value), "All values in array should be scalar");
        $this->fields[$attributeName] = $value;
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function __isset(string $attributeName): bool
    {
        $this->checkAttributeName(strval($attributeName));
        return isset($this->fields[$attributeName]);
    }

    /**
     * @param string $attributeName
     * @return void
     */
    public function __unset(string $attributeName): void
    {
        $this->checkAttributeName(strval($attributeName));
        $this->checkAttributeExist($attributeName);
        unset($this->fields[$attributeName]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->fields;
    }

    /**
     * @return object
     */
    public function toObject(): object
    {
        return (object) $this->fields;
    }

    /**
     * @param string $attributeName
     * @return bool
     */
    public function hasAttribute(string $attributeName): bool
    {
        $this->checkAttributeName(strval($attributeName));
        return array_key_exists($attributeName, $this->fields);
    }

    /**
     * @param string $attributeName
     * @return void
     */
    protected function checkAttributeExist(string $attributeName): void
    {
        Assert::true($this->hasAttribute($attributeName), 'Attribute ' . $attributeName
            . 'not exist in structure');
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function buildNewFromAttributes(array $attributes): self
    {
        return new self(array_filter(
            $this->fields,
            fn(string $attrName) => (in_array($attrName, $attributes)),
            ARRAY_FILTER_USE_KEY
        ));
    }

    /**
     * @param array $attributesAndValues
     * @return static
     */
    public static function fromArray(array $attributesAndValues): self
    {
        return new self($attributesAndValues);
    }

    /**
     * @param object $attributesAndValues
     * @return static
     */
    public static function fromObject(object $attributesAndValues): self
    {
        return new self((array) $attributesAndValues);
    }

    /**
     * @param string $attributeName
     * @return void
     */
    protected function checkAttributeName(string $attributeName): void
    {
        Assert::eq(
            preg_match_all("/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/", $attributeName),
            1,
            'Incorrect attribute name: ' . $attributeName
        );
    }

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isIncludedStrictlyIn(Structure $structure): bool
    {
        foreach ($this->fields as $fKey => $fVal)
        {
            if($structure->$fKey !== $fVal)
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
            if($structure->$fKey != $fVal)
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
    public function isRudeEquals(Structure $structure): bool
    {
        $toArray = $structure->toArray();
        if(count($this->fields) !== count($toArray)) {
            return false;
        }
        foreach ($this->fields as $key => $val)
        {
            if(!key_exists($key, $toArray)) {
                return false;
            }
            if($this->fields[$key] != $toArray[$key]) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isStrictlyEquals(Structure $structure): bool
    {
        $toArray = $structure->toArray();
        if(count($this->fields) !== count($toArray)) {
            return false;
        }
        foreach ($this->fields as $key => $val)
        {
            if(!key_exists($key, $toArray)) {
                return false;
            }
            if($this->fields[$key] !== $toArray[$key]) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param callable $mapFunction
     * @return array
     */
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
     * @return $this
     */
    public function removeAttribute(string $attr): self
    {
        Assert::keyExists($this->fields, $attr, "Removing attribute " . $attr . " not exist");
        unset($this->fields[$attr]);
        return $this;
    }

    /**
     * @param string[] $attrs
     * @return $this
     */
    public function getOnlyAttributes(array $attrs): self
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
     * @return array
     */
    public function getAttributesList(): array
    {
        return array_keys($this->fields);
    }

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isAttributesListEquals(Structure $structure): bool
    {
        $myAttributes = array_keys($this->fields);
        $paramAttributes = $structure->getAttributesList();

        return (empty(array_diff($myAttributes, $paramAttributes)) && empty(array_diff($paramAttributes, $myAttributes)));
    }
}