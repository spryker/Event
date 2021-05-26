<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Event\Business\Dispatcher;

use Generated\Shared\Transfer\EventCollectionTransfer;

interface EventDispatcherInterface
{
    /**
     * @param string $listenerName
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface[] $transfers
     *
     * @throws \Spryker\Zed\Event\Business\Exception\EventListenerNotFoundException
     * @throws \Spryker\Zed\Event\Business\Exception\EventListenerAmbiguousException
     *
     * @return void
     */
    public function triggerByListenerName(string $listenerName, string $eventName, array $transfers): void;

    /**
     * @param \Generated\Shared\Transfer\EventCollectionTransfer $eventCollectionTransfer
     *
     * @return void
     */
    public function dispatch(EventCollectionTransfer $eventCollectionTransfer): void;
}
