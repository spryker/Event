<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Event\Business;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\EventCollectionTransfer;
use Generated\Shared\Transfer\EventQueueSendMessageBodyTransfer;
use Generated\Shared\Transfer\EventTransfer;
use Generated\Shared\Transfer\QueueReceiveMessageTransfer;
use Generated\Shared\Transfer\QueueSendMessageTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Event\Business\EventBusinessFactory;
use Spryker\Zed\Event\Business\EventFacade;
use Spryker\Zed\Event\Dependency\Client\EventToQueueInterface;
use Spryker\Zed\Event\Dependency\EventCollection;
use Spryker\Zed\Event\Dependency\EventCollectionInterface;
use Spryker\Zed\Event\Dependency\EventSubscriberCollection;
use Spryker\Zed\Event\Dependency\EventSubscriberCollectionInterface;
use Spryker\Zed\Event\Dependency\Plugin\EventBaseHandlerInterface;
use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Event\Dependency\Plugin\EventHandlerInterface;
use Spryker\Zed\Event\Dependency\Plugin\EventSubscriberInterface;
use Spryker\Zed\Event\EventDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerTest\Zed\Event\Stub\TestEventBulkListenerPluginStub;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Event
 * @group Business
 * @group Facade
 * @group EventFacadeTest
 * Add your own group annotations below this line
 */
class EventFacadeTest extends Unit
{
    public const TEST_EVENT_NAME = 'test.event';

    /**
     * @var \SprykerTest\Zed\Event\EventBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testDispatchWhenEventProvidedWithSubscriberShouldHandleListener(): void
    {
        // Arrange
        $eventFacade = $this->createEventFacade();
        $eventCollectionTransfer = $this->createEventCollectionTransfer();

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Event\Dependency\Plugin\EventHandlerInterface $eventListenerMock */
        $eventListenerMock = $this->createEventListenerMock();
        $eventListenerMock->expects($this->once())
            ->method('handle')
            ->with($eventCollectionTransfer->getEvents()[0]->getMessage());

        $eventCollection = $this->createEventListenerCollection();
        $eventCollection->addListener(static::TEST_EVENT_NAME, $eventListenerMock);

        /** @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Event\Dependency\Plugin\EventSubscriberInterface $eventSubscriberMock */
        $eventSubscriberMock = $this->createEventSubscriberMock();
        $eventSubscriberMock->method('getSubscribedEvents')
            ->willReturn($eventCollection);

        $eventSubscriberCollection = $this->createEventSubscriberCollection();
        $eventSubscriberCollection->add($eventSubscriberMock);

        $eventBusinessFactory = $this->createEventBusinessFactory(
            null,
            null,
            $eventSubscriberCollection
        );

        $eventFacade->setFactory($eventBusinessFactory);

