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
     * Emit when the client connect to a server
     * @var string
     */
    const CONNECT_TO_SERVER =  'connect_to_server';

    /**
     * Emit when server socket has error
     * @var string
     */
    const SOCKET_ERROR = 'socket_error';

    /**
     * Emit when client reports its proxy hosts to the server
     * @var string
     */
    const TRANSFER_PROXY_HOSTS = 'transfer_proxy_hosts';

    /**
     * Emit when client accepts a new connection
     * @var string
     */
    const ACCEPT_CONNECTION = 'accept_connection';

    /**
     * Emit when client receive message
     * @var string
     */
    const RECEIVE_MESSAGE = 'receive_message';

    /**
     * Emit when client receives message
     * @var string
     */
    const RECEIVE_PROXY_REQUEST = 'receive_proxy_request';

    /**
     * Emit when client sends a request response message
     * @var string
     */
    const SEND_PROXY_RESPONSE = 'send_proxy_response';
}