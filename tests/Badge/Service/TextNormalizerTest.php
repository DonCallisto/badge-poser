<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) App\Tests <http://App\Tests.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Badge\Service;

use App\Badge\Service\TextNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Class TextNormalizerTest.
 */
class TextNormalizerTest extends TestCase
{
    /** @var TextNormalizer */
    private $normalizer;

    public function setUp(): void
    {
        $this->normalizer = new TextNormalizer();
    }

    /**
     * @dataProvider getBadNumberToConvert
     */
    public function testNumberToTextConversion($input, $output): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $res = $this->normalizer->normalize($input);
        $this->assertEquals($output, $res);
    }

    public static function getBadNumberToConvert()
    {
        return [
            ['A', 'ERR 2 '],
            [-1, 'ERR 2 '],
        ];
    }

    /**
     * @dataProvider getGoodNumberToConvert
     */
    public function testGoodNumberToTextConversion($input, $output): void
    {
        $res = $this->normalizer->normalize($input);
        $this->assertEquals($output, $res);
    }

    public static function getGoodNumberToConvert()
    {
        return [
            [0,               '1'],
            [1,               '1'],
            ['16',            '16'],
            [199,             '199'],
            ['1012',          '1.01 k'],
            ['1212',          '1.21 k'],
            ['1999',          '2 k'],
            ['1003000',       '1 M'],
            [9001003000,      '9 G'],
            ['9001003000000', '9 T'],
        ];
    }
}