<?php

declare(strict_types=1);

/*
 *  This file is part of the Micro framework package.
 *
 *  (c) Stanislau Komar <kost@micro-php.net>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Micro\Plugin\Http\Test\Unit\Listener;

use Micro\Kernel\App\AppKernelInterface;
use Micro\Kernel\App\Business\Event\ApplicationReadyEvent;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;
use Micro\Plugin\Http\Listener\ApplicationStartedListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ApplicationStartedListenerTest extends TestCase
{
    private ApplicationStartedListener $applicationStartedListener;

    private HttpFacadeInterface $httpFacade;

    private ApplicationReadyEvent $applicationReadyEvent;

    protected function setUp(): void
    {
        $this->httpFacade = $this->createMock(HttpFacadeInterface::class);
        $this->applicationStartedListener = $this->getMockBuilder(ApplicationStartedListener::class)
            ->setConstructorArgs([
                $this->httpFacade,
            ])
            ->onlyMethods([
                'isHttp',
            ])
            ->getMock();

        $this->applicationReadyEvent = new ApplicationReadyEvent(
            $this->createMock(AppKernelInterface::class),
            'any',
        );
    }

    /**
     * @dataProvider dataProvider
     */
    public function testOn(bool $isHttp): void
    {
        $this->applicationStartedListener
            ->expects($this->once())
            ->method('isHttp')
            ->willReturn($isHttp);

        if (!$isHttp) {
            $this->httpFacade
                ->expects($this->never())
                ->method('execute');
        } else {
            $this->httpFacade
                ->expects($this->once())
                ->method('execute')
                ->willReturn(
                    $this->createMock(Response::class)
                );
        }

        $this->applicationStartedListener->on($this->applicationReadyEvent);
    }

    public function testIsHttp()
    {
        $this->httpFacade->expects($this->never())->method('execute');

        $appListener = new ApplicationStartedListener(
            $this->httpFacade
        );

        $appListener->on($this->applicationReadyEvent);
    }

    public function dataProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    public function testSupports()
    {
        $httpFacade = $this->createMock(HttpFacadeInterface::class);
        $listener = new ApplicationStartedListener($httpFacade);

        $this->assertTrue($listener->supports($this->applicationReadyEvent));
    }
}
