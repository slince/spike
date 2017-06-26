<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SpikeCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();
        $this->setName('spiked')
            ->setDefinition($this->createDefinition())
            ->setDescription('Spike is a reverse proxy that help to expose your local server to the internet.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = new DescriptorHelper();
        $helper->describe($output, $this, array(
            'format' => $input->getOption('format'),
            'raw_text' => $input->getOption('raw'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeDefinition()
    {
        return $this->createDefinition();
    }

    /**
     * {@inheritdoc}
     */
    private function createDefinition()
    {
        return new InputDefinition(array(
            new InputOption('config', null, InputOption::VALUE_REQUIRED,
                'The configuration file, support json,ini,xml and yaml format'),
            new InputOption('address', null, InputOption::VALUE_REQUIRED,
                'The server address'),
            new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format (txt, xml, json, or md)', 'txt'),
            new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command help'),
        ));
    }
}