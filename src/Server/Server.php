<?php


namespace Spike\Server;

use React\Socket\ConnectionInterface;
use Spike\TcpServer;

final class Server extends TcpServer
{
    /**
     * @var string
     */
    const LOGO = <<<EOT
 _____   _____   _   _   _    _____   _____  
/  ___/ |  _  \ | | | | / /  | ____| |  _  \ 
| |___  | |_| | | | | |/ /   | |__   | | | | 
\___  \ |  ___/ | | | |\ \   |  __|  | | | | 
 ___| | | |     | | | | \ \  | |___  | |_| | 
/_____/ |_|     |_| |_|  \_\ |_____| |_____/ 
EOT;

    const NAME = 'Spiked';

    const VERSION = '0.2.0';

    /**
     * @var ConnectionInterface[]
     */
    protected $clients;

    public function handleConnection(ConnectionInterface $connection)
    {
        $this->clients[] = $connection;

    }
}