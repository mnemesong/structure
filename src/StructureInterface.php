<?php

namespace Mnemesong\Structure;

interface StructureInterface
{
    /**
     * @param string $attributeName
     * @return scalar|null
     */
    public function __get(string $attributeName);

    /**
     * @param string $attributeName
     * @param scalar|null $value
     * @return void
     */
    public function __set(string $attributeName, $value): void;

    /**
     * @param string $attributeName
     * @return bool
     */
    public function __isset(string $attributeName): bool;

    /**
     * @param string $attributeName
     * @return void
     */
    public function __unset(string $attributeName): void;

    /**
     * @return array<scalar|null>
     */
    public function toArray(): array;

    /**
     * @param string $attributeName
     * @return bool
     */
    public function hasAttribute(string $attributeName): bool;

    /**
     * @param string[] $attributes
     * @return self
     */
    public function buildNewFromAttributes(array $attributes): self;

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isIncludedStrictlyIn(Structure $structure): bool;

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isIncludedRudeIn(Structure $structure): bool;

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isRudeEquals(Structure $structure): bool;

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isStrictlyEquals(Structure $structure): bool;

    /**
     * @param callable $mapFunction
     * @return array
     */
    /* @phpstan-ignore-next-line */
    public function map(callable $mapFunction):array;

    /**
     * @param string $attr
     * @return self
     */
    public function removeAttribute(string $attr): self;

    /**
     * @param string[] $attrs
     * @return self
     */
    public function getOnlyAttributes(array $attrs): self;

    /**
     * @return string[]
     */
    public function getAttributesList(): array;

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isAttributesListEquals(Structure $structure): bool;
}