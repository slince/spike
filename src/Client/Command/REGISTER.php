<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Client\Command;

use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class REGISTER implements CommandInterface
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $tunnels;

    public function __construct(string $username, string $password, array $tunnels)
    {
        $this->username = $username;
        $this->password = $password;
        $this->tunnels = $tunnels;
    }

    /**
     * Gets the username
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Gets the password
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Gets tunnels info.
     *
     * @return array
     */
    public function getTunnels(): array
    {
        return $this->tunnels;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'REGISTER';
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, [
            'username' => $this->username,
            'password' => $this->password,
            'tunnels' => $this->tunnels
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static(
            $message->getArgument('username'),
            $message->getArgument('password'),
            $message->getArgument('tunnels')
        );
    }
}