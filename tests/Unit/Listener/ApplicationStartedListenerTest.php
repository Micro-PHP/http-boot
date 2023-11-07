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

use Micro\Kernel\App\Business\Event\ApplicationReadyEventInterface;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;
use Micro\Plugin\Http\Listener\ApplicationStartedListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ApplicationStartedListenerTest extends TestCase
{
    private ApplicationStartedListener $applicationStartedListener;

    private HttpFacadeInterface $httpFacade;

    protected function setUp(): void
    {
        $this->httpFacade = $this->createMock(HttpFacadeInterface::class);
        $this->applicationStartedListener = new ApplicationStartedListener($this->httpFacade);
    }

    protected function createEvent(string $env)
    {
        $evt = $this->createMock(ApplicationReadyEventInterface::class);
        $evt->method('systemEnvironment')->willReturn($env);

        return $evt;
    }

    /**
     * @dataProvider dataProvider
     */
    public function testOn(string $extension, bool $isHttp): void
    {
        $event = $this->createEvent($extension);

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

        $this->applicationStartedListener->on($event);
    }

    public function dataProvider(): array
    {
        $allowed = ApplicationStartedListener::ALLOWED_MODES;
        $data = [];

        foreach ($allowed as $val) {
            $data[] = [$val, true];
        }

        return $data;
    }

    public function testSupports()
    {
        $httpFacade = $this->createMock(HttpFacadeInterface::class);
        $listener = new ApplicationStartedListener($httpFacade);

        $this->assertTrue($listener->supports($this->createEvent(ApplicationStartedListener::ALLOWED_MODES[0])));
    }
}
