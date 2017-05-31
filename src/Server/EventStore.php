<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

final class EventStore
{
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
}