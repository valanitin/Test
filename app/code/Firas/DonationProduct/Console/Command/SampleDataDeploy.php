<?php
/**
 * A Magento 2 module named Firas/DonationProduct
 * Copyright (C) 2017 Derrick Heesbeen
 *
 * This file is part of Firas/DonationProduct.
 *
 * Firas/DonationProduct is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Firas\DonationProduct\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SampleDataDeploy extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    protected $donationSampleDataModel;

    private $state;

    public function __construct(
        \Firas\DonationProduct\Model\SampleData $donationSampleDataModel,
        \Magento\Framework\App\State $state
    ) {
        $this->donationSampleDataModel = $donationSampleDataModel;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        $output->writeln("Deploy SampleData");

        $this->donationSampleDataModel->install();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("firas_donationproduct:sampledata:deploy");
        $this->setDescription("Deploy donation product sample data ");

        parent::configure();
    }
}
