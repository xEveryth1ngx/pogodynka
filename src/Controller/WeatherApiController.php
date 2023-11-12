<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        WeatherUtil $weatherUtil,
        #[MapQueryParameter] string $country,
        #[MapQueryParameter] string $city,
        #[MapQueryParameter] string $format,
        #[MapQueryParameter('twig')] bool $twig = false,
    ): Response
    {
        $measurements = $weatherUtil->getWeatherForCountryAndCity($country, $city);

        if ($format === 'json') {
            if ($twig) {
                return $this->render('weather_api/index.json.twig', [
                    'measurements' => $measurements,
                ]);
            }

            return $this->json([
                'measurements' => array_map(fn(Measurement $m) => [
                    'date' => $m->getDate()->format('Y-m-d'),
                    'celsius' => $m->getCelsius(),
                    'fahrenheit' => $m->getFahrenheit(),
                ], $measurements),
            ]);
        }
        elseif ($format === 'csv') {
            if ($twig) {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }

            $returnText = sprintf('%s,%s,%s,%s,%s', 'city', 'country', 'date', 'celsius', 'fahrenheit') . PHP_EOL;

            foreach ($measurements as $measurement) {
                $returnText .=  sprintf(
                    '%s,%s,%s,%s,%s' . PHP_EOL,
                    $city,
                    $country,
                    $measurement->getDate()->format('Y-m-d'),
                    $measurement->getCelsius(),
                    $measurement->getFahrenheit()
                );
            }

            return new Response($returnText, headers: ['content-type' => 'text/csv']);
        }

        return new Response(status: Response::HTTP_BAD_REQUEST);
    }
}
