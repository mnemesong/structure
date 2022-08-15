<?php

namespace Mnemesong\Structure;

interface StructureInterface
{
    /**
     * @param string $attributeName
     * @return scalar|null
     */
    public function getAttribute(string $attributeName);

    /**
     * @param string $attributeName
     * @param scalar|null $value
     * @return self
     */
    public function withAttribute(string $attributeName, $value): self;

    /**
     * @param string $attributeName
     * @return bool
     */
    public function issetAttribute(string $attributeName): bool;

    /**
     * @param string $attributeName
     * @return bool
     */
    public function isEmptyAttribute(string $attributeName): bool;

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
    public function withOnlyAttributes(array $attributes): self;

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
    public function mapAttributes(callable $mapFunction): array;

    /**
     * @param string $attr
     * @return self
     */
    public function withoutAttribute(string $attr): self;

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