<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Server\Event;

final class Events
{
    /**
     * Fires when server begin run
     * @var string
     */
    const SERVER_RUN = 'server_run';

    /**
     * Fires when server socket has error
     * @var string
     */
    const SOCKET_ERROR = 'socket_error';

    /**
     * Fires when server accept a new connection
     * @var string
     */
    const ACCEPT_CONNECTION = 'accept_connection';

    /**
     * Fires when connection error
     * @var string
     */
    const CONNECTION_ERROR = 'connection_error';

    /**
     * Fires when the server receives message
     * @var string
     */
    const KERNEL_ACTION = 'kernel.action';

    /**
     * Fires when the server receives message
     * @var string
     */
    const RECEIVE_MESSAGE = 'receive_message';

    /**
     * Fires when  an unauthorized client iss connected
     * @var string
     */
    const UNAUTHORIZED_CLIENT = 'unauthorized_client';

    /**
     * Fires when the server sends a "request_proxy" message to a proxy client
     * @var string
     */
    const REQUEST_PROXY = 'request_proxy';

    /**
     * Fires when the server receives a "register_proxy" message
     * @var string
     */
    const REGISTER_PROXY = 'register_proxy';

    /**
     * Fires when the server sends a "start_proxy" message
     * @var string
     */
    const START_PROXY = 'start_proxy';

    /**
     * Fires when the client is disconnected
     * @var string
     */
    const CLIENT_CLOSE = 'client_close';
}