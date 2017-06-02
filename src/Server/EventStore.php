<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

final class EventStore
{
    /**
     * Emit when server begin run
     * @var string
     */
    const SERVER_RUN = 'server_run';

    /**
     * Emit when server socket has error
     * @var string
     */
    const SOCKET_ERROR = 'socket_error';

    /**
     * Emit when server accept a new connection
     * @var string
     */
    const ACCEPT_CONNECTION = 'accept_connection';

    /**
     * Emit when server receive message
     * @var string
     */
    const RECEIVE_MESSAGE = 'receive_message';

    /**
     * Emit when the server send a proxy request to a proxy client
     * @var string
     */
    const SEND_PROXY_REQUEST = 'send_proxy_request';

    /**
     * Emit when the server receive a proxy response from a proxy client
     * @var string
     */
    const RECEIVE_PROXY_RESPONSE = 'receive_proxy_response';
}