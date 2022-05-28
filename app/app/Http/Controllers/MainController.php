<?php

namespace App\Http\Controllers;

use App\Services\GetContentService;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;

class MainController extends Controller
{
    private GetContentService $contentService;

    public function __construct(GetContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function index()
    {
        $current_date = date('Y-m-d');
        return view('main', ['current_date' => $current_date]);
    }

    public function getDates(Request $request): \Illuminate\Http\JsonResponse
    {
        $dates = [
            'dateFrom' => $request->get('dateFrom'),
            'dateTo' => $request->get('dateTo'),
        ];
        $data = $this->findCourse($dates);
        $values = array_map(function ($item) {
            return (float)$item;
        }, str_replace(',', '.', array_values($data['dollarRates'])));

        return response()->json(['dates' => $data['dates'], 'values' => $values]);
    }


    #[ArrayShape(['dollarRates' => "array", 'dates' => "array"])]
    public function findCourse($dates): array
    {
        $vars = $this->contentService->initVars($dates);

        $response = $vars['client']->request('GET', $vars['url']);
        $string = $response->getBody()->getContents();

        $rawDollarRates = $this->contentService->parse($string);

        $dollarRates = $this->contentService->getRates($rawDollarRates);
        $dates = $this->contentService->getDates($rawDollarRates);

        return ['dollarRates' => $dollarRates, 'dates' => $dates];
    }
}
