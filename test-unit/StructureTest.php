<?php
declare(strict_types=1);

namespace Mnemesong\StructureUnitTest;

use Mnemesong\Structure\Structure;
use PHPUnit\Framework\TestCase;

/**
 * @author Analoty Starodubtsev "Pantagruel74" Tostar74@mail.ru
 */
class StructureTest extends TestCase
{
    public function testBasics()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $this->assertEquals($struct->var1, 'someStr');
        $this->assertEquals($struct->var2, 12);

        $struct->a12 = 'var1af!';
        $this->assertEquals($struct->var1, 'someStr');
        $this->assertEquals($struct->var2, 12);
        $this->assertEquals($struct->a12, 'var1af!');

        $struct->var1 = 299;
        $this->assertEquals($struct->var1, 299);
        $this->assertEquals($struct->var2, 12);
        $this->assertEquals($struct->a12, 'var1af!');

        $this->assertTrue(isset($struct->var2));
        unset($struct->var2);
        $this->assertFalse(isset($struct->var2));
        $this->assertEquals($struct->var1, 299);
        $this->assertEquals($struct->var2, null);
        $this->assertEquals($struct->a12, 'var1af!');

        $struct->var2 = null;
        $this->assertFalse(isset($struct->var2));
        $this->assertEquals($struct->var2, null);
    }

    public function testConstructException1()
    {
        $this->expectException(\InvalidArgumentException::class);
        $struct = new Structure(['!var1' => 'someStr']);
    }

    public function testConstructException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $struct = new Structure(['val1', 'val2']);
    }

    public function testSetException()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $this->expectException(\InvalidArgumentException::class);
        $struct->{'!1290'} = 'valval';
    }

    public function testUnsetException1()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $this->expectException(\InvalidArgumentException::class);
        unset($struct->{'!31fc1'});
    }

    public function testToArray()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct->a12 = 'var1af!';
        $this->assertEquals($struct->toArray(), ['var1' => 'someStr', 'var2' => 12, 'a12' => 'var1af!']);
    }

    public function testToObject()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct->a12 = 'var1af!';
        $this->assertEquals($struct->toObject(), (object) ['var1' => 'someStr', 'var2' => 12, 'a12' => 'var1af!']);
    }

    public function testContructors()
    {
        $struct = Structure::fromArray(['var1' => 'someStr', 'var2' => 12]);
        $this->assertEquals($struct->toObject(), (object) ['var1' => 'someStr', 'var2' => 12]);
        $struct = Structure::fromObject((object) ['var1' => 'someStr', 'var2' => 12]);
        $this->assertEquals($struct->toObject(), (object) ['var1' => 'someStr', 'var2' => 12]);
    }

    public function testConstructorsException1()
    {
        $this->expectException(\InvalidArgumentException::class);
        $struct = Structure::fromArray(['!var1' => 'someStr']);
    }

    public function testConstructorsException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $struct = Structure::fromArray(['val1', 'val2']);
    }

    public function testConstructorsException3()
    {
        $this->expectException(\InvalidArgumentException::class);
        $struct = Structure::fromObject((object) ['!var1' => 'someStr']);
    }

    public function testConstructorsException4()
    {
        $this->expectException(\InvalidArgumentException::class);
        $struct = Structure::fromObject((object) ['val1', 'val2']);
    }

    public function testHasAttribute()
    {
        $struct = Structure::fromArray(['var1' => 'someStr', 'var2' => 12]);
        $this->assertTrue($struct->hasAttribute('var2'));
        $this->assertFalse($struct->hasAttribute('var3'));

        $struct->var3 = '!!!';
        $this->assertTrue($struct->hasAttribute('var3'));
        $struct->var3 = null;
        $this->assertTrue($struct->hasAttribute('var3'));
        $this->assertFalse(isset($struct->var3));
    }

    public function testIsIncludedIn()
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct2 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $this->assertEquals(true, $struct1->isIncludedRudeIn($struct2));
        $this->assertEquals(false, $struct1->isIncludedStrictlyIn($struct2));
        $this->assertFalse($struct2->isIncludedRudeIn($struct1));

        $struct3 = new Structure(['var1' => 'anotherStr', 'var2' => 12]);
        $this->assertEquals(false, $struct1->isIncludedRudeIn($struct3));
        $this->assertEquals(false, $struct3->isIncludedRudeIn($struct1));
    }

    public function testBuildFromNewStructure()
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = $struct1->buildNewFromAttributes(['var2', 'var3']);
        $this->assertEquals(new Structure(['var2' => '12', 'var3' => 'aboba']), $struct2);
    }

    public function testRudeEquals()
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct2 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $this->assertEquals($struct1->isRudeEquals($struct2), false);
        $this->assertEquals($struct2->isRudeEquals($struct1), false);

        $struct1 = new Structure(['var1' => null, 'var2' => 12]);
        $struct2 = new Structure(['var1' => '', 'var2' => '12']);
        $this->assertEquals($struct1->isRudeEquals($struct2), true);
        $this->assertEquals($struct2->isRudeEquals($struct1), true);

        $struct1 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct2 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $this->assertEquals($struct1->isRudeEquals($struct2), true);
        $this->assertEquals($struct2->isRudeEquals($struct1), true);
    }

    public function testStrictlyEquals()
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct2 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $this->assertEquals($struct1->isStrictlyEquals($struct2), false);
        $this->assertEquals($struct2->isStrictlyEquals($struct1), false);

        $struct1 = new Structure(['var1' => null, 'var2' => 12]);
        $struct2 = new Structure(['var1' => '', 'var2' => '12']);
        $this->assertEquals($struct1->isStrictlyEquals($struct2), false);
        $this->assertEquals($struct2->isStrictlyEquals($struct1), false);

        $struct1 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct2 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $this->assertEquals($struct1->isStrictlyEquals($struct2), true);
        $this->assertEquals($struct2->isStrictlyEquals($struct1), true);
    }

    public function testMap()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);

        $result = $struct->map(fn($val, $key) => ($val . '-' . $key));
        $this->assertEquals(['var1' => 'someStr-var1', 'var2' => '12-var2', 'var3' => 'aboba-var3'], $result);

        $result = $struct->map(fn($val) => ($val . '!'));
        $this->assertEquals(['var1' => 'someStr!', 'var2' => '12!', 'var3' => 'aboba!'], $result);
    }

    public function testRemoveAttribute()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct->removeAttribute('var2');
        $this->assertEquals(new Structure(['var1' => 'someStr', 'var3' => 'aboba']), $struct);
    }

    public function testGetOnlyAttributes()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $newStruct = $struct->getOnlyAttributes(['var1', 'var3']);
        $this->assertEquals($struct, new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']));
        $this->assertEquals($newStruct, new Structure(['var1' => 'someStr', 'var3' => 'aboba']));
    }

    public function testGetOnlyAttributesException()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $this->expectException(\InvalidArgumentException::class);
        $newStruct = $struct->getOnlyAttributes(['var1', 'var4']);
    }

    public function testGetAttributesList()
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $this->assertEquals(['var1', 'var2', 'var3'], $struct->getAttributesList());
    }

    public function testIsAttributesTestEquals()
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = new Structure(['var1' => 'c214c', 'var2' => '33', 'var3' => 'aaool']);
        $this->assertEquals(true, $struct1->isAttributesListEquals($struct2));

        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = new Structure(['var1' => 'c214c', 'var2' => '33', 'var4' => 'aaool']);
        $this->assertEquals(false, $struct1->isAttributesListEquals($struct2));

        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = new Structure(['var1' => 'c214c', 'var2' => '33']);
        $this->assertEquals(false, $struct1->isAttributesListEquals($struct2));

        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = new Structure(['var1' => 'c214c', 'var2' => '33', 'var3' => 'aaool', 'var4' => 14142]);
        $this->assertEquals(false, $struct1->isAttributesListEquals($struct2));
    }
}