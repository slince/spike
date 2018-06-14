<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Handler;

use Spike\Common\Exception\InvalidArgumentException;
use Spike\Common\Protocol\SpikeInterface;
use Spike\Common\Protocol\Spike;
use Spike\Server\Client;

class AuthHandler extends MessageActionHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        $auth = $message->getBody();
        try{
            $authentication = $this->server->getConfiguration()->getAuthentication();
            if (!$authentication
                || $authentication->verify($auth)
            ) {
                $client = new Client($message->getBody(), $this->connection);
                $this->server->getClients()->add($client);
                $response = new Spike('auth_response', $client->toArray(), [
                    'code' => 200,
                ]);
            } else {
                $response = new Spike('auth_response', $auth, [
                    'code' => 403,
                ]);
            }
        } catch (InvalidArgumentException $exception) {
            $response = new Spike('auth_response', $auth, [
                'code' => 403,
                'message' => $exception->getMessage(),
            ]);
        }
        $this->connection->write($response);
    }
}