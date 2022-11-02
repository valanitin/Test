<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Console\Command;

use Amasty\Meta\Model\UrlKey\Generate\Processor;
use Amasty\Meta\Model\UrlKey\Generate\ProcessorFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Indexer\Model\Indexer\StateFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\ProgressBarFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractGenerator extends Command
{
    const EXCLUDE_DISABLED_PRODUCTS = 'exclude-disabled-products';

    /**
     * @var StateFactory
     */
    protected $stateFactory;

    /**
     * @var ProcessorFactory
     */
    private $processorFactory;

    /***
     * @var ProgressBarFactory
     */
    private $progressBarFactory;

    public function __construct(
        ProcessorFactory $processorFactory,
        ProgressBarFactory $progressBarFactory,
        StateFactory $stateFactory,
        string $name = null
    ) {
        $this->progressBarFactory = $progressBarFactory;
        $this->processorFactory = $processorFactory;
        $this->stateFactory = $stateFactory;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDefinition($this->getOptionList());

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln(__('Initialization...'));
        $processor = $this->getProcessor($input->getOption(self::EXCLUDE_DISABLED_PRODUCTS));
        $stepsAmount = $processor->getProcessStepsAmount();
        $progressBar = $this->initProgressBar($output, $stepsAmount);
        $progressBar->start();
        $processor->process(function () use ($progressBar) {
            static $currentStep = 0;
            $progressBar->setProgress(++$currentStep);
        }, $output);
        $progressBar->finish();
        $output->writeln('');
    }

    private function getProcessor(bool $excludeDisabledProducts, ?array $stores = null): Processor
    {
        $processor = $this->processorFactory->create();
        $processor->setCreateRedirect($this->isNeedRedirect());
        $processor->setStores($stores);
        $processor->excludeDisabledProducts($excludeDisabledProducts);

        return $processor;
    }

    private function initProgressBar(OutputInterface $output, int $stepsCount): ProgressBar
    {
        $progressBar = $this->progressBarFactory->create([
            'output' => $output,
            'max' => $stepsCount
        ]);
        $progressBar->setFormat('<info>%message%</info> %current%/%max% [%bar%] %percent:3s%% %elapsed%');
        $progressBar->setMessage((string)__('Products processed:'));

        return $progressBar;
    }

    private function getOptionList(): array
    {
        return [
            new InputOption(
                self::EXCLUDE_DISABLED_PRODUCTS,
                '-e',
                InputOption::VALUE_NONE,
                (string)__('Don\'t generate URLs for disabled products')
            )
        ];
    }

    abstract protected function isNeedRedirect(): bool;
}
