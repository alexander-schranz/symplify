<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symplify\EasyCodingStandard\Application\EasyCodingStandardApplication;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\ValueObject\Option;

abstract class AbstractCheckCommand extends Command
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var EasyCodingStandardApplication
     */
    protected $easyCodingStandardApplication;

    /**
     * @var OutputFormatterCollector
     */
    private $outputFormatterCollector;

    /**
     * @required
     */
    public function autowireAbstractCheckCommand(
        Configuration $configuration,
        EasyCodingStandardApplication $easyCodingStandardApplication,
        OutputFormatterCollector $outputFormatterCollector
    ): void {
        $this->configuration = $configuration;
        $this->easyCodingStandardApplication = $easyCodingStandardApplication;
        $this->outputFormatterCollector = $outputFormatterCollector;
    }

    protected function configure(): void
    {
        $this->addArgument(
            Option::SOURCES,
            // optional is on purpose here, since path from ecs.php can se ubsed
            InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
            'The path(s) to be checked.'
        );

        $this->addOption(Option::FIX, null, null, 'Fix found violations.');

        $this->addOption(Option::CLEAR_CACHE, null, null, 'Clear cache for already checked files.');

        $this->addOption(
            Option::NO_PROGRESS_BAR,
            null,
            InputOption::VALUE_NONE,
            'Hide progress bar. Useful e.g. for nicer CI output.'
        );

        $this->addOption(
            Option::NO_ERROR_TABLE,
            null,
            InputOption::VALUE_NONE,
            'Hide error table. Useful e.g. for fast check of error count.'
        );

        $this->addOption(
            Option::OUTPUT_FORMAT,
            null,
            InputOption::VALUE_REQUIRED,
            'Select output format',
            ConsoleOutputFormatter::NAME
        );
    }

    protected function reportProcessedFiles(int $processedFileCount): int
    {
        $outputFormat = $this->configuration->getOutputFormat();
        $outputFormatter = $this->outputFormatterCollector->getByName($outputFormat);

        return $outputFormatter->report($processedFileCount);
    }

    protected function ensureSomeCheckersAreRegistered(): void
    {
        $checkerCount = $this->easyCodingStandardApplication->getCheckerCount();
        if ($checkerCount !== 0) {
            return;
        }

        throw new NoCheckersLoadedException(
            'No checkers were found. Register them in your config in "services:" '
            . 'section, load them via "--config <file>.yml" or "--set <set>" option.'
        );
    }
}
