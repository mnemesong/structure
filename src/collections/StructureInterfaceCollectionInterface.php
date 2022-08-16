<?php

namespace Mnemesong\Structure\collections;

use Mnemesong\Structure\StructureInterface;

interface StructureInterfaceCollectionInterface
{
    /**
     * @param \Mnemesong\Structure\StructureInterface $object
     * @return self
     */
    public function withNewOneItem(StructureInterface $object): self;

    /**
     * @param \Mnemesong\Structure\StructureInterface[] $objects
     * @return self
     */
    public function withManyNewItems(array $objects): self;

    /**
     * @return StructureInterface[]
     */
    public function getAll(): array;

    /**
     * @param \Mnemesong\Structure\StructureInterface $object
     * @param int $limit
     * @return self
     */
    public function withoutObjectsLike(StructureInterface $object, int $limit = 0): self;

    /**
     * @param callable $callbackFunction
     * @return self
     */
    public function filteredBy(callable $callbackFunction): self;

    /**
     * @param callable $callbackFunction
     * @return array
     */
    /* @phpstan-ignore-next-line  */
    public function map(callable $callbackFunction): array;

    /**
     * @param callable $callbackFunction
     * @return self
     * @throws \ErrorException
     */
    public function reworkedBy(callable $callbackFunction): self;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return \Mnemesong\Structure\StructureInterface[]
     */
    public function jsonSerialize(): array;

    /**
     * @return \Mnemesong\Structure\StructureInterface
     */
    public function getFirstAsserted(): StructureInterface;

    /**
     * @return \Mnemesong\Structure\StructureInterface
     */
    public function getLastAsserted(): StructureInterface;

    /**
     * @param callable $func
     * @return self
     */
    public function assertCount(callable $func): self;

    /**
     * @return \Mnemesong\Structure\StructureInterface|null
     */
    public function getFirstOrNull(): ?StructureInterface;

    /**
     * @return \Mnemesong\Structure\StructureInterface|null
     */
    public function getLastOrNull(): ?StructureInterface;

    /**
     * @param callable $sortFunc
     * @return $this
     * @throws \ErrorException
     */
    public function sortedBy(callable $sortFunc): self;
}