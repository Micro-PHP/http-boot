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

namespace Micro\Plugin\Http\Listener;

use Micro\Component\EventEmitter\EventInterface;
use Micro\Component\EventEmitter\EventListenerInterface;
use Micro\Kernel\App\Business\Event\ApplicationReadyEvent;
use Micro\Kernel\App\Business\Event\ApplicationReadyEventInterface;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Stanislau Komar <kost@micro-php.net>
 */
class ApplicationStartedListener implements EventListenerInterface
{
    public function __construct(
        private readonly HttpFacadeInterface $httpFacade
    ) {
    }

    /**
     * @param ApplicationReadyEvent $event
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function on(EventInterface $event): void
    {
        $sysenv = $event->systemEnvironment();
        if ($sysenv === 'cli') {
            return;
        }

        $request = Request::createFromGlobals();

        $this->httpFacade->execute($request);
    }

    public static function supports(EventInterface $event): bool
    {
        return $event instanceof ApplicationReadyEventInterface;
    }
}
