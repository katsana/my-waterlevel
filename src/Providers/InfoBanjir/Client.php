<?php namespace MyKatsana\WaterLevel\Providers\InfoBanjir;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Goutte\Client as Goutte;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;
use MyKatsana\WaterLevel\Contracts\Client as ClientContract;

class Client implements ClientContract
{
    /**
     * The request client implementation.
     *
     * @var \Goutte\Client
     */
    protected $client;

    /**
     * Request options.
     *
     * @var array
     */
    protected static $options = [];

    /**
     * Base URL
     *
     * @var string
     */
    protected static $url = 'http://infobanjir.water.gov.my/waterlevel_page.cfm?state=%s';

    /**
     * Setup a new water level retriever.
     *
     * @param  \Goutte\Client  $client
     */
    public function __construct(Goutte $client)
    {
        $this->client = $client;
    }

    /**
     * Get water level information on all state.
     *
     * @return array
     */
    public function execute()
    {
        $result = [];
        
        foreach ($this->getAvailableStateCode() as $code => $name) {
            $response = $this->executeByState($code);

            if (! empty($response)) {
                $result[$code] = $response;
            }
        }

        return $result;
    }

    /**
     * Get water level information based on state.
     *
     * @param  string  $code
     * @return \MyKatsana\WaterLevel\Contracts\Data[]
     */
    public function executeByState($code)
    {
        $request = $this->client->request('GET', sprintf(static::$url, $code), static::$options);

        try {
            $crawler = $request->filter('table.tbMain1_aa')->eq(1)->filter('tr');
            $filter = $crawler->slice(1);
            $result = $filter->each($this->getScrapperCallback($code));
        } catch (RequestException $e) {
            throw $e;
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        return array_filter($result, function ($value) {
            return ! is_null($value);
        });
    }

    /**
     * Get available state code.
     *
     * @return string[]
     */
    public function getAvailableStateCode()
    {
        return [
            'PEL' => 'Perlis',
            'KDH' => 'Kedah',
            'PNG' => 'Pulau Pinang',
            'PRK' => 'Perak',
            'SEL' => 'Selangor',
            'WLH' => 'Kuala Lumpur',
            'NSN' => 'Negeri Sembilan',
            'MLK' => 'Melaka',
            'JHR' => 'Johor',
            'PHG' => 'Pahang',
            'TRG' => 'Terengganu',
            'KEL' => 'Kelantan',
            'SRK' => 'Sarawak',
            'SBH' => 'Sabah',
        ];
    }

    /**
     * Get scrapper callback.
     *
     * @param  string  $code
     * @return callable
     */
    protected function getScrapperCallback($code)
    {
        return function (Crawler $content) use ($code) {
            $crawler = $content->filter('td');

            if (count($crawler) < 10 || ! $this->stationShouldBeOnline($crawler)) {
                return null;
            }

            return $this->getWaterLevelInformationForStation($crawler, $code);
        };
    }

    /**
     * Get water level information for station.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @param  string  $code
     * @return \MyKatsana\WaterLevel\Contracts\Data
     */
    protected function getWaterLevelInformationForStation(Crawler $crawler, $code)
    {
        $updated = $this->trimNonAscii($crawler->eq(4)->filter('font > strong')->first()->text());

        $info = new Data([
            'state' => $code,
            'station' => [
                'id' => $this->trimNonAscii($crawler->eq(0)->filter('a > font')->first()->text()),
                'name' => $this->trimNonAscii($crawler->eq(1)->text()),
                'district' => $this->trimNonAscii($crawler->eq(2)->text()),
                'basin' => $this->trimNonAscii($crawler->eq(3)->text()),
            ],
            'water' => floatval($this->trimNonAscii($crawler->eq(5)->text())),
            'meta' => [
                'normal' => floatval($this->trimNonAscii($crawler->eq(6)->text())),
                'alert' => floatval($this->trimNonAscii($crawler->eq(7)->text())),
                'warning' => floatval($this->trimNonAscii($crawler->eq(8)->text())),
                'danger' => floatval($this->trimNonAscii($crawler->eq(9)->text()))
            ],
            'updated_at' => Carbon::createFromFormat('d/m/Y - H:i', $updated, 'Asia/Kuala_Lumpur'),
        ]);

        return $info;
    }

    /**
     * Should only update online station.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return bool
     */
    protected function stationShouldBeOnline(Crawler $crawler)
    {
        $text = $this->trimNonAscii($crawler->eq(4)->filter('font')->first()->text());

        if ($text == 'Off-line') {
            return false;
        }

        return true;
    }

    protected function trimNonAscii($text)
    {
        $text = preg_replace('/[[:^print:]]/', '', Str::ascii($text));

        return trim($text);
    }
}
