<?php

namespace App\Services;

use GuzzleHttp\Client;
use JetBrains\PhpStorm\ArrayShape;
use Laravie\Parser\Xml\Document;
use Laravie\Parser\Xml\Reader;
use Monolog\DateTimeImmutable;

class GetContentService
{
    #[ArrayShape([
        'begin' => "\DateTimeImmutable|false",
        'end' => "\DateTimeImmutable|false",
        'url' => "string",
        'client' => "\GuzzleHttp\Client"
    ])] public function initVars(array $dates): array
    {
        $begin = DateTimeImmutable::createFromFormat('Y-m-d', $dates['dateFrom']);
        $end = DateTimeImmutable::createFromFormat('Y-m-d', $dates['dateTo']);

        $url = sprintf(
            'https://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=%s&date_req2=%s&VAL_NM_RQ=R01235',
            $begin->format('d/m/Y'),
            $end->format('d/m/Y')
        );

        $client = new Client([
            'headers' => [
                'User-Agent' => 'curl/7.65.3',
                'Accept' => 'application/xml',
            ]
        ]);

        return [
            'begin' => $begin,
            'end' => $end,
            'url' => $url,
            'client' => $client,
        ];
    }

    public function parse(string $string): array
    {
        $xml = (new Reader(new Document()))->extract($string);

        return $xml->parse([
            'values' => ['uses' => 'Record[Value]'],
            'dates' => ['uses' => 'Record[::Date>Date]']
        ]);
    }

    public function getRates(array $rawDollarRates): array
    {
        return array_map(function ($item) {
            return $item['Value'];
        }, $rawDollarRates['values']);
    }

    public function getDates(array $rawDollarRates): array
    {
        return array_map(function ($item) {
            return $item['Date'];
        }, $rawDollarRates['dates']);
    }
}
