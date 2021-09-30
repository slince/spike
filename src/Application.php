<?php


namespace Spike;

use Monolog\Logger;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class Application extends BaseApplication
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
     * @var SerializerInterface 
     */
    protected $serializer;

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        if (null === $this->serializer) {
            $this->serializer = $this->createSerializer();
        }
        return $this->serializer;
    }

    protected function createSerializer()
    {
        return new Serializer([
            new ObjectNormalizer(),
            new JsonSerializableNormalizer(),
        ], [
            new XmlEncoder(),
            new JsonEncoder(),
            new YamlEncoder()
        ]);
    }

    protected function createLogger()
    {
        $logger = new Logger()
    }

    /**
     * {@inheritDoc}
     */
    public function getHelp()
    {
        return static::LOGO . parent::getHelp();
    }

    protected function getDefaultCommands()
    {
        return array_merge([
            new Command\ServeCommand()
        ], parent::getDefaultCommands());
    }
}