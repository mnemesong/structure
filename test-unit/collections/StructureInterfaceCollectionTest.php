<?php
declare(strict_types = 0);
namespace Mnemesong\Structure\collections;

use Mnemesong\Structure\collections\StructureInterfaceCollection;
use Mnemesong\Structure\StructureInterface;
use Mnemesong\Structure\Structure;
use Mnemesong\CollectionGeneratorStubs\SomeNewObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class StructureInterfaceCollectionTest extends TestCase
{
    public static function getArrayObjects(): array
    {
        return [
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Jones']),
            new Structure(['name' => 'Valerua']),
            new Structure(['name' => 'Clementine']),
        ];
    }

    public function testConstruct()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->assertEquals($collection->getAll(), self::getArrayObjects());
    }

    public function testAdd()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
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

    public function testAddOneException()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->expectException(\TypeError::class);
        $newCollection = $collection
            /* @phpstan-ignore-next-line */
            ->withNewOneItem((object) ['name' => 'Jimmie']);
    }

    public function addMany()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
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

    public function testAddManyException()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->expectException(\InvalidArgumentException::class);
        $newCollection = $collection
            /* @phpstan-ignore-next-line */
            ->withManyNewItems([(object) ['name' => 'Valerua'],
                new Structure(['name' => 'Clementine'])]);
    }

    public function testWithoutOneObjectLike()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
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

    public function testFiltering()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $newCollection = $collection
            ->filteredBy(fn(Structure $object) => (strlen($object->get('name')) > 5));

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

    public function testMapping()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
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

    public function testReworkingBy()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
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

    public function testCount()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->assertEquals($collection->count(), 4);

        $newCollection = $collection->withNewOneItem(new Structure(['name' => 'Jimmie']));
        $this->assertEquals($newCollection->count(), 5);
    }

    public function testJsonSerialize()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->assertEquals(self::getArrayObjects(), $collection->getAll());
    }

    public function testGetFirst()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->assertEquals(new Structure(['name' => 'Valerua']), $collection->getFirstAsserted());
    }

    public function testGetFirstException()
    {
        $collection = new StructureInterfaceCollection([]);
        $this->expectException(RuntimeException::class);
        $collection->getFirstAsserted();
    }

    public function testGetFirstOrNull()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->assertEquals(new Structure(['name' => 'Valerua']), $collection->getFirstOrNull());

        $collection = new StructureInterfaceCollection([]);
        $this->assertNull($collection->getFirstOrNull());
    }

    public function testGetLast()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->assertEquals(new Structure(['name' => 'Clementine']), $collection->getLastAsserted());
    }

    public function testGetLastException()
    {
        $collection = new StructureInterfaceCollection([]);
        $this->expectException(RuntimeException::class);
        $collection->getLastAsserted();
    }

    public function testGetLastOrNull()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->assertEquals(new Structure(['name' => 'Clementine']), $collection->getLastOrNull());

        $collection = new StructureInterfaceCollection([]);
        $this->assertNull($collection->getLastOrNull());
    }

    public function testAssertCount()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $first = $collection
            ->assertCount(fn(int $count) => ($count === 4))
            ->getFirstOrNull();
        $this->assertEquals(new Structure(['name' => 'Valerua']), $first);
    }

    public function testAssertCountException1()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->expectException(\AssertionError::class);
        $collection->assertCount(fn(int $count) => ($count === 5));
    }

    public function testAssertCountException2()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $this->expectException(\TypeError::class);
        $collection->assertCount(fn(array $count) => ($count));
    }

    public function testSort()
    {
        $collection = new StructureInterfaceCollection(self::getArrayObjects());
        $newCollection = $collection
            ->sortedBy(fn(Structure $obj1, Structure $obj2) => (strcasecmp($obj1->get('name'), $obj2->get('name'))));

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