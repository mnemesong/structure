<?php
declare(strict_types = 0);
namespace Mnemesong\StructureUnitTest\collections;

use Mnemesong\Structure\collections\StructureCollection;
use Mnemesong\Structure\Structure;
use Mnemesong\CollectionGeneratorStubs\SomeNewObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class StructureCollectionTest extends TestCase
{
    /**
     * @return Structure[]
     */
    protected static function getArrayObjects(): array
    {
        return [
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ];
    }

    /**
     * @return void
     */
    public function testConstruct(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->assertEquals($collection->getAll(), self::getArrayObjects());
    }

    /**
     * @return void
     */
    public function testAdd(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $newCollection = $collection
            ->withNewOneItem(new Structure(['name' => 'Jimmie']))
            ->withNewOneItem(new Structure(['name' => 'Valentine']));

        //Test objects head been added successfull
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
            new Structure(['name' => 'Jimmie']),
            new Structure(['name' => 'Valentine'])
        ], $newCollection->getAll());

        //Test original collection unmutable
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $collection->getAll());
    }

    /**
     * @return void
     */
    public function testAddOneException(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->expectException(\TypeError::class);
        $newCollection = $collection
            /* @phpstan-ignore-next-line */
            ->withNewOneItem((object) ['name' => 'Jimmie']);
    }

    /**
     * @return void
     */
    public function addMany(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $newCollection = $collection
            ->withManyNewItems([new Structure(['name' => 'Jimmie'])])
            ->withManyNewItems([new Structure(['name' => 'Valentine']), new Structure(['name' => 'Jakob'])]);

        //Test objects head been added successfull
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
            new Structure(['name' => 'Jimmie']),
            new Structure(['name' => 'Valentine']),
            new Structure(['name' => 'Jakob']),
        ], $newCollection->getAll());

        //Test original collection unmutable
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $collection->getAll());
    }

    /**
     * @return void
     */
    public function testAddManyException(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->expectException(\InvalidArgumentException::class);
        $newCollection = $collection
            /* @phpstan-ignore-next-line */
            ->withManyNewItems([(object) ['name' => 'Valerua'],
                new Structure(['name' => 'Clementine'])]);
    }

    /**
     * @return void
     */
    public function testWithoutOneObjectLike(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $newCollection = $collection
            ->withoutObjectsLike(new Structure(['name' => 'Valerua']), 1);

        //Test removing
        $this->assertEquals([
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $newCollection->getAll());

        //Test original collection hadn't been muted
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $collection->getAll());

        $newCollection = $collection
            ->withoutObjectsLike(new Structure(['name' => 'Valerua']));

        //Test unlimited removing
        $this->assertEquals([
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Clementine']),
        ], $newCollection->getAll());

        $newCollection = $collection
            ->withoutObjectsLike(new Structure(['name' => 'Valerua']), -1);

        //Test reversed limit removing
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Clementine']),
        ], $newCollection->getAll());
    }

    /**
     * @return void
     */
    public function testFiltering(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $newCollection = $collection
            ->filteredBy(fn(Structure $object) => (strlen(strval($object->get('name'))) > 5));

        //Test filtering
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $newCollection->getAll());

        //Test original collection hadn't been muted
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $collection->getAll());
    }

    /**
     * @return void
     */
    public function testMapping(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $mapped = $collection
            ->map(fn(Structure $object) => ($object->get('name') . '!'));

        //Test filtering
        $this->assertEquals([
            'Valerua!',
            'Jones!',
            'Valerua!',
            'Clementine!',
        ], $mapped);

        //Test original collection hadn't been muted
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $collection->getAll());
    }

    /**
     * @return void
     * @throws \ErrorException
     */
    public function testReworkingBy(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $newCollection = $collection
            ->reworkedBy(fn(Structure $object) => (new Structure(['name' => $object->get('name') . '!'])));

        //Test reworking
        $this->assertEquals([
            new Structure(['name' => 'Valerua!']),
            new Structure(['name' => 'Jones!']),
            new Structure(['name' => 'Valerua!']),
            new Structure(['name' => 'Clementine!']),
        ], $newCollection->getAll());

        //Test original collection hadn't been muted
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $collection->getAll());
    }

    /**
     * @return void
     */
    public function testCount(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->assertEquals($collection->count(), 4);

        $newCollection = $collection->withNewOneItem(new Structure(['name' => 'Jimmie']));
        $this->assertEquals($newCollection->count(), 5);
    }

    /**
     * @return void
     */
    public function testJsonSerialize(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->assertEquals(self::getArrayObjects(), $collection->getAll());
    }

    /**
     * @return void
     */
    public function testGetFirst(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->assertEquals(new Structure(['name' => 'Valerua']), $collection->getFirstAsserted());
    }

    /**
     * @return void
     */
    public function testGetFirstException(): void
    {
        $collection = new StructureCollection([]);
        $this->expectException(RuntimeException::class);
        $collection->getFirstAsserted();
    }

    /**
     * @return void
     */
    public function testGetFirstOrNull(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->assertEquals(new Structure(['name' => 'Valerua']), $collection->getFirstOrNull());

        $collection = new StructureCollection([]);
        $this->assertNull($collection->getFirstOrNull());
    }

    /**
     * @return void
     */
    public function testGetLast(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->assertEquals(new Structure(['name' => 'Clementine']), $collection->getLastAsserted());
    }

    /**
     * @return void
     */
    public function testGetLastException(): void
    {
        $collection = new StructureCollection([]);
        $this->expectException(RuntimeException::class);
        $collection->getLastAsserted();
    }

    /**
     * @return void
     */
    public function testGetLastOrNull(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->assertEquals(new Structure(['name' => 'Clementine']), $collection->getLastOrNull());

        $collection = new StructureCollection([]);
        $this->assertNull($collection->getLastOrNull());
    }

    /**
     * @return void
     */
    public function testAssertCount(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $first = $collection
            ->assertCount(fn(int $count) => ($count === 4))
            ->getFirstOrNull();
        $this->assertEquals(new Structure(['name' => 'Valerua']), $first);
    }

    /**
     * @return void
     */
    public function testAssertCountException1(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->expectException(\AssertionError::class);
        $collection->assertCount(fn(int $count) => ($count === 5));
    }

    /**
     * @return void
     */
    public function testAssertCountException2(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $this->expectException(\TypeError::class);
        $collection->assertCount(fn(array $count) => ($count));
    }

    /**
     * @return void
     * @throws \ErrorException
     */
    public function testSort(): void
    {
        $collection = new StructureCollection(self::getArrayObjects());
        $newCollection = $collection
            ->sortedBy(fn(Structure $obj1, Structure $obj2)
                => (strcasecmp(strval($obj1->get('name')), strval($obj2->get('name')))));

        //Test sorting
        $this->assertEquals([
            new Structure(['name' => 'Clementine']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Valerua']),
        ], $newCollection->getAll());

        //Test original collection hadn't been muted
        $this->assertEquals([
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ], $collection->getAll());
    }

}