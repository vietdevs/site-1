<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Amazon
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright © 2018 CedCommerce. All rights reserved.
 * @license     EULA http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Amazon\Console\Product;

class Inventory extends \Ced\Integrator\Console\Base
{
    const CLI_NAME = self::CLI_PREFIX . 'amazon:product:inventory';

    protected function configure()
    {
        $this->setName(self::CLI_NAME);
        $this->setDescription('Sync product inventory via cli');
        $this->addOption(
            'log',
            'd',
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            'Log in File',
            '0'
        );
        parent::configure();
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        parent::execute($input, $output);
        $rows = 2;
        /** @var \Symfony\Component\Console\Helper\ProgressBar $progress */
        $progress = $this->om->create(
            \Symfony\Component\Console\Helper\ProgressBar::class,
            ['output' => $output,'rows' => $rows]
        );
        $progress->setBarCharacter('<fg=magenta>=</>');
        $progress->start();
        usleep(300000);
        $progress->advance();
        usleep(300000);
        /** @var \Ced\Amazon\Cron\Queue\Processor $processor */
        $processor = $this->om->create(\Ced\Amazon\Cron\Product\Inventory::class);
        $processor->execute();
        $progress->advance();
        usleep(300000);
        $progress->finish();
        $output->writeln('');

        $log = $input->getOption('log');
        if ($log) {
            /** @var \Ced\Amazon\Helper\File\Logger $logger */
            $logger = $this->om->create(\Ced\Amazon\Helper\File\Logger::class);
            $logger->info("All inventory add cron run by schedule.");
        }
    }
}
