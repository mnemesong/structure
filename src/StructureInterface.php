<?php

namespace Mnemesong\Structure;

interface StructureInterface
{
    /**
     * @param string $attributeName
     * @return scalar|null
     */
    public function get(string $attributeName);

    /**
     * @param string $attributeName
     * @param scalar|null $value
     * @return self
     */
    public function with(string $attributeName, $value): self;

    /**
     * @param string $attributeName
     * @return bool
     */
    public function isset(string $attributeName): bool;

    /**
     * @param string $attributeName
     * @return bool
     */
    public function isEmpty(string $attributeName): bool;

    /**
     * @return array<scalar|null>
     */
    public function toArray(): array;

    /**
     * @param string $attributeName
     * @return bool
     */
    public function has(string $attributeName): bool;

    /**
     * @param string[] $attributes
     * @return self
     */
    public function withOnly(array $attributes): self;

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
    public function map(callable $mapFunction): array;

    /**
     * @param string $attr
     * @return self
     */
    public function without(string $attr): self;

    /**
     * @return string[]
     */
    public function attributes(): array;

    /**
     * @param Structure $structure
     * @return bool
     */
    public function isAttributesEquals(Structure $structure): bool;
}