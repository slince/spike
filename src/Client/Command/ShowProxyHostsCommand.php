<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Command;

use Spike\Tunnel\HttpTunnel;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowProxyHostsCommand extends Command
{
    public function configure()
    {
        $this->setName('list-proxy')
            ->setDescription('Lists all supported proxy hosts by the client');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $tunnels = $this->getClient()->getKernel()->getTunnels();
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