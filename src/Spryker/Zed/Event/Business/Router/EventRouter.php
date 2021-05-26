<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Event\Business\Router;

use ArrayObject;
use Generated\Shared\Transfer\EventCollectionTransfer;
use Generated\Shared\Transfer\EventTransfer;
use Spryker\Zed\Event\Business\Exception\EventBrokerPluginNotFoundException;

class EventRouter implements EventRouterInterface
{
    /**
     * @var \Spryker\Shared\EventExtension\Dependency\Plugin\EventBrokerPluginInterface[]
     */
    private $eventBrokerPlugins;

    /**
     * @param \Spryker\Shared\EventExtension\Dependency\Plugin\EventBrokerPluginInterface[] $eventBrokerPlugins
     *
     * @throws \Spryker\Zed\Event\Business\Exception\EventBrokerPluginNotFoundException
     */
    public function __construct(array $eventBrokerPlugins)
    {
        if (!$eventBrokerPlugins) {
            throw new EventBrokerPluginNotFoundException();
        }

        $this->eventBrokerPlugins = $eventBrokerPlugins;
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface[] $transfers
     * @param string $eventBusName
     *
     * @return void
     */
    public function putEvents(string $eventName, array $transfers, string $eventBusName): void
    {
        $eventCollectionTransfer = $this->prepareEventCollectionTransfer($eventBusName, $transfers);

        foreach ($this->eventBrokerPlugins as $eventBrokerPlugin) {
            if ($eventBrokerPlugin->isApplicable($eventBusName)) {
                $eventBrokerPlugin->putEvents($eventCollectionTransfer);
            }
        }
    }

    /**
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface[] $transfers
     *
     * @return \Generated\Shared\Transfer\EventCollectionTransfer
     */
    protected function prepareEventCollectionTransfer(string $eventName, array $transfers): EventCollectionTransfer
    {
        $eventTransfers = new ArrayObject();

        foreach ($transfers as $transfer) {
            $timestamp = (int)(microtime(true) * 1000);

            $eventTransfer = new EventTransfer();
            $eventTransfer->setEventName($eventName)
                ->setMessage($transfer)
                ->setMessageType(get_class($transfer))
                ->setTimestamp($timestamp)
                ->setEventUuid(uniqid('event-', true));

            $eventTransfers->append($eventTransfer);
        }

        $eventCollectionTransfer = new EventCollectionTransfer();
        $eventCollectionTransfer->setEvents($eventTransfers);

        return $eventCollectionTransfer;
    }
}
