<?php

namespace Espier\Swoole\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class StatusCommand extends ServerCommand
{
    protected $name = 'espier:status';

    protected $description = 'Outputs the status of the espier web server for the given address';

    public function configure()
    {
        $this->setHelp(<<<EOF
The <info>%command.name%</info> watch espier web server status:

  <info>php %command.full_name%</info>

To change the default bind address and the default port use the <info>address</info> argument:

  <info>php %command.full_name% 0.0.0.0:9058</info>

EOF
        );
    }

    public function fire()
    {
        list($address, $host, $port) = $this->initAddress();

        if ($this->isServerRunning($address)) {
            $this->info(sprintf('Espier web server still listening on http://%s', $address));
        } else {
            $this->warn(sprintf('No espier web server is listening on http://%s', $address));
        }
    }

    protected function getArguments()
    {
        return [
            ['address', InputArgument::OPTIONAL, 'Address:port']
        ];
    }

    private function isServerRunning($address)
    {
        return ($this->getProcessId($address) && $this->isAdreesRunning($address));
    }

    private function isAdreesRunning($address)
    {
        list($hostname, $port) = explode(':', $address);

        if (false !== $fp = @fsockopen($hostname, $port, $errno, $errstr, 1)) {
            fclose($fp);

            return true;
        }

        return false;
    }
}