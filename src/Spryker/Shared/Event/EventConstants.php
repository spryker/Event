<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Event;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface EventConstants
{
    /**
     * Specification:
     * - Log file location for logging all events in system (path to file)
     *
     * @api
     */
    public const LOG_FILE_PATH = 'EVENT_LOG_FILE_PATH';

    /**
     * Specification:
     * - Is logging activated for events (true|false)
     *
     * @api
     */
    public const LOGGER_ACTIVE = 'LOGGER_ACTIVE';

    /**
     * Specification:
     * - Maximum amount of retrying on failing message
     *
     * @api
     */
    public const MAX_RETRY_ON_FAIL = 'MAX_RETRY_ON_FAIL';

    /**
     * Specification:
     * - Number of event messages for bulk operation
     *
     * @api
     */
    public const EVENT_CHUNK = 'EVENT_CHUNK';

    /**
     * Specification:
     *  - Chunk size of enqueueing event messages for bulk operations
     *
     * @api
     */
    public const ENQUEUE_EVENT_CHUNK = 'EVENT:ENQUEUE_EVENT_CHUNK';

    /**
     * Specification:
     * - Queue name as used when with asynchronous event handling
     *
     * @api
     */
    public const EVENT_QUEUE = 'event';

    /**
     * Specification:
     * - Retry queue name as used when with asynchronous event handling
     *
     * @api
     */
    public const EVENT_QUEUE_RETRY = 'event.retry';

    /**
     * Specification:
     * - Error queue name as used when with asynchronous event handling
     *
     * @api
     */
    public const EVENT_QUEUE_ERROR = 'event.error';

    /**
     * Specification:
     * - Manages instance pooling during events processing.
     * - Publish process consume less RAM when this configuration is disabled.
     *
     * @api
     */
    public const IS_INSTANCE_POOLING_ALLOWED = 'EVENT:IS_INSTANCE_POOLING_ALLOWED';

    /**
     * Specification:
     * - Name of the internal event bus that handles events immediately after their dispatching.
     *
     * @api
     */
    public const EVENT_BUS_INTERNAL = '@internal';
}
