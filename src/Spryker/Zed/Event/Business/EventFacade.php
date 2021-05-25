<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Event\Business;

use Generated\Shared\Transfer\EventCollectionTransfer;
use Spryker\Shared\Event\EventConfig;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\Event\Business\EventBusinessFactory getFactory()
 */
class EventFacade extends AbstractFacade implements EventFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $transfer
     * @param string $eventBusName
     *
     * @return void
     */
    public function trigger($eventName, TransferInterface $transfer, string $eventBusName = EventConfig::EVENT_BUS_INTERNAL)
    {
        $this->getFactory()
            ->createEventRouter()
            ->putEvents($eventName, [$transfer], $eventBusName);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $eventName
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $transfers
     * @param string $eventBusName
     *
     * @return void
     */
    public function triggerBulk($eventName, array $transfers, string $eventBusName = EventConfig::EVENT_BUS_INTERNAL): void
    {
        $this->getFactory()
            ->createEventRouter()
            ->putEvents($eventName, $transfers, $eventBusName);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EventCollectionTransfer $eventCollectionTransfer
     *
     * @return void
     */
    public function dispatch(EventCollectionTransfer $eventCollectionTransfer): void
    {
        $this->getFactory()
            ->createEventDispatcher()
            ->dispatch($eventCollectionTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $listenerName
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface[] $transfers
     *
     * @return void
     */
    public function triggerByListenerName(string $listenerName, string $eventName, array $transfers): void
    {
        $this->getFactory()
            ->createEventDispatcher()
            ->triggerByListenerName($listenerName, $eventName, $transfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QueueReceiveMessageTransfer[] $queueMessageTransfers
     *
     * @return \Generated\Shared\Transfer\QueueReceiveMessageTransfer[]
     */
    public function processEnqueuedMessages(array $queueMessageTransfers)
    {
        return $this->getFactory()
            ->createEventQueueConsumer()
            ->processMessages($queueMessageTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QueueReceiveMessageTransfer[] $queueMessageTransfers
     *
     * @return \Generated\Shared\Transfer\QueueReceiveMessageTransfer[]
     */
    public function forwardMessages(array $queueMessageTransfers): array
    {
        return $this->getFactory()
            ->createMessageForwarder()
            ->forwardMessages($queueMessageTransfers);
    }
}
