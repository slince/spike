<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client;

final class EventStore
{
    /**
     * Fires when the client begin run
     * @var string
     */
    const CLIENT_RUN = 'client_run';

    /**
     * Fires when the client connect to a server
     * @var string
     */
    const CONNECT_TO_SERVER =  'connect_to_server';

    /**
     * Fires when server socket has error
     * @var string
     */
    const SOCKET_ERROR = 'socket_error';

    /**
     * Fires when client registers array of tunnels to the server
     * @var string
     */
    const REGISTER_TUNNELS = 'register_tunnels';

    /**
     * Fires when client accepts a new connection
     * @var string
     */
    const ACCEPT_CONNECTION = 'accept_connection';

    /**
     * Fires when connection error
     * @var string
     */
    const CONNECTION_ERROR = 'connection_error';

    /**
     * Fires when client receive message
     * @var string
     */
    const RECEIVE_MESSAGE = 'receive_message';

    const REGISTER_TUNNEL_ERROR = 'register_tunnel_error';

    const REGISTER_TUNNEL_SUCCESS = 'register_tunnel_success';

    /**
     * Fires when client receives message
     * @var string
     */
    const RECEIVE_PROXY_REQUEST = 'receive_proxy_request';

    /**
     * Fires when client sends a request response message
     * @var string
     */
    const SEND_PROXY_RESPONSE = 'send_proxy_response';
}