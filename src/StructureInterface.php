<?php

namespace Mnemesong\Structure;

interface StructureInterface
{
    public function __get(string $attributeName);

    public function __set(string $attributeName, $value): void;

    public function __isset(string $attributeName): bool;

    public function __unset(string $attributeName): void;

    public function toArray(): array;

    public function toObject(): object;

    public function hasAttribute(string $attributeName): bool;

    public function buildNewFromAttributes(array $attributes): self;

    public static function fromArray(array $attributesAndValues): self;

    public static function fromObject(object $attributesAndValues): self;

    public function isIncludedStrictlyIn(Structure $structure): bool;

    public function isIncludedRudeIn(Structure $structure): bool;

    public function isRudeEquals(Structure $structure): bool;

    public function isStrictlyEquals(Structure $structure): bool;

    public function map(callable $mapFunction):array;

    public function removeAttribute(string $attr): self;

    public function getOnlyAttributes(array $attrs): self;

    public function getAttributesList(): array;

    public function isAttributesListEquals(Structure $structure): bool;
}