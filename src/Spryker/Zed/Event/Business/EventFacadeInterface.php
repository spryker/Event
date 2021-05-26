<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Event\Business;

use Generated\Shared\Transfer\EventCollectionTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Event\EventConfig;

/**
 * @method \Spryker\Zed\Event\Business\EventBusinessFactory getFactory()
 */
interface EventFacadeInterface
{
    /**
     * Specification:
     * - Puts the event into appropriate Event Brokers based on given event bus name.
     * - If event bus name is empty calls `EventFacade::dispatch` by default via InternalEventBrokerPlugin if wired.
     *
     * @api
     *
     * @param string $eventName
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $transfer
     * @param string $eventBusName
     *
     * @return void
     */
    public function trigger($eventName, TransferInterface $transfer, string $eventBusName = EventConfig::EVENT_BUS_INTERNAL);

    /**
     * Specification:
     * - Puts the events into appropriate Event Brokers.
     * - Calls `EventFacade::dispatchBulk` by default via InternalEventBrokerPlugin if wired.
     *
     * @api
     *
     * @param string $eventName
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $transfers
     * @param string $eventBusName
     *
     * @return void
     */
    public function triggerBulk($eventName, array $transfers, string $eventBusName = EventConfig::EVENT_BUS_INTERNAL): void;

    /**
     * Specification:
     * - Handles all events by registered by appropriate $eventName in each EventTransfer.
     * - Passes transfer object to each listener
     * - If listener is queueable then it will put into queue system.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EventCollectionTransfer $eventCollectionTransfer
     *
     * @return void
     */
    public function dispatch(EventCollectionTransfer $eventCollectionTransfer): void;

    /**
     * Specification:
     * - Triggers event processing by listener name with an optional parameter event name.
     * - Listener name is a listener class name in short, qualified or a fully qualified form.
     * - Throws an exception if event listener is not found or ambiguous.
     *
     * @api
     *
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
     * Specification:
     * - Processes all listeners enqueued in event queue (queue consumer)
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QueueReceiveMessageTransfer[] $queueMessageTransfers
     *
     * @return \Generated\Shared\Transfer\QueueReceiveMessageTransfer[]
     */
    public function processEnqueuedMessages(array $queueMessageTransfers);

    /**
     * Specification:
     * - Forward consumed messages to event queue
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QueueReceiveMessageTransfer[] $queueMessageTransfers
     *
     * @return \Generated\Shared\Transfer\QueueReceiveMessageTransfer[]
     */
    public function forwardMessages(array $queueMessageTransfers): array;
}
