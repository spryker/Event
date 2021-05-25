<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Event\Communication\Plugin;

use Generated\Shared\Transfer\EventCollectionTransfer;
use Spryker\Shared\EventExtension\Dependency\Plugin\EventBrokerPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\Event\Business\EventFacadeInterface getFacade()
 * @method \Spryker\Zed\Event\EventConfig getConfig()
 */
class InternalEventBrokerPlugin extends AbstractPlugin implements EventBrokerPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EventCollectionTransfer $eventCollectionTransfer
     *
     * @return void
     */
    public function putEvents(EventCollectionTransfer $eventCollectionTransfer): void
    {
        $this->getFacade()->dispatch($eventCollectionTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $eventBusName
     *
     * @return bool
     */
    public function isSupportEventBusName(string $eventBusName): bool
    {
        return in_array($eventBusName, $this->getConfig()->getInternalEventBusNames());
    }
}