        // Act
        $eventFacade->dispatch($eventCollectionTransfer);
    }

    /**
     * @return void
     */
    public function testProcessEnqueuedMessagesShouldHandleProvidedEvents(): void
    {
        // Arrange
        $eventFacade = $this->createEventFacade();
        $transferObject = $this->createTransferObjectMock();

        $eventCollection = $this->createEventListenerCollection();
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Event\Dependency\Plugin\EventHandlerInterface $eventListenerMock */
        $eventListenerMock = $this->createEventListenerMock();

        $eventCollection->addListenerQueued(static::TEST_EVENT_NAME, $eventListenerMock);

        $queueReceivedMessageTransfer = $this->createQueueReceiveMessageTransfer($eventListenerMock, $transferObject);

        $messages = [
            $queueReceivedMessageTransfer,
        ];

        // Act
        $processedMessages = $eventFacade->processEnqueuedMessages($messages);

        // Assert
        $processedQueueReceivedMessageTransfer = $processedMessages[0];

        $this->assertTrue($processedQueueReceivedMessageTransfer->getAcknowledge());
    }

    /**
     * @return void
     */
    public function testProcessEnqueuedMessagesShouldMarkAsFailedWhenDataIsMissing(): void
    {
        // Arrange
        $eventFacade = $this->createEventFacade();

        $eventCollection = $this->createEventListenerCollection();
        /** @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Event\Dependency\Plugin\EventHandlerInterface $eventListenerMock */
        $eventListenerMock = $this->createEventListenerMock();

        $eventCollection->addListenerQueued(static::TEST_EVENT_NAME, $eventListenerMock);

        $queueReceivedMessageTransfer = $this->createQueueReceiveMessageTransfer();

        $messages = [
            $queueReceivedMessageTransfer,
        ];

        // Act
        $processedMessages = $eventFacade->processEnqueuedMessages($messages);

        // Assert
        $processedQueueReceivedMessageTransfer = $processedMessages[0];

        $this->assertFalse($processedQueueReceivedMessageTransfer->getAcknowledge());
        $this->assertTrue($processedQueueReceivedMessageTransfer->getReject());
        $this->assertTrue($processedQueueReceivedMessageTransfer->getHasError());
    }

    /**
     * @return void
     */
    public function testProcessEnqueuedMessageWillSendOnlyErroredMessageFromBulkToRetry(): void
    {
        //Arrange
        $eventCollection = $this->createEventListenerCollection();
        $eventBulkListenerStub = new TestEventBulkListenerPluginStub();
        $eventCollection->addListenerQueued(static::TEST_EVENT_NAME, $eventBulkListenerStub);
        $messages = [
            $this->createQueueReceiveMessageTransfer($eventBulkListenerStub, $this->createTransferObjectMock()),
            $this->createQueueReceiveMessageTransfer($eventBulkListenerStub, $this->createTransferObjectMock()),
        ];

        //Act
        $processedMessages = $this->createEventFacade()->processEnqueuedMessages($messages);

        //Assert
        $this->assertTrue($processedMessages[0]->getAcknowledge());
        $this->assertSame('retry', $processedMessages[0]->getRoutingKey());
        $this->assertTrue($processedMessages[1]->getAcknowledge());
        $this->assertNull($processedMessages[1]->getRoutingKey());
    }

    /**
     * @return \Generated\Shared\Transfer\EventCollectionTransfer
     */
    protected function createEventCollectionTransfer(): EventCollectionTransfer
    {
        $eventTransfer = new EventTransfer();
        $eventTransfer->setEventName(static::TEST_EVENT_NAME)
            ->setMessage($this->createTransferObjectMock());

        $eventTransfers = new ArrayObject();
        $eventTransfers->append($eventTransfer);

        $eventCollectionTransfer = new EventCollectionTransfer();
        $eventCollectionTransfer->setEvents($eventTransfers);

        return $eventCollectionTransfer;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Event\Dependency\Client\EventToQueueInterface
     */
    protected function createQueueClientMock(): EventToQueueInterface
    {
        return $this->getMockBuilder(EventToQueueInterface::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Event\Dependency\Plugin\EventHandlerInterface
     */
    protected function createEventListenerMock(): EventHandlerInterface
    {
        return $this->getMockBuilder(EventHandlerInterface::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface
     */
    protected function createEventBulkListenerMock(): EventBulkHandlerInterface
    {
        return $this->getMockBuilder(EventBulkHandlerInterface::class)
            ->getMock();
    }

    /**
     * @return \Spryker\Zed\Event\Dependency\EventCollectionInterface
     */
    protected function createEventListenerCollection(): EventCollectionInterface
    {
        return new EventCollection();
    }

    /**
     * @return \Spryker\Zed\Event\Business\EventFacade
     */
    protected function createEventFacade(): EventFacade
    {
        return new EventFacade();
    }

    /**
     * @return \Spryker\Zed\Event\Dependency\EventSubscriberCollectionInterface
     */
    protected function createEventSubscriberCollection(): EventSubscriberCollectionInterface
    {
        return new EventSubscriberCollection();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Shared\Kernel\Transfer\TransferInterface
     */
    protected function createTransferObjectMock(): TransferInterface
    {
        return $this->getMockBuilder(TransferInterface::class)
           ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Event\Dependency\Plugin\EventSubscriberInterface
     */
    protected function createEventSubscriberMock(): EventSubscriberInterface
    {
        return $this->getMockBuilder(EventSubscriberInterface::class)
            ->getMock();
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\Client\EventToQueueInterface|null $queueClientMock
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface|null $eventCollection
     * @param \Spryker\Zed\Event\Dependency\EventSubscriberCollectionInterface|null $eventSubscriberCollection
     *
     * @return \Spryker\Zed\Event\Business\EventBusinessFactory
     */
    protected function createEventBusinessFactory(
        ?EventToQueueInterface $queueClientMock = null,
        ?EventCollectionInterface $eventCollection = null,
        ?EventSubscriberCollectionInterface $eventSubscriberCollection = null
    ): EventBusinessFactory {
        if ($queueClientMock === null) {
            $queueClientMock = $this->createQueueClientMock();
        }

        if ($eventCollection === null) {
            $eventCollection = $this->createEventListenerCollection();
        }

        if ($eventSubscriberCollection === null) {
            $eventSubscriberCollection = $this->createEventSubscriberCollection();
        }

        $eventDependencyProvider = new EventDependencyProvider();

        $container = new Container();

        $businessLayerDependencies = $eventDependencyProvider->provideBusinessLayerDependencies($container);

        $container[EventDependencyProvider::CLIENT_QUEUE] = function () use ($queueClientMock) {
            return $queueClientMock;
        };

        $container[EventDependencyProvider::EVENT_LISTENERS] = function () use ($eventCollection) {
            return $eventCollection;
        };

        $container[EventDependencyProvider::EVENT_SUBSCRIBERS] = function () use ($eventSubscriberCollection) {
            return $eventSubscriberCollection;
        };

        $eventBusinessFactory = new EventBusinessFactory();
        $eventBusinessFactory->setContainer($businessLayerDependencies);

        return $eventBusinessFactory;
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\Plugin\EventBaseHandlerInterface|null $eventListenerMock
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface|null $transferObject
     *
     * @return \Generated\Shared\Transfer\QueueReceiveMessageTransfer
     */
    protected function createQueueReceiveMessageTransfer(
        ?EventBaseHandlerInterface $eventListenerMock = null,
        ?TransferInterface $transferObject = null
    ): QueueReceiveMessageTransfer {
        $message = [
            EventQueueSendMessageBodyTransfer::LISTENER_CLASS_NAME => ($eventListenerMock) ? get_class($eventListenerMock) : null,
            EventQueueSendMessageBodyTransfer::TRANSFER_CLASS_NAME => ($transferObject) ? get_class($transferObject) : null,
            EventQueueSendMessageBodyTransfer::TRANSFER_DATA => ['1', '2', '3'],
            EventQueueSendMessageBodyTransfer::EVENT_NAME => static::TEST_EVENT_NAME,
        ];

        $queueMessageTransfer = new QueueSendMessageTransfer();
        $queueMessageTransfer->setBody(json_encode($message));

        $queueReceivedMessageTransfer = new QueueReceiveMessageTransfer();
        $queueReceivedMessageTransfer->setQueueMessage($queueMessageTransfer);

        return $queueReceivedMessageTransfer;
    }
}
