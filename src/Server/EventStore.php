<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

final class EventStore
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
     * Fires when server receive message
     * @var string
     */
    const RECEIVE_MESSAGE = 'receive_message';

    /**
     * Fires when the server send a proxy request to a proxy client
     * @var string
     */
    const REQUEST_PROXY = 'request_proxy';

    /**
     * Fires when the server receive a register proxy message
     * @var string
     */
    const RECEIVE_REGISTER_PROXY = 'receive_register_proxy';

    /**
     * Fires when the client is disconnected
     * @var string
     */
    const CLIENT_CLOSE = 'client_close';
}