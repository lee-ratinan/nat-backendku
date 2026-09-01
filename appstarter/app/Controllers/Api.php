<?php

namespace App\Controllers;

use App\Models\JourneyMasterModel;
use App\Models\JourneyTransportModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{

    public function getJourneyStats(): ResponseInterface
    {
        helper('math');
        // DATA
        $model      = new JourneyTransportModel();
        $model2     = new JourneyMasterModel();
        // Count everything that departs before end of today
        $end_today  = date(DATE_FORMAT_DB) . ' 23:59:59';
        $raw_data   = $model->where('journey_status', 'as_planned')->where('departure_date_time <=', $end_today)->findAll();
        $flight_cnt = 0;
        $distance   = 0.0;
        foreach ($raw_data as $row) {
            $distance += $row['distance_traveled'];
            if ('airplane' == $row['mode_of_transport']) {
                $flight_cnt += 1;
            }
        }
        // Count countries
        $countries   = $model2->select('country_code, COUNT(1) AS country_count')->where('date_entry <=', date(DATE_FORMAT_DB))->where('journey_status', 'as_planned')->groupBy('country_code')->findAll();
        $country_cnt = [];
        foreach ($countries as $country) {
            $country_cnt[$country['country_code']] = $country['country_count'];
        }
        // JSON
        $data = [
            'status' => 'OK',
            'data'   => [
                'flight_count'      => $flight_cnt,
                'country_count'     => count($country_cnt),
                'country_breakdown' => $country_cnt,
                'journey_distance'  => [
                    'km'   => $distance,
                    'mile' => round(kmToMiles($distance))
                ]
            ],
            'called'  => date(DATETIME_FORMAT_UI) . ' UTC'
        ];
        return $this->response->setJSON($data);
    }
}