<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Event;

use Spryker\Zed\Event\Communication\Plugin\InternalEventBrokerPlugin;
use Spryker\Zed\Event\Dependency\Client\EventToQueueBridge;
use Spryker\Zed\Event\Dependency\EventCollection;
use Spryker\Zed\Event\Dependency\EventSubscriberCollection;
use Spryker\Zed\Event\Dependency\Service\EventToUtilEncoding;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\Event\EventConfig getConfig()
 */
class EventDependencyProvider extends AbstractBundleDependencyProvider
{
    public const EVENT_LISTENERS = 'event_listeners';
    public const EVENT_SUBSCRIBERS = 'event subscribers';
    public const EVENT_BROKER_PLUGINS = 'EVENT_BROKER_PLUGINS';

    public const CLIENT_QUEUE = 'client queue';

    public const SERVICE_UTIL_ENCODING = 'service util encoding';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addEventListenerCollection($container);
        $container = $this->addEventSubscriberCollection($container);
        $container = $this->addQueueClient($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addEventBrokerPlugins($container);

        return $container;
    }

    /**
     * @phpstan-return \Spryker\Zed\Event\Dependency\EventCollectionInterface<string, \Spryker\Zed\Event\Business\Dispatcher\EventListenerContextInterface[]>
     *
     * @return \Spryker\Zed\Event\Dependency\EventCollectionInterface
     */
    public function getEventListenerCollection()
    {
        return new EventCollection();
    }

    /**
     * @phpstan-return \Spryker\Zed\Event\Dependency\EventSubscriberCollectionInterface<\Spryker\Zed\Event\Dependency\Plugin\EventSubscriberInterface>
     *
     * @return \Spryker\Zed\Event\Dependency\EventSubscriberCollectionInterface
     */
    public function getEventSubscriberCollection()
    {
        return new EventSubscriberCollection();
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEventListenerCollection(Container $container): Container
    {
        $container->set(static::EVENT_LISTENERS, function (Container $container) {
            return $this->getEventListenerCollection();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEventSubscriberCollection(Container $container): Container
    {
        $container->set(static::EVENT_SUBSCRIBERS, function (Container $container) {
            return $this->getEventSubscriberCollection();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQueueClient(Container $container): Container
    {
        $container->set(static::CLIENT_QUEUE, function (Container $container) {
            return new EventToQueueBridge($container->getLocator()->queue()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new EventToUtilEncoding($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEventBrokerPlugins(Container $container): Container
    {
        $container->set(static::EVENT_BROKER_PLUGINS, function () {
            return $this->getEventBrokerPlugins();
        });

        return $container;
    }

    /**
     * @return \Spryker\Shared\EventExtension\Dependency\Plugin\EventBrokerPluginInterface[]
     */
    protected function getEventBrokerPlugins(): array
    {
        return [
            new InternalEventBrokerPlugin(),
        ];
    }
}
