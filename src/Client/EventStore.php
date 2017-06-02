<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

final class EventStore
{
    /**
     * Emit when the client begin run
     * @var string
     */
    const CLIENT_RUN = 'client_run';

    /**
     * Emit when client reports its proxy hosts to the server
     * @var string
     */
    const TRANSFER_PROXY_HOSTS = 'transfer_proxy_hosts';

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
     * Emit when the client connect to a server
     * @var string
     */
    const CONNECT_TO_SERVER =  'connect_to_server';

    /**
     * Emit when server socket has error
     * @var string
     */
    const SOCKET_ERROR = 'socket_error';
}