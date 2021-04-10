<?php


namespace Spike\Server\Handler;

namespace Spike\Server\Handler;

use Spike\Exception\InvalidArgumentException;
use Spike\Io\Message;
use Spike\Server\Client;

class LoginHandler extends MessageMessageHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $auth = $message->getPayload();
        try{
            $authentication = $this->server->getConfiguration()->getAuthentication();
            if (!$authentication
                || $authentication->verify($auth)
            ) {
                $client = new Client($message->getBody(), $this->connection);
                $this->server->getClients()->add($client);
                $response = new Message('auth_response', $client->toArray());
            } else {
                $response = new Message('auth_response', $auth, [
                    'code' => 403,
                ]);
            }
        } catch (InvalidArgumentException $exception) {
            $response = new Message('auth_response', $auth, [
                'code' => 403,
                'message' => $exception->getMessage(),
            ]);
        }
        $this->connection->write($response);
    }
}