<?php

namespace App\Service;

use App\Entity\Location;
use App\Entity\Measurement;
use App\Repository\MeasurementRepository;

class WeatherUtil
{
    public function __construct(
        protected MeasurementRepository $measurementRepository,
    ) {
    }

    /**
     * @return Measurement[]
     */
    public function getWeatherForLocation(Location $location): array
    {
        return $location->getFutureMeasurements()->toArray();
    }

    /**
     * @return Measurement[]
     */
    public function getWeatherForCountryAndCity(string $countryCode, string $city): array
    {
        return $this->measurementRepository->findByCountryAndCity($countryCode, $city);
    }

}