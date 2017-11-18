<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Tests\Unit\Service\Image;

use PHPUnit\Framework\TestCase;
use Strider2038\ImgCache\Service\Image\CreateAction;
use Strider2038\ImgCache\Service\Image\DeleteAction;
use Strider2038\ImgCache\Service\Image\GetAction;
use Strider2038\ImgCache\Service\Image\ImageActionFactory;
use Strider2038\ImgCache\Service\Image\ReplaceAction;

class ImageActionFactoryTest extends TestCase
{
    private const ACTION_ID_INVALID = 'invalid';

    /** @var GetAction */
    private $getAction;

    /** @var CreateAction */
    private $createAction;

    /** @var ReplaceAction */
    private $replaceAction;

    /** @var DeleteAction */
    private $deleteAction;

    protected function setUp(): void
    {
        $this->getAction = \Phake::mock(GetAction::class);
        $this->createAction = \Phake::mock(CreateAction::class);
        $this->replaceAction = \Phake::mock(ReplaceAction::class);
        $this->deleteAction = \Phake::mock(DeleteAction::class);
    }

    /**
     * @test
     * @dataProvider actionIdAndClassProvider
     * @param string $actionId
     * @param string $actionClass
     */
    public function createAction_givenActionIdAndLocation_actionIsCreated(string $actionId, string $actionClass): void
    {
        $factory = $this->createFactory();

        $action = $factory->createAction($actionId);

        $this->assertInstanceOf($actionClass, $action);
    }

    /**
     * @test
     * @expectedException \Strider2038\ImgCache\Exception\InvalidRouteException
     * @expectedExceptionCode 404
     * @expectedExceptionMessage Action "invalid" not found
     */
    public function createAction_givenActionIdNotExist_exceptionThrown(): void
    {
        $factory = $this->createFactory();

        $factory->createAction(self::ACTION_ID_INVALID);
    }

    public function actionIdAndClassProvider(): array
    {
        return [
            ['get', GetAction::class],
            ['create', CreateAction::class],
            ['replace', ReplaceAction::class],
            ['delete', DeleteAction::class],
        ];
    }

    protected function createFactory(): ImageActionFactory
    {
        return new ImageActionFactory(
            $this->getAction,
            $this->createAction,
            $this->replaceAction,
            $this->deleteAction
        );
    }
}
