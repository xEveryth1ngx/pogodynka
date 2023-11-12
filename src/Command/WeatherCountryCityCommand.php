<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:country_city',
    description: 'Get weather by country code and city name',
)]
class WeatherCountryCityCommand extends Command
{
    public function __construct(
        protected WeatherUtil $util,
        protected LocationRepository $locationRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('country', InputArgument::REQUIRED, 'country code')
            ->addArgument('city', InputArgument::REQUIRED, 'city name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $country = $input->getArgument('country');
        $city = $input->getArgument('city');

        try {
            $measurements = $this->util->getWeatherForCountryAndCity($country, $city);
        } catch (\Exception) {
            return Command::FAILURE;
        }

        $io->writeln(sprintf('Location: %s %s', $country, $city));
        foreach ($measurements as $measurement) {
            $io->writeln(sprintf("\t%s: %s",
                $measurement->getDate()->format('Y-m-d'),
                $measurement->getCelsius()
            ));
        }

        return Command::SUCCESS;
    }
}
