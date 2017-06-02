<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowProxyHostsCommand extends Command
{
    public function configure()
    {
        $this->setName('list-hosts')
            ->setDescription('Lists all supported proxy hosts by the client');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $proxyHosts = $this->getClient()->getConfiguration()->get('proxy-hosts');
        if ($proxyHosts) {
            $table = new Table($output);
            $table->setHeaders(['Proxy Host', 'Forward Host']);
            foreach ($proxyHosts as $proxyHost => $forwardHost) {
                $table->addRow([$proxyHost, $forwardHost]);
            }
            $table->render();
        } else {
            $output->writeln("<warning>Hi, there is no proxy host, you should create a configuration file first</warning>");
        }
    }
}