<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Event\Business\Router;

interface EventRouterInterface
{
    /**
     * Specification:
     * - Maps `$eventName` and `$transfers` into EventCollectionTransfer and puts events in all wired Event Broker plugins.
     * - Event Broker plugins are filtered by `$eventBusName`.
     *
     * @api
     *
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface[] $transfers
     * @param string $eventBusName
     *
     * @return void
     */
    public function putEvents(string $eventName, array $transfers, string $eventBusName): void;
}
