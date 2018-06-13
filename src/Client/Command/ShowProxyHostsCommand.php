<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Client\Command;

use Spike\Common\Tunnel\HttpTunnel;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowProxyHostsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('list-proxy')
            ->setDescription('Lists all supported proxy hosts by the client');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $tunnels = $this->getClient()->getConfiguration()->getTunnels();
        if ($tunnels) {
            $table = new Table($output);
            $table->setHeaders(['Protocol', 'Server Port', 'Local Host', 'Proxy Host']);
            foreach ($tunnels as $tunnel) {
                if ($tunnel instanceof HttpTunnel) {
                    foreach ($tunnel->getProxyHosts() as $proxyHost => $forwardHost) {
                        $table->addRow([$tunnel->getProtocol(), $tunnel->getServerPort(), $forwardHost, $proxyHost]);
                    }
                } else {
                    $table->addRow([$tunnel->getProtocol(), $tunnel->getServerPort(), $tunnel->getHost(), '-']);
                }
            }
            $table->render();
        } else {
            $output->writeln("<comment>Hi, there is no proxy host, you should create a configuration file first</comment>");
        }
    }
}