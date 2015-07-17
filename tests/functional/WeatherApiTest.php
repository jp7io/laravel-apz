<?php

use App\WeatherApi;

class WeatherApiTest extends \Codeception\TestCase\Test
{
    /**
     * @var \FunctionalTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testUpdateFromApiWeather()
    {
        $json_test = basename(dirname(__DIR__)).'/_data/weather_test.json';
        $api = new WeatherApi;
        $data = $api->update($json_test);

        $this->assertEquals($data->temp, 18);
    }

    public function testCache()
    {
        $json_test = basename(dirname(__DIR__)).'/_data/weather_test.json';

        $api = new WeatherApi;
        $api->update($json_test);
        $update_date1 = $api->data()->updated_at;

        $api2 = new WeatherApi;
        $update_date2 = $api2->data()->updated_at;

        $this->assertEquals($update_date1, $update_date2);
    }
}
