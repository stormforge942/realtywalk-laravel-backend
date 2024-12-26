<?php

namespace App\Services;

use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use App\Models\Polygon;

class PolygonService
{
    private $http;

    public function __construct()
    {
        $this->http = new Client(
            HttpClient::create(array(
                'headers' => array(
                    'user-agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0'
                )
            )));
        $this->http->setServerParameter('HTTP_USER_AGENT', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0');
    }

    /**
     * Removes polygons
     */
    public function removePolygons()
    {
        $apartments = [];
        $array = getZipCodes();

        foreach ($array['zipcodes'] as $zipCode) {
            $zpApartments = $this->getApartments($zipCode);
            $apartments = array_merge($apartments, $zpApartments);
        }

        $apartments = collect($apartments);
        $apartments = $apartments->unique()->all();

        foreach ($apartments as $apartment) {
            $polygons = Polygon::withDepth()->where('title', 'like', '%' . $apartment .'%')->get();
            $ids = $polygons->where('depth', '>', 1)->pluck('id')->all();
            Polygon::whereIn('id', $ids)->delete();
        }
    }

    /**
     * Gets apartments
     */
    private function getApartments($zipCode)
    {
        $crawler = $this->fetchApartments($zipCode);
        $pagination = $crawler->filter('.searchResults > .pageRange')->each(function ($node) {
            return $node->text();
        });

        $totalPages = 1;
        $apartments = [];

        if (count($pagination) > 0) {
            $totalPages = substr($pagination[0], -1);
        }

        for ($i = 1; $i<=$totalPages; $i++) {
            $crawler = $this->fetchApartments($zipCode, $i);
            $ul = $crawler->filter('.placards .placardContainer ul li article header div a .property-title span')
                ->each(function ($node) {
                    return $node->text();
                });

            $apartments = array_merge($apartments, $ul);
        }

        return $apartments;
    }

    /**
     * Fetches apartments by zip code
     */
    private function fetchApartments($zipCode, $page = 0)
    {
        $url = "https://www.apartments.com/houston-tx-" . $zipCode . ($page > 0  ? "/" . $page : "");
        $crawler = $this->http->request("GET", $url);

        return $crawler;
    }
}
