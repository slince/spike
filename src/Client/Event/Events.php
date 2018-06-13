<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Client\Event;

final class Events
{
    /**
     * Fires when the client begin run
     * @var string
     */
    const CLIENT_RUN = 'client.run';

    /**
     * Fires when the client connect to the server
     * @var string
     */
    const CLIENT_CONNECT =  'client.connect';

    /**
     * Fires if the client cannot connect to the server
     * @var string
     */
    const CANNOT_CONNECT_SERVER = 'client.connect_to_server';

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
     * Fires when the client disconnect form the server
     * @var string
     */
    const DISCONNECT_FROM_SERVER = 'disconnect_from_server';

    /**
     * Fires when client receive message
     * @var string
     */
    const RECEIVE_MESSAGE = 'receive_message';

    /**
     * Fires when the registration tunnel fails
     * @var string
     */
    const REGISTER_TUNNEL_ERROR = 'register_tunnel_error';

    /**
     * Fires when the registration tunnel success
     * @var string
     */
    const REGISTER_TUNNEL_SUCCESS = 'register_tunnel_success';

    /**
     * Fires when client receives a "request_proxy"  message
     * @var string
     */
    const REQUEST_PROXY = 'request_proxy';

    /**
     * Fires when client sends a "register_proxy" message
     * @var string
     */
    const REGISTER_PROXY = 'register_proxy';

    /**
     * Fires when client receives a "start_proxy"  message
     * @var string
     */
    const START_PROXY = 'start_proxy';

    /**
     * Fires when client authentication fails
     * @var string
     */
    const AUTH_ERROR = 'auth_error';

    /**
     * Fires when client authentication success
     * @var string
     */
    const AUTH_SUCCESS = 'auth_success';
}