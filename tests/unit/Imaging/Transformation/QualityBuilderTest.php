<?php

/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Imaging\Transformation\{
    Quality,
    QualityBuilder
};

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class QualityBuilderTest extends TestCase
{

    public function testCreateQuality_ValidConfig_ClassIsConstructed(): void
    {
        $builder = new QualityBuilder();
        foreach ([20, 50, 100] as $value) {
            $this->assertInstanceOf(Quality::class, $builder->build($value));
        }
    }
    
    /**
     * @dataProvider qualityInvalidConfigProvider
     * @expectedException Strider2038\ImgCache\Exception\InvalidConfigException
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid config for quality transformation
     */
    public function testCreateQuality_InvalidConfig_ClassIsConstructed($config): void
    {
        $builder = new QualityBuilder();
        $builder->build($config);
    }
    
    public function qualityInvalidConfigProvider(): array
    {
        return [['20h'], ['abc'], ['']];
    }

}
