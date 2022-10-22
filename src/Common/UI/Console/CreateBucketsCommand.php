<?php

namespace Jefero\Bot\Common\UI\Console;

use Jefero\Bot\Common\Application\CreateBucketsHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateBucketsCommand extends Command
{
    public static $defaultName = 'common:create:buckets';

    private CreateBucketsHandler $createBucketsHandler;

    public function __construct(CreateBucketsHandler $createBucketsHandler)
    {
        $this->createBucketsHandler = $createBucketsHandler;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createBucketsHandler->handle($output);
        return Command::SUCCESS;
    }
}
