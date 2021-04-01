<?php


namespace Spike;

use Symfony\Component\Console\Application as BaseApplication;

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

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);
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

        ], parent::getDefaultCommands());
    }
}