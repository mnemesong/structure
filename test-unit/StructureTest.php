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
    public function testBasics(): void
    {
        //Testing constuct
        $struct = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $this->assertEquals($struct->get('var1'), 'someStr');
        $this->assertEquals($struct->get('var2'), 12);

        //Testing setters
        $newStruct = $struct->with('a12', 'var1af!');
        $this->assertEquals($newStruct->get('var1'), 'someStr');
        $this->assertEquals($newStruct->get('var2'), 12);
        $this->assertEquals($newStruct->get('a12'), 'var1af!');
        $this->assertEquals($struct->has('a12'), false);

        $struct = $struct->with('var1', 299);
        $this->assertEquals($struct->get('var1'), 299);
        $this->assertEquals($struct->get('var2'), 12);

        //Testing checks
        $struct = new Structure(['var1' => 1, 'var2' => '', 'var3' => null]);

        $this->assertTrue($struct->has('var1'));
        $this->assertFalse($struct->isEmpty('var1'));
        $this->assertTrue($struct->isset('var1'));

        $this->assertTrue($struct->has('var2'));
        $this->assertTrue($struct->isEmpty('var2'));
        $this->assertTrue($struct->isset('var2'));

        $this->assertTrue($struct->has('var3'));
        $this->assertTrue($struct->isEmpty('var3'));
        $this->assertFalse($struct->isset('var3'));

        $this->assertFalse($struct->has('var4'));
        $this->assertTrue($struct->isEmpty('var4'));
        $this->assertFalse($struct->isset('var4'));

        //Testing removing
        $struct = $struct->without('var3');
        $this->assertFalse($struct->has('var3'));
        $this->assertEquals($struct->get('var1'), 1);
        $this->assertEquals($struct->get('var2'), '');
    }

    public function testConstructException1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $struct = new Structure([1 => 'someStr']);
    }

    public function testSettingException1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /* @phpstan-ignore-next-line  */
        $struct = (new Structure())->with('var1', ['var2' => 2]);
    }

    public function testRemovingException1(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $struct = (new Structure())->without('var1');
    }

    public function testToArray(): void
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $this->assertEquals($struct->toArray(), ['var1' => 'someStr', 'var2' => 12]);
    }

    public function testIsIncludedIn(): void
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct2 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct3 = new Structure(['var1' => 'anotherStr', 'var2' => 12]);
        $struct4 = new Structure(['var1' => 'someStr', 'var2' => 12, 'var3' => 1]);

        //Test rude include in
        $this->assertTrue($struct1->isIncludedRudeIn($struct2));
        $this->assertFalse($struct2->isIncludedRudeIn($struct1));
        $this->assertFalse($struct1->isIncludedRudeIn($struct3));
        $this->assertFalse($struct3->isIncludedRudeIn($struct1));
        $this->assertTrue($struct1->isIncludedRudeIn($struct4));
        $this->assertFalse($struct4->isIncludedRudeIn($struct1));

        //Test strictly include in
        $this->assertFalse($struct1->isIncludedStrictlyIn($struct2));
        $this->assertFalse($struct2->isIncludedStrictlyIn($struct1));
        $this->assertFalse($struct1->isIncludedStrictlyIn($struct3));
        $this->assertFalse($struct3->isIncludedStrictlyIn($struct1));
        $this->assertTrue($struct1->isIncludedStrictlyIn($struct4));
        $this->assertFalse($struct4->isIncludedStrictlyIn($struct1));
    }

    public function testIsEquals(): void
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => 12]);
        $struct2 = new Structure(['var2' => '12', 'var1' => 'someStr']);
        $struct3 = new Structure(['var2' => 12, 'var1' => 'someStr']);
        $struct4 = new Structure(['var2' => '12']);

        //Test rude equals
        $this->assertTrue($struct1->isRudeEquals($struct2));
        $this->assertTrue($struct2->isRudeEquals($struct1));
        $this->assertTrue($struct1->isRudeEquals($struct3));
        $this->assertTrue($struct3->isRudeEquals($struct1));
        $this->assertFalse($struct1->isRudeEquals($struct4));
        $this->assertFalse($struct4->isRudeEquals($struct1));

        //Test strictly equals
        $this->assertFalse($struct1->isStrictlyEquals($struct2));
        $this->assertFalse($struct2->isStrictlyEquals($struct1));
        $this->assertTrue($struct1->isStrictlyEquals($struct3));
        $this->assertTrue($struct3->isStrictlyEquals($struct1));
        $this->assertFalse($struct1->isStrictlyEquals($struct4));
        $this->assertFalse($struct4->isStrictlyEquals($struct1));
    }

    public function testBuildFromNewStructure(): void
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = $struct1->withOnly(['var3', 'var2']);
        $this->assertEquals(new Structure(['var3' => 'aboba', 'var2' => '12',]), $struct2);
    }

    public function testMap(): void
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);

        $result = $struct->map(fn($val, $key) => ($val . '-' . $key));
        $this->assertEquals(['var1' => 'someStr-var1', 'var2' => '12-var2', 'var3' => 'aboba-var3'], $result);

        $result = $struct->map(fn($val) => ($val . '!'));
        $this->assertEquals(['var1' => 'someStr!', 'var2' => '12!', 'var3' => 'aboba!'], $result);
    }

    public function testRemoveAttribute(): void
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct = $struct->without('var2');
        $this->assertEquals(new Structure(['var1' => 'someStr', 'var3' => 'aboba']), $struct);
    }

    public function testGetOnlyAttributesException(): void
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $this->expectException(\InvalidArgumentException::class);
        $newStruct = $struct->getOnly(['var1', 'var4']);
    }

    public function testGetAttributesList(): void
    {
        $struct = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $this->assertEquals(['var1', 'var2', 'var3'], $struct->attributes());
    }

    public function testIsAttributesTestEquals(): void
    {
        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = new Structure(['var1' => 'c214c', 'var3' => 'aaool', 'var2' => '33',]);
        $this->assertEquals(true, $struct1->isAttributesEquals($struct2));

        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = new Structure(['var1' => 'c214c', 'var2' => '33', 'var4' => 'aaool']);
        $this->assertEquals(false, $struct1->isAttributesEquals($struct2));

        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = new Structure(['var1' => 'c214c', 'var2' => '33']);
        $this->assertEquals(false, $struct1->isAttributesEquals($struct2));

        $struct1 = new Structure(['var1' => 'someStr', 'var2' => '12', 'var3' => 'aboba']);
        $struct2 = new Structure(['var1' => 'c214c', 'var2' => '33', 'var3' => 'aaool', 'var4' => 14142]);
        $this->assertEquals(false, $struct1->isAttributesEquals($struct2));
    }
}