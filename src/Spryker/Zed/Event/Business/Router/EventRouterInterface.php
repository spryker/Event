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
     * - If there is no one Event Broker plugin after filtering it throws EventBrokerPluginNotFoundException.
     *
     * @api
     *
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface[] $transfers
     * @param string $eventBusName
     *
     * @throws \Spryker\Zed\Event\Business\Exception\EventBrokerPluginNotFoundException
     *
     * @return void
     */
    public function route(string $eventName, array $transfers, string $eventBusName): void;
}
