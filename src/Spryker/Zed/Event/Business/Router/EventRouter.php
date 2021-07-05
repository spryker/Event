<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Event\Business\Router;

use ArrayObject;
use Generated\Shared\Transfer\EventCollectionTransfer;
use Generated\Shared\Transfer\EventTransfer;
use Spryker\Zed\Event\Business\Dispatcher\EventDispatcherInterface;
use Spryker\Zed\Event\EventConfig;

class EventRouter implements EventRouterInterface
{
    /**
     * @var \Spryker\Shared\EventExtension\Dependency\Plugin\EventBrokerPluginInterface[]
     */
    protected $eventBrokerPlugins;

    /**
     * @var \Spryker\Zed\Event\Business\Dispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param \Spryker\Zed\Event\Business\Dispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Spryker\Shared\EventExtension\Dependency\Plugin\EventBrokerPluginInterface[] $eventBrokerPlugins
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        array $eventBrokerPlugins
    ) {
        $this->eventDispatcher = $eventDispatcher;
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
        $eventCollectionTransfer = $this->prepareEventCollectionTransfer($eventName, $transfers, $eventBusName);

        foreach ($this->eventBrokerPlugins as $eventBrokerPlugin) {
            if ($eventBrokerPlugin->isApplicable($eventBusName)) {
                $eventBrokerPlugin->putEvents($eventCollectionTransfer);
            }
        }

        if ($eventBusName === EventConfig::EVENT_BUS_INTERNAL) {
            $this->eventDispatcher->dispatch($eventCollectionTransfer);
        }
    }

    /**
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface[] $transfers
     * @param string $eventBusName
     *
     * @return \Generated\Shared\Transfer\EventCollectionTransfer
     */
    protected function prepareEventCollectionTransfer(
        string $eventName,
        array $transfers,
        string $eventBusName
    ): EventCollectionTransfer {
        $eventTransfers = new ArrayObject();

        foreach ($transfers as $transfer) {
            $timestamp = (string)(microtime(true));

            $eventTransfer = new EventTransfer();
            $eventTransfer->setEventName($eventName)
                ->setMessage($transfer)
                ->setTimestamp($timestamp)
                ->setEventUuid(uniqid('event-', true));

            $eventTransfers->append($eventTransfer);
        }

        $eventCollectionTransfer = new EventCollectionTransfer();
        $eventCollectionTransfer->setEvents($eventTransfers);
        $eventCollectionTransfer->setEventBusName($eventBusName);

        return $eventCollectionTransfer;
    }
}
