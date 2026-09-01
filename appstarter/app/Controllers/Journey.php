<?php

namespace App\Controllers;

use App\Models\HealthActivityModel;
use App\Models\JourneyAccommodationModel;
use App\Models\JourneyAttractionModel;
use App\Models\JourneyBucketListModel;
use App\Models\JourneyHolidayModel;
use App\Models\JourneyMasterModel;
use App\Models\JourneyOperatorModel;
use App\Models\JourneyPortModel;
use App\Models\JourneyTransportModel;
use App\Models\LogActivityModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Journey extends BaseController
{

    const PERMISSION_REQUIRED = 'journey';
    private array $color_classes = [
        ['red', 'gold'],
        ['yellow', 'black'],
        ['blue', 'bronze'],
        ['green', 'silver']
    ];

    /************************************************************************
     * TRIP
     ************************************************************************/

    /**
     * This page shows all journeys (immigration records)
     * @return string
     */
    public function trip(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Trip',
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/trip',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'countries'    => lang('ListCountries.countries'),
        ];
        return view('journey_trip', $data);
    }

    /**
     * This API retrieves immigration data and return for DataTables
     * Table: journey_master
     * @return ResponseInterface
     */
    public function tripList(): ResponseInterface
    {
        $model              = new JourneyMasterModel();
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column       = 'journey_master.date_entry';
        $order_direction    = 'desc';
        $country_code       = $this->request->getPost('country_code');
        $year               = $this->request->getPost('year');
        $journey_status     = $this->request->getPost('journey_status');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search['value'], $country_code, $year, $journey_status);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * This page shows the trip edit form and the data of child rows associated to it
     * @param string $trip_id
     * @return string
     */
    public function tripEdit(string $trip_id = 'new'): string
    {
        $master_model       = new JourneyMasterModel();
        $trip_data          = [];
        $mode               = 'new';
        $page_title         = 'New Trip';
        $nonces             = [];
        $modes_of_transport = [];
        $health_records     = [];
        $country_code       = '';
        if (is_numeric($trip_id)) {
            $trip_data    = $master_model->getTripData($trip_id);
            if (empty($trip_data)) {
                throw PageNotFoundException::forPageNotFound();
            }
            $country_code = $trip_data['master_data']['country_code'];
            $mode         = 'edit';
            $page_title   = 'Edit Trip: ' . lang('ListCountries.countries.' . $trip_data['master_data']['country_code'] . '.common_name') . ' ' . date(DATE_FORMAT_UI, strtotime($trip_data['master_data']['date_entry']));
            $nonces       = [
                'transport'     => JourneyTransportModel::ID_NONCE,
                'accommodation' => JourneyAccommodationModel::ID_NONCE,
                'attraction'    => JourneyAttractionModel::ID_NONCE
            ];
            $modes_of_transport = (new JourneyTransportModel())->getModeOfTransport();
            $health_records     = (new HealthActivityModel())->getRecordTypes();
        }
        $session = session();
        $data    = [
            'page_title'         => $page_title,
            'slug_group'         => 'trip',
            'slug'               => '/office/journey/trip',
            'user_session'       => $session->user,
            'roles'              => $session->roles,
            'current_role'       => $session->current_role,
            'trip_data'          => $trip_data,
            'mode'               => $mode,
            'master_config'      => $master_model->getConfigurations([], $country_code, TRUE),
            'nonces'             => $nonces,
            'modes_of_transport' => $modes_of_transport,
            'health_records'     => $health_records
        ];
        return view('journey_trip_edit', $data);
    }

    /**
     * This API saves the trip (journey_master) data
     * @return ResponseInterface
     */
    public function tripSave(): ResponseInterface
    {
        $session = session();
        $model   = new JourneyMasterModel();
        $mode    = $this->request->getPost('mode');
        $data    = [
            'country_code'    => $this->request->getPost('country_code'),
            'date_entry'      => $this->request->getPost('date_entry'),
            'day_count'       => $this->request->getPost('day_count') ?? 0,
            'visa_info'       => $this->request->getPost('visa_info'),
            'journey_status'  => $this->request->getPost('journey_status'),
            'created_by'      => $session->user_id,
        ];
        $trip_code       = $this->request->getPost('trip_code');
        $date_exit       = $this->request->getPost('date_exit');
        $entry_port_id   = $this->request->getPost('entry_port_id');
        $exit_port_id    = $this->request->getPost('exit_port_id');
        $trip_tags       = $this->request->getPost('trip_tags');
        $journey_details = $this->request->getPost('journey_details');
        if (!empty($trip_code)) {
            $data['trip_code'] = $trip_code;
        }
        if (!empty($date_exit)) {
            $data['date_exit'] = $date_exit;
        } else {
            $data['date_exit'] = '2099-12-31';
        }
        if (!empty($entry_port_id)) {
            $data['entry_port_id'] = $entry_port_id;
        }
        if (!empty($exit_port_id)) {
            $data['exit_port_id'] = $exit_port_id;
        }
        if (!empty($trip_tags)) {
            $data['trip_tags'] = $trip_tags;
        }
        if (!empty($journey_details)) {
            $data['journey_details'] = $journey_details;
        }
        $states                 = $this->request->getPost('visited_states');
        $data['visited_states'] = '';
        if (is_array($states) && 0 < count($states)) {
            $data['visited_states'] = implode(',', $states);
        }
        try {
            if ('new' == $mode) {
                $inserted_id = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Trip has been added',
                        'url'    => base_url($session->locale . '/office/journey/trip/edit/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                $id = $this->request->getPost('id');
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Trip has been updated',
                        'url'    => base_url($session->locale . '/office/journey/trip/edit/' . ($id * $model::ID_NONCE))
                    ]);
                }
            }
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'There was some unknown error, please try again later.'
            ]);
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * This is the Trip Statistics page
     * @return string
     */
    public function tripStatistics(): string
    {
        $session  = session();
        $model    = new JourneyMasterModel();
        $raw_data = $model->where('journey_status', 'as_planned')->where('date_entry <=', date(DATE_FORMAT_DB))->findAll();
        $visited_countries_by_year    = [];
        $countries_by_visits          = [];
        foreach ($raw_data as $row) {
            $year_1 = intval(substr($row['date_entry'], 0, 4));
            $year_2 = (empty($row['date_exit']) ? intval(date('Y')) : intval(substr($row['date_exit'], 0, 4)));
            $years  = range($year_1, $year_2);
            foreach ($years as $year) {
                $visited_countries_by_year[$year][$row['country_code']] = 1;
            }
            $countries_by_visits[$row['country_code']] = (isset($countries_by_visits[$row['country_code']]) ? $countries_by_visits[$row['country_code']] + 1 : 1);
        }
        ksort($countries_by_visits);
        $data = [
            'page_title'                   => 'Trip Statistics',
            'slug_group'                   => 'trip',
            'slug'                         => '/office/journey/trip/statistics',
            'user_session'                 => $session->user,
            'roles'                        => $session->roles,
            'current_role'                 => $session->current_role,
            'visited_countries_by_year'    => $visited_countries_by_year,
            'countries_by_visits'          => $countries_by_visits,
            'countries'                    => lang('ListCountries.countries'),
            'countries_considered_home'    => ['SG', 'TH']
        ];
        return view('journey_trip_statistics', $data);
    }

    /**
     * This returns all financial data of everything in the journey_* tables
     * @return string
     */
    public function tripFinance(): string
    {
        $session                = session();
        $transport_model        = new JourneyTransportModel();
        $accommodation_model    = new JourneyAccommodationModel();
        $attraction_model       = new JourneyAttractionModel();
        $financial_data         = [];
        $all_currencies         = [];
        $raw_transport_data     = $transport_model->where('journey_status', 'as_planned')
            ->groupStart()->where('price_amount >', 0)->orWhere('charged_amount >', 0)->groupEnd()
            ->orderBy('departure_date_time', 'asc')->findAll();
        $raw_accommodation_data = $accommodation_model->where('journey_status', 'as_planned')
            ->groupStart()->where('price_amount >', 0)->orWhere('charged_amount >', 0)->groupEnd()
            ->orderBy('check_in_date', 'asc')->findAll();
        $raw_attraction_data    = $attraction_model->where('journey_status', 'as_planned')
            ->groupStart()->where('price_amount >', 0)->orWhere('charged_amount >', 0)->groupEnd()
            ->orderBy('attraction_date', 'asc')->findAll();
        foreach ($raw_transport_data as $row) {
            $year     = substr($row['departure_date_time'], 0, 4);
            $price    = $row['price_amount'];
            $currency = $row['price_currency_code'];
            if (0 < $row['charged_amount']) {
                $price    = $row['charged_amount'];
                $currency = $row['charged_currency_code'];
            }
            $financial_data[$year]['transport'][$currency][] = $price;
            $all_currencies[$currency] = 1;
        }
        foreach ($raw_accommodation_data as $row) {
            $year     = substr($row['check_in_date'], 0, 4);
            $price    = $row['price_amount'];
            $currency = $row['price_currency_code'];
            if (0 < $row['charged_amount']) {
                $price    = $row['charged_amount'];
                $currency = $row['charged_currency_code'];
            }
            $financial_data[$year]['accommodation'][$currency][] = $price;
            $all_currencies[$currency] = 1;
        }
        foreach ($raw_attraction_data as $row) {
            $year     = substr($row['attraction_date'], 0, 4);
            $price    = $row['price_amount'];
            $currency = $row['price_currency_code'];
            if (0 < $row['charged_amount']) {
                $price    = $row['charged_amount'];
                $currency = $row['charged_currency_code'];
            }
            $financial_data[$year]['attraction'][$currency][] = $price;
            $all_currencies[$currency] = 1;
        }
        ksort($all_currencies);
        $data = [
            'page_title'     => 'Finance',
            'slug_group'     => 'trip',
            'slug'           => '/office/journey/trip/finance',
            'user_session'   => $session->user,
            'roles'          => $session->roles,
            'current_role'   => $session->current_role,
            'color_classes'  => $this->color_classes,
            'financial_data' => $financial_data,
            'all_currencies' => array_keys($all_currencies),
        ];
        return view('journey_trip_finance', $data);
    }

    /************************************************************************
     * TRANSPORT
     ************************************************************************/

    /**
     * This page list all transport data
     * @return string
     */
    public function transport(): string
    {
        $session = session();
        $model   = new JourneyTransportModel();
        $data    = [
            'page_title'   => 'Transport',
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/transport',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'countries'    => lang('ListCountries.countries'),
            'modes'        => $model->getModeOfTransport()
        ];
        return view('journey_transport', $data);
    }

    /**
     * This API returns all transport data for DataTables
     * @return ResponseInterface
     */
    public function transportList(): ResponseInterface
    {
        $model              = new JourneyTransportModel();
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column       = 'journey_transport.departure_date_time';
        $order_direction    = 'desc';
        $country_code       = $this->request->getPost('country_code');
        $year               = $this->request->getPost('year');
        $journey_status     = $this->request->getPost('journey_status');
        $mode_of_transport  = $this->request->getPost('mode_of_transport');
        $is_domestic        = $this->request->getPost('is_domestic');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search['value'], $country_code, $year, $journey_status, $mode_of_transport, $is_domestic);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * This page show the edit form for the transport
     * @param string $transport_id
     * @param int $journey_id
     * @return string
     */
    public function transportEdit(string $transport_id = 'new', int $journey_id = 0): string
    {
        $session   = session();
        $model     = new JourneyTransportModel();
        $transport = [];
        if ('new' == $transport_id && 0 < $journey_id) {
            // new
            $mode         = 'new';
            $journey_id   = intval($journey_id / JourneyTransportModel::ID_NONCE);
            $transport_id = 0;
            $page_title   = 'New Transport';
        } else {
            // edit
            $mode         = 'edit';
            $transport_id = intval($transport_id / JourneyTransportModel::ID_NONCE);
            $transport    = $model->find($transport_id);
            $page_title   = 'Edit Transport ' . (empty($transport['flight_number']) ? '' : ' [' . $transport['flight_number'] . ']');
            $journey_id   = $transport['journey_id'];
        }
        $data    = [
            'page_title'   => $page_title,
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/transport',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'mode'         => $mode,
            'config'       => $model->getConfigurations(),
            'transport_id' => $transport_id,
            'journey_id'   => $journey_id,
            'transport'    => $transport,
            'parent'       => base_url($session->locale . '/office/journey/trip/edit/' . ($journey_id * JourneyMasterModel::ID_NONCE))
        ];
        return view('journey_transport_edit', $data);
    }

    /**
     * This API saves the transport data
     * @return ResponseInterface
     */
    public function transportSave(): ResponseInterface
    {
        helper('math');
        $session       = session();
        $model         = new JourneyTransportModel();
        $mode          = $this->request->getPost('mode');
        $data          = [
            'created_by' => $session->user_id,
        ];
        $operator_id       = $this->request->getPost('operator_id');
        $departure_port_id = $this->request->getPost('departure_port_id');
        $arrival_port_id   = $this->request->getPost('arrival_port_id');
        if (!empty($operator_id)) {
            $data['operator_id'] = $operator_id;
        }
        if (!empty($departure_port_id)) {
            $data['departure_port_id'] = $departure_port_id;
        }
        if (!empty($arrival_port_id)) {
            $data['arrival_port_id'] = $arrival_port_id;
        }
        if (!empty($departure_port_id) && !empty($arrival_port_id)) {
            $port_model = new JourneyPortModel();
            $departure  = $port_model->find($departure_port_id);
            $arrival    = $port_model->find($arrival_port_id);
            $data['distance_traveled'] = calculateDistance($departure['location_latitude'], $departure['location_longitude'], $arrival['location_latitude'], $arrival['location_longitude']);
            $data['haul_type']         = calculateHaul($data['distance_traveled']);
            $data['is_domestic']       = ($departure['country_code'] == $arrival['country_code'] ? 'D' : 'I');
        }
        $flight_number = $this->request->getPost('flight_number');
        $pnr_number    = $this->request->getPost('pnr_number');
        $departure_dt  = $this->request->getPost('departure_date_time');
        $departure_tz  = $this->request->getPost('departure_timezone');
        $arrival_dt    = $this->request->getPost('arrival_date_time');
        $arrival_tz    = $this->request->getPost('arrival_timezone');
        $is_time_known = $this->request->getPost('is_time_known');
        if (!empty($flight_number)) {
            $data['flight_number'] = $flight_number;
        }
        if (!empty($pnr_number)) {
            $data['pnr_number'] = $pnr_number;
        }
        if (!empty($departure_dt)) {
            $data['departure_date_time'] = $departure_dt;
        }
        if (!empty($departure_tz)) {
            $data['departure_timezone'] = $departure_tz;
        }
        if (!empty($arrival_dt)) {
            $data['arrival_date_time'] = $arrival_dt;
        }
        if (!empty($arrival_tz)) {
            $data['arrival_timezone'] = $arrival_tz;
        }
        if (!empty($is_time_known)) {
            $data['is_time_known'] = $is_time_known;
            if ('Y' == $is_time_known && !empty($departure_dt) && !empty($arrival_dt) && !empty($departure_tz) && !empty($arrival_tz)) {
                // DEPARTURE DATETIME
                try {
                    $departure             = new \DateTime($departure_dt, new \DateTimeZone($departure_tz));
                    $arrival               = new \DateTime($arrival_dt, new \DateTimeZone($arrival_tz));
                    $diff                  = $arrival->diff($departure);
                    $min                   = $diff->days * 24 * 60;
                    $min                  += $diff->h * 60;
                    $min                  += $diff->i;
                    $data['trip_duration'] = $min;
                } catch (\Exception $e) {
                    log_message('error', '! Journey / transportSave - ' . $e->getMessage());
                }
            }
        }
        $mode_of_transport     = $this->request->getPost('mode_of_transport');
        $craft_type            = $this->request->getPost('craft_type');
        $price_amount          = $this->request->getPost('price_amount');
        $price_currency_code   = $this->request->getPost('price_currency_code');
        $charged_amount        = $this->request->getPost('charged_amount');
        $charged_currency_code = $this->request->getPost('charged_currency_code');
        $journey_details       = $this->request->getPost('journey_details');
        $journey_status        = $this->request->getPost('journey_status');
        $google_drive_link     = $this->request->getPost('google_drive_link');
        if (!empty($mode_of_transport)) {
            $data['mode_of_transport'] = $mode_of_transport;
        }
        if (!empty($craft_type)) {
            $data['craft_type'] = $craft_type;
        }
        if (!empty($price_amount)) {
            $data['price_amount'] = $price_amount;
        }
        if (!empty($price_currency_code)) {
            $data['price_currency_code'] = $price_currency_code;
        }
        if (!empty($charged_amount)) {
            $data['charged_amount'] = $charged_amount;
        }
        if (!empty($charged_currency_code)) {
            $data['charged_currency_code'] = $charged_currency_code;
        }
        if (!empty($journey_details)) {
            $data['journey_details'] = $journey_details;
        }
        if (!empty($journey_status)) {
            $data['journey_status'] = $journey_status;
        }
        if (!empty($google_drive_link)) {
            $data['google_drive_link'] = $google_drive_link;
        }
        try {
            if ('new' == $mode) {
                $data['journey_id'] = $this->request->getPost('journey_id');
                $inserted_id        = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Transport has been added',
                        'url'    => base_url($session->locale . '/office/journey/transport/edit/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                $id = $this->request->getPost('id');
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Transport has been updated',
                        'url'    => base_url($session->locale . '/office/journey/transport/edit/' . ($id * $model::ID_NONCE))
                    ]);
                }
            }
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'There was some unknown error, please try again later.'
            ]);
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * This page shows the statistical data of the transport
     * @return string
     */
    public function transportStatistics(): string
    {
        $session    = session();
        $model      = new JourneyTransportModel();
        // Count everything that departs before end of today
        $end_today  = date(DATE_FORMAT_DB) . ' 23:59:59';
        $raw_data   = $model->where('journey_status', 'as_planned')->where('departure_date_time <=', $end_today)->orderBy('departure_date_time', 'asc')->findAll();
        // Prepare the data
        $generic_stats       = [];
        $distant_by_year     = []; // only flights
        $distant_by_year_sum = []; // only flights
        $haul_types          = [
            'S' => 0,
            'M' => 0,
            'L' => 0,
            'U' => 0
        ]; // only flights
        foreach ($raw_data as $row) {
            $year = substr($row['departure_date_time'], 0, 4);
            // Generic
            $generic_stats['count_by_mode'][$row['mode_of_transport']] = (isset($generic_stats['count_by_mode'][$row['mode_of_transport']]) ? $generic_stats['count_by_mode'][$row['mode_of_transport']] + 1 : 1);
            $generic_stats['distant_by_mode'][$row['mode_of_transport']] = (isset($generic_stats['distant_by_mode'][$row['mode_of_transport']]) ? $generic_stats['distant_by_mode'][$row['mode_of_transport']] + $row['distance_traveled'] : $row['distance_traveled']);
            // Distant
            if ('airplane' == $row['mode_of_transport']) {
                $distant_by_year[$year][] = $row['distance_traveled'];
                $distant_by_year_sum[$year] = (isset($distant_by_year_sum[$year]) ? $distant_by_year_sum[$year] + $row['distance_traveled'] : $row['distance_traveled']);
                $haul_types[$row['haul_type']] += 1;
            }
        }
        $data = [
            'page_title'          => 'Transport Statistics',
            'slug_group'          => 'trip',
            'slug'                => '/office/journey/transport/statistics',
            'user_session'        => $session->user,
            'roles'               => $session->roles,
            'current_role'        => $session->current_role,
            'color_classes'       => $this->color_classes,
            'countries'           => lang('ListCountries.countries'),
            'modes_of_transport'  => $model->getModeOfTransport(),
            'generic_stats'       => $generic_stats,
            'distant_by_year'     => $distant_by_year,
            'distant_by_year_sum' => $distant_by_year_sum,
            'distant_by_year_max' => max($distant_by_year_sum),
            'haul_types'          => $haul_types,
        ];
        return view('journey_transport_statistics', $data);
    }

    /************************************************************************
     * ACCOMMODATION
     ************************************************************************/

    /**
     * This page list accommodation data
     * @return string
     */
    public function accommodation(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Accommodation',
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/accommodation',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'countries'    => lang('ListCountries.countries'),
        ];
        return view('journey_accommodation', $data);
    }

    /**
     * This API returns accommodation for DataTables
     * @return ResponseInterface
     */
    public function accommodationList(): ResponseInterface
    {
        $model              = new JourneyAccommodationModel();
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column       = 'check_in_date';
        $order_direction    = 'desc';
        $country_code       = $this->request->getPost('country_code');
        $year               = $this->request->getPost('year');
        $journey_status     = $this->request->getPost('journey_status');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search['value'], $country_code, $year, $journey_status);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * This page edits the accommodation data
     * @param string $accommodation_id
     * @param int $journey_id
     * @return string
     */
    public function accommodationEdit(string $accommodation_id = 'new', int $journey_id = 0): string
    {
        $session       = session();
        $model         = new JourneyAccommodationModel();
        $accommodation = [];
        if ('new' == $accommodation_id && 0 < $journey_id) {
            // new
            $mode             = 'new';
            $journey_id       = intval($journey_id / JourneyAccommodationModel::ID_NONCE);
            $accommodation_id = 0;
            $page_title       = 'New Accommodation';
        } else {
            // edit
            $mode             = 'edit';
            $accommodation_id = intval($accommodation_id / JourneyAccommodationModel::ID_NONCE);
            $accommodation    = $model->find($accommodation_id);
            $page_title       = 'Edit Accommodation ' . (empty($accommodation['hotel_name']) ? '' : ' [' . $accommodation['hotel_name'] . ']');
            $journey_id       = $accommodation['journey_id'];
        }
        $data = [
            'page_title'       => $page_title,
            'slug_group'       => 'trip',
            'slug'             => '/office/journey/accommodation',
            'user_session'     => $session->user,
            'roles'            => $session->roles,
            'current_role'     => $session->current_role,
            'mode'             => $mode,
            'config'           => $model->getConfigurations(),
            'accommodation_id' => $accommodation_id,
            'journey_id'       => $journey_id,
            'accommodation'    => $accommodation,
            'parent'           => base_url($session->locale . '/office/journey/trip/edit/' . ($journey_id * JourneyMasterModel::ID_NONCE))
        ];
        return view('journey_accommodation_edit', $data);
    }

    /**
     * This API saves the accommodation data
     * @return ResponseInterface
     */
    public function accommodationSave(): ResponseInterface
    {
        $session                = session();
        $model                  = new JourneyAccommodationModel();
        $mode                   = $this->request->getPost('mode');
        $country_code           = $this->request->getPost('country_code');
        $check_in_date          = $this->request->getPost('check_in_date');
        $check_out_date         = $this->request->getPost('check_out_date');
        $accommodation_timezone = $this->request->getPost('accommodation_timezone');
        if (!empty($country_code)) {
            $data['country_code'] = $country_code;
        }
        if (!empty($check_in_date)) {
            $data['check_in_date'] = $check_in_date;
        }
        if (!empty($check_out_date)) {
            $data['check_out_date'] = $check_out_date;
        }
        if (!empty($accommodation_timezone)) {
            $data['accommodation_timezone'] = $accommodation_timezone;
        }
        if (!empty($check_in_date) && !empty($check_out_date)) {
            try {
                $str_chk_in          = substr($check_in_date, 0, 10) . ' 00:00:00';
                $str_chk_out         = substr($check_out_date, 0, 10) . ' 00:00:00';
                $check_in            = new \DateTime($str_chk_in, new \DateTimeZone('UTC'));
                $check_out           = new \DateTime($str_chk_out, new \DateTimeZone('UTC'));
                $diff                = $check_out->diff($check_in);
                $data['night_count'] = $diff->days;
            } catch (\Exception $e) {
                log_message('error', '! Journey / accommodationSave - ' . $e->getMessage());
            }
        }
        $hotel_name         = $this->request->getPost('hotel_name');
        $hotel_address      = $this->request->getPost('hotel_address');
        $booking_channel    = $this->request->getPost('booking_channel');
        $room_type          = $this->request->getPost('room_type');
        $breakfast_included = $this->request->getPost('breakfast_included');
        if (!empty($hotel_name)) {
            $data['hotel_name'] = $hotel_name;
        }
        if (!empty($hotel_address)) {
            $data['hotel_address'] = $hotel_address;
        }
        if (!empty($booking_channel)) {
            $data['booking_channel'] = $booking_channel;
        }
        if (!empty($room_type)) {
            $data['room_type'] = $room_type;
        }
        if (!empty($breakfast_included)) {
            $data['breakfast_included'] = $breakfast_included;
        }
        $price_amount          = $this->request->getPost('price_amount');
        $price_currency_code   = $this->request->getPost('price_currency_code');
        $charged_amount        = $this->request->getPost('charged_amount');
        $charged_currency_code = $this->request->getPost('charged_currency_code');
        $journey_details       = $this->request->getPost('journey_details');
        $journey_status        = $this->request->getPost('journey_status');
        $google_drive_link     = $this->request->getPost('google_drive_link');
        if (!empty($price_amount)) {
            $data['price_amount'] = $price_amount;
        }
        if (!empty($price_currency_code)) {
            $data['price_currency_code'] = $price_currency_code;
        }
        if (!empty($charged_amount)) {
            $data['charged_amount'] = $charged_amount;
        }
        if (!empty($charged_currency_code)) {
            $data['charged_currency_code'] = $charged_currency_code;
        }
        if (!empty($journey_details)) {
            $data['journey_details'] = $journey_details;
        }
        if (!empty($journey_status)) {
            $data['journey_status'] = $journey_status;
        }
        if (!empty($google_drive_link)) {
            $data['google_drive_link'] = $google_drive_link;
        }
        $data['created_by'] = $session->user_id;
        try {
            if ('new' == $mode) {
                $data['journey_id'] = $this->request->getPost('journey_id');
                $inserted_id        = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Accommodation has been added',
                        'url'    => base_url($session->locale . '/office/journey/accommodation/edit/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                $id = $this->request->getPost('id');
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Accommodation has been updated',
                        'url'    => base_url($session->locale . '/office/journey/accommodation/edit/' . ($id * $model::ID_NONCE))
                    ]);
                }
            }
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'There was some unknown error, please try again later.'
            ]);
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * This page shows the statistical data of the accommodation
     * @return string
     */
    public function accommodationStatistics(): string
    {
        $session    = session();
        $model      = new JourneyAccommodationModel();
        $by_country = [];
        $by_year    = [];
        $raw_data   = $model->where('journey_status', 'as_planned')->where('check_in_date <=', date(DATE_FORMAT_DB))->orderBy('check_in_date', 'asc')->findAll();
        foreach ($raw_data as $row) {
            // count it based on check-in date, although check-out is not in the same year...
            $year = substr($row['check_in_date'], 0, 4);
            $by_year[$year]['countries'][$row['country_code']]['list'][] = $row['night_count'];
            $by_year[$year]['countries'][$row['country_code']]['nights'] = (isset($by_year[$year]['countries'][$row['country_code']]['nights']) ? $by_year[$year]['countries'][$row['country_code']]['nights'] + $row['night_count'] : $row['night_count']);
            $by_country[$row['country_code']]['list'][]                  = $row['night_count'];
            $by_country[$row['country_code']]['nights']                  = (isset($by_country[$row['country_code']]['nights']) ? $by_country[$row['country_code']]['nights'] + $row['night_count'] : $row['night_count']);
        }
        // find by_year annual count, and max of all years
        $by_year_max = 0;
        foreach ($by_year as $year => $data) {
            $annual_count = 0;
            foreach ($data['countries'] as $country_code => $detail) {
                $annual_count += $detail['nights'];
            }
            $by_year[$year]['annual_count'] = $annual_count;
            if ($by_year_max < $annual_count) {
                $by_year_max = $annual_count;
            }
        }
        // find by_country max
        $by_country_max = 0;
        foreach ($by_country as $country_code => $detail) {
            if ($detail['nights'] > $by_country_max) {
                $by_country_max = $detail['nights'];
            }
        }
        ksort($by_country);
        $data = [
            'page_title'      => 'Accommodation Statistics',
            'slug_group'      => 'trip',
            'slug'            => '/office/journey/accommodation/statistics',
            'user_session'    => $session->user,
            'roles'           => $session->roles,
            'current_role'    => $session->current_role,
            'color_classes'   => $this->color_classes,
            'countries'       => lang('ListCountries.countries'),
            'by_year'         => $by_year,
            'by_year_max'     => $by_year_max,
            'by_year_half'    => $by_year_max / 2,
            'by_country'      => $by_country,
            'by_country_max'  => $by_country_max,
            'by_country_half' => $by_country_max / 2,
        ];
        return view('journey_accommodation_statistics', $data);
    }

    /************************************************************************
     * ATTRACTION
     ************************************************************************/

    /**
     * This page list all attraction data
     * @return string
     */
    public function attraction(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Attraction',
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/attraction',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'countries'    => lang('ListCountries.countries'),
        ];
        return view('journey_attraction', $data);
    }

    /**
     * This API returns all attraction data for DataTables
     * @return ResponseInterface
     */
    public function attractionList(): ResponseInterface
    {
        $model              = new JourneyAttractionModel();
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column       = 'attraction_date';
        $order_direction    = 'desc';
        $country_code       = $this->request->getPost('country_code');
        $year               = $this->request->getPost('year');
        $journey_status     = $this->request->getPost('journey_status');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search['value'], $country_code, $year, $journey_status);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * This page shows the edit form for the attraction
     * @param string $attraction_id
     * @param int $journey_id
     * @return string
     */
    public function attractionEdit(string $attraction_id = 'new', int $journey_id = 0): string
    {
        $session    = session();
        $model      = new JourneyAttractionModel();
        $attraction = [];
        if ('new' == $attraction_id && 0 < $journey_id) {
            // new
            $mode          = 'new';
            $journey_id    = intval($journey_id / JourneyAttractionModel::ID_NONCE);
            $attraction_id = 0;
            $page_title    = 'New Attraction';
        } else {
            // edit
            $mode          = 'edit';
            $attraction_id = intval($attraction_id / JourneyAttractionModel::ID_NONCE);
            $attraction    = $model->find($attraction_id);
            $page_title    = 'Edit Attraction ' . (empty($attraction['attraction_title']) ? '' : ' [' . $attraction['attraction_title'] . ']');
            $journey_id    = $attraction['journey_id'];
        }
        $data = [
            'page_title'    => $page_title,
            'slug_group'    => 'trip',
            'slug'          => '/office/journey/attraction',
            'user_session'  => $session->user,
            'roles'         => $session->roles,
            'current_role'  => $session->current_role,
            'mode'          => $mode,
            'config'        => $model->getConfigurations(),
            'attraction_id' => $attraction_id,
            'journey_id'    => $journey_id,
            'attraction'    => $attraction,
            'parent'        => base_url($session->locale . '/office/journey/trip/edit/' . ($journey_id * JourneyMasterModel::ID_NONCE))
        ];
        return view('journey_attraction_edit', $data);
    }

    /**
     * This API saves the attraction data
     * @return ResponseInterface
     */
    public function attractionSave(): ResponseInterface
    {
        $session                = session();
        $model                  = new JourneyAttractionModel();
        $mode                   = $this->request->getPost('mode');
        $country_code           = $this->request->getPost('country_code');
        $attraction_date        = $this->request->getPost('attraction_date');
        $attraction_title       = $this->request->getPost('attraction_title');
        $attraction_type        = $this->request->getPost('attraction_type');
        if (!empty($country_code)) {
            $data['country_code'] = $country_code;
        }
        if (!empty($attraction_date)) {
            $data['attraction_date'] = $attraction_date;
        }
        if (!empty($attraction_title)) {
            $data['attraction_title'] = $attraction_title;
        }
        if (!empty($attraction_type)) {
            $data['attraction_type'] = $attraction_type;
        }
        $price_amount          = $this->request->getPost('price_amount');
        $price_currency_code   = $this->request->getPost('price_currency_code');
        $charged_amount        = $this->request->getPost('charged_amount');
        $charged_currency_code = $this->request->getPost('charged_currency_code');
        $journey_details       = $this->request->getPost('journey_details');
        $journey_status        = $this->request->getPost('journey_status');
        $google_drive_link     = $this->request->getPost('google_drive_link');
        if (!empty($price_amount)) {
            $data['price_amount'] = $price_amount;
        }
        if (!empty($price_currency_code)) {
            $data['price_currency_code'] = $price_currency_code;
        }
        if (!empty($charged_amount)) {
            $data['charged_amount'] = $charged_amount;
        }
        if (!empty($charged_currency_code)) {
            $data['charged_currency_code'] = $charged_currency_code;
        }
        if (!empty($journey_details)) {
            $data['journey_details'] = $journey_details;
        }
        if (!empty($journey_status)) {
            $data['journey_status'] = $journey_status;
        }
        if (!empty($google_drive_link)) {
            $data['google_drive_link'] = $google_drive_link;
        }
        $data['created_by'] = $session->user_id;
        try {
            if ('new' == $mode) {
                $data['journey_id'] = $this->request->getPost('journey_id');
                $inserted_id        = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Attraction has been added',
                        'url'    => base_url($session->locale . '/office/journey/attraction/edit/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                $id = $this->request->getPost('id');
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Attraction has been updated',
                        'url'    => base_url($session->locale . '/office/journey/attraction/edit/' . ($id * $model::ID_NONCE))
                    ]);
                }
            }
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'There was some unknown error, please try again later.'
            ]);
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * This page shows the statistical data of the attraction
     * @return string
     */
    public function attractionStatistics(): string
    {
        $session    = session();
        $model      = new JourneyAttractionModel();
        $raw_data   = $model->where('journey_status', 'as_planned')->where('attraction_date <=', date(DATE_FORMAT_DB))->orderBy('attraction_date', 'asc')->findAll();
        $categories = [];
        $by_year    = [];
        foreach ($raw_data as $row) {
            if (empty($row['attraction_type'])) {
                continue;
            }
            $year = substr($row['attraction_date'], 0, 4);
            $category = $row['attraction_type'];
            $categories[$category] = (isset($categories[$category]) ? $categories[$category] + 1 : 1);
            $by_year[$year][$category] = (isset($by_year[$year][$category]) ? $by_year[$year][$category] + 1 : 1);
        }
        $data = [
            'page_title'      => 'Attraction Statistics',
            'slug_group'      => 'trip',
            'slug'            => '/office/journey/attraction/statistics',
            'user_session'    => $session->user,
            'roles'           => $session->roles,
            'current_role'    => $session->current_role,
            'categories'      => $categories,
            'by_year'         => $by_year,
            'colors'          => $this->color_classes,
        ];
        return view('journey_attraction_statistics', $data);
    }

    /************************************************************************
     * PORT
     ************************************************************************/

    /**
     * This page list all port data
     * @return string
     */
    public function port(): string
    {
        $session   = session();
        $model     = new JourneyPortModel();
        $data      = [
            'page_title'   => 'Port',
            'slug_group'   => 'trip-support-data',
            'slug'         => '/office/journey/port',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'countries'    => lang('ListCountries.countries'),
            'modes'        => $model->getModeOfTransport()
        ];
        return view('journey_port', $data);
    }

    /**
     * This API returns all port data for DataTables
     * @return ResponseInterface
     */
    public function portList(): ResponseInterface
    {
        $model              = new JourneyPortModel();
        $columns            = [
            '',
            'id',
            'country_code',
            'city_name',
            'mode_of_transport',
            'port_code_1',
            'port_name',
            'location_latitude'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $country_code       = $this->request->getPost('country_code');
        $mode_of_transport  = $this->request->getPost('mode_of_transport');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $country_code, $mode_of_transport);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * This page shows the edit form for the port
     * @param string $port_id
     * @return string
     */
    public function portEdit(string $port_id = 'new'): string
    {
        $session    = session();
        $model      = new JourneyPortModel();
        $port       = [];
        $page_title = 'New Port';
        $mode       = 'new';
        if ('new' != $port_id && is_numeric($port_id)) {
            $port_id    = intval($port_id / $model::ID_NONCE);
            $port       = $model->find($port_id);
            $page_title = 'Edit Port' . ($port['port_name'] ? ' [' . $port['port_name'] . ']' : '');
            $mode       = 'edit';
        }
        $data    = [
            'page_title'   => $page_title,
            'slug_group'   => 'trip-support-data',
            'slug'         => '/office/journey/port',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'config'       => $model->getConfigurations(),
            'port'         => $port,
            'mode'         => $mode
        ];
        return view('journey_port_edit', $data);
    }

    /**
     * This API saves the port data
     * @return ResponseInterface
     */
    public function portSave(): ResponseInterface
    {
        $session                = session();
        $model                  = new JourneyPortModel();
        $mode                   = $this->request->getPost('mode');
        $mode_of_transport      = $this->request->getPost('mode_of_transport');
        $port_code_1            = $this->request->getPost('port_code_1');
        $port_code_2            = $this->request->getPost('port_code_2');
        $country_code           = $this->request->getPost('country_code');
        $location_latitude      = $this->request->getPost('location_latitude');
        $location_longitude     = $this->request->getPost('location_longitude');
        $port_name              = $this->request->getPost('port_name');
        $port_local_name        = $this->request->getPost('port_local_name');
        $port_full_name         = $this->request->getPost('port_full_name');
        $city_name              = $this->request->getPost('city_name');
        if (!empty($mode_of_transport)) {
            $data['mode_of_transport'] = $mode_of_transport;
        }
        if (!empty($port_code_1)) {
            $data['port_code_1'] = $port_code_1;
        }
        if (!empty($port_code_2)) {
            $data['port_code_2'] = $port_code_2;
        }
        if (!empty($country_code)) {
            $data['country_code'] = $country_code;
        }
        $data['location_latitude'] = $location_latitude;
        $data['location_longitude'] = $location_longitude;
        if (!empty($port_name)) {
            $data['port_name'] = $port_name;
        }
        if (!empty($port_local_name)) {
            $data['port_local_name'] = $port_local_name;
        }
        if (!empty($port_full_name)) {
            $data['port_full_name'] = $port_full_name;
        }
        if (!empty($city_name)) {
            $data['city_name'] = $city_name;
        }
        $data['created_by'] = $session->user_id;
        try {
            if ('new' == $mode) {
                $inserted_id        = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Port has been added',
                        'url'    => base_url($session->locale . '/office/journey/port/edit/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                $id = $this->request->getPost('id');
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Port has been updated',
                        'url'    => base_url($session->locale . '/office/journey/port/edit/' . ($id * $model::ID_NONCE))
                    ]);
                }
            }
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'There was some unknown error, please try again later.'
            ]);
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * This page shows the statistical data of the port
     * @return string
     */
    public function portStatistics(): string
    {
        $session         = session();
        $transport_model = new JourneyTransportModel();
        $port_model      = new JourneyPortModel();
        $raw_data        = $transport_model->where('journey_status', 'as_planned')->where('departure_date_time <=', date(DATE_FORMAT_DB))->orderBy('departure_date_time', 'asc')->findAll();
        $port_ids        = [];
        $by_year         = [];
        $by_port         = [];
        foreach ($raw_data as $row) {
            $year = substr($row['departure_date_time'], 0, 4);
            $port_ids[$row['departure_port_id']] = $row['departure_port_id'];
            $port_ids[$row['arrival_port_id']]   = $row['arrival_port_id'];
            $by_year[$year][$row['departure_port_id']] = (isset($by_year[$year][$row['departure_port_id']]) ? $by_year[$year][$row['departure_port_id']] + 1 : 1);
            $by_year[$year][$row['arrival_port_id']]   = (isset($by_year[$year][$row['arrival_port_id']]) ? $by_year[$year][$row['arrival_port_id']] + 1 : 1);
            $by_port[$row['departure_port_id']] = (isset($by_port[$row['departure_port_id']]) ? $by_port[$row['departure_port_id']] + 1 : 1);
            $by_port[$row['arrival_port_id']]   = (isset($by_port[$row['arrival_port_id']]) ? $by_port[$row['arrival_port_id']] + 1 : 1);
        }
        $port_ids  = array_keys($port_ids);
        $raw_ports = $port_model->whereIn('id', $port_ids)->findAll();
        $ports     = [];
        foreach ($raw_ports as $row) {
            $ports[$row['id']] = [
                'name'         => $row['port_name'],
                'code'         => $row['port_code_1'],
                'type'         => $row['mode_of_transport'],
                'country_code' => $row['country_code']
            ];
        }
        arsort($by_port);
        $data     = [
            'page_title'   => 'Port Statistics',
            'slug_group'   => 'trip-support-data',
            'slug'         => '/office/journey/port/statistics',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'by_year'      => $by_year,
            'by_port'      => $by_port,
            'ports'        => $ports,
            'modes'        => $port_model->getModeOfTransport(),
            'colors'       => $this->color_classes
        ];
        return view('journey_port_statistics', $data);
    }

    /************************************************************************
     * OPERATOR
     ************************************************************************/

    /**
     * @return string
     */
    public function operator(): string
    {
        $session = session();
        $model   = new JourneyOperatorModel();
        $data    = [
            'page_title'   => 'Operator',
            'slug_group'   => 'trip-support-data',
            'slug'         => '/office/journey/operator',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'modes'        => $model->getModeOfTransport()
        ];
        return view('journey_operator', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function operatorList(): ResponseInterface
    {
        $model              = new JourneyOperatorModel();
        $columns            = [
            '',
            'id',
            'mode_of_transport',
            'operator_code_1',
            'operator_name'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $mode_of_transport  = $this->request->getPost('mode_of_transport');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $mode_of_transport ?? '');
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * Page to edit operator
     * @param string $operator_id
     * @return string
     */
    public function operatorEdit(string $operator_id = 'new'): string
    {
        $session    = session();
        $model      = new JourneyOperatorModel();
        $operator   = [];
        $page_title = 'New Operator';
        $mode       = 'new';
        if ('new' != $operator_id && is_numeric($operator_id)) {
            $operator_id = intval($operator_id / $model::ID_NONCE);
            $operator    = $model->find($operator_id);
            $page_title  = 'Edit Operator' . ($operator['operator_name'] ? ' [' . $operator['operator_name'] . ']' : '');
            $mode        = 'edit';
        }
        $data    = [
            'page_title'   => $page_title,
            'slug_group'   => 'trip-support-data',
            'slug'         => '/office/journey/operator',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'config'       => $model->getConfigurations(),
            'operator'     => $operator,
            'mode'         => $mode
        ];
        return view('journey_operator_edit', $data);
    }

    /**
     * Save operator data
     * @return ResponseInterface
     */
    public function operatorSave(): ResponseInterface
    {
        $session                 = session();
        $model                   = new JourneyOperatorModel();
        $mode                    = $this->request->getPost('mode');
        $operator_code_1         = $this->request->getPost('operator_code_1');
        $operator_code_2         = $this->request->getPost('operator_code_2');
        $operator_callsign       = $this->request->getPost('operator_callsign');
        $operator_name           = $this->request->getPost('operator_name');
        $operator_logo_file_name = $this->request->getPost('operator_logo_file_name');
        $mode_of_transport       = $this->request->getPost('mode_of_transport');
        if (!empty($operator_code_1)) {
            $data['operator_code_1'] = $operator_code_1;
        }
        if (!empty($operator_code_2)) {
            $data['operator_code_2'] = $operator_code_2;
        }
        if (!empty($operator_callsign)) {
            $data['operator_callsign'] = $operator_callsign;
        }
        $data['operator_name'] = $operator_name;
        if (!empty($operator_logo_file_name)) {
            $data['operator_logo_file_name'] = $operator_logo_file_name;
        }
        if (!empty($mode_of_transport)) {
            $data['mode_of_transport'] = $mode_of_transport;
        }
        $data['created_by'] = $session->user_id;
        try {
            if ('new' == $mode) {
                $inserted_id        = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Operator has been added',
                        'url'    => base_url($session->locale . '/office/journey/operator/edit/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                $id = $this->request->getPost('id');
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Operator has been updated',
                        'url'    => base_url($session->locale . '/office/journey/operator/edit/' . ($id * $model::ID_NONCE))
                    ]);
                }
            }
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'There was some unknown error, please try again later.'
            ]);
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * This page shows the statistical data of the port
     * @return string
     */
    public function operatorStatistics(): string
    {
        $session         = session();
        $transport_model = new JourneyTransportModel();
        $operator_model  = new JourneyOperatorModel();
        $raw_data        = $transport_model->where('journey_status', 'as_planned')->where('departure_date_time <=', date(DATE_FORMAT_DB))->orderBy('departure_date_time', 'asc')->findAll();
        $operator_ids    = [];
        $by_year         = [];
        $by_operator     = [];
        foreach ($raw_data as $row) {
            $year                                = substr($row['departure_date_time'], 0, 4);
            $operator_ids[$row['operator_id']]   = $row['operator_id'];
            $by_year[$year][$row['operator_id']] = (isset($by_year[$year][$row['operator_id']]) ? $by_year[$year][$row['operator_id']] + 1 : 1);
            $by_operator[$row['operator_id']]    = (isset($by_operator[$row['operator_id']]) ? $by_operator[$row['operator_id']] + 1 : 1);
        }
        $operator_ids  = array_keys($operator_ids);
        $raw_operators = $operator_model->whereIn('id', $operator_ids)->findAll();
        $operators     = [];
        foreach ($raw_operators as $row) {
            $operators[$row['id']] = [
                'name' => $row['operator_name'],
                'code' => $row['operator_code_1'],
                'type' => $row['mode_of_transport'],
                'file' => $row['operator_logo_file_name']
            ];
        }
        $modes          = $operator_model->getModeOfTransport();
        $modes_of_trans = [];
        foreach ($modes as $key => $val) {
            $modes_of_trans[$key] = explode('</i>', $val)[0] . '</i>';
        }
        arsort($by_operator);
        $data           = [
            'page_title'      => 'Operator Statistics',
            'slug_group'      => 'trip-support-data',
            'slug'            => '/office/journey/operator/statistics',
            'user_session'    => $session->user,
            'roles'           => $session->roles,
            'current_role'    => $session->current_role,
            'colors'          => $this->color_classes,
            'by_year'         => $by_year,
            'by_operator'     => $by_operator,
            'operators'       => $operators,
            'modes'           => $modes_of_trans
        ];
        return view('journey_operator_statistics', $data);
    }

    /**
     * This page shows the statistical data of the aircraft
     * @return string
     */
    public function aircraftStatistics(): string
    {
        $session         = session();
        $types           = [
            'AIRBUS 319' => 'Narrow-body',
            'AIRBUS 320' => 'Narrow-body',
            'AIRBUS 321' => 'Narrow-body',
            'AIRBUS 330' => 'Wide-body',
            'AIRBUS 340' => 'Wide-body',
            'AIRBUS 350' => 'Wide-body',
            'AIRBUS 380' => 'Wide-body',
            'BOEING 737' => 'Narrow-body',
            'BOEING 747' => 'Wide-body',
            'BOEING 777' => 'Wide-body',
            'BOEING 787' => 'Wide-body',
        ];
        $transport_model = new JourneyTransportModel();
        $raw_data        = $transport_model->select('craft_type, COUNT(*) AS cnt')->where('craft_type IS NOT NULL')->where('journey_status', 'as_planned')->where('departure_date_time <=', date(DATE_FORMAT_DB))->groupBy('craft_type')->findAll();
        $aircrafts       = [];
        $by_manufacturer = [];
        foreach ($raw_data as $row) {
            $craft   = $row['craft_type'];
            $model   = substr($craft, 0, 10);
            $explode = explode(' ', $model);
            // Type
            if (isset($types[$model])) {
                $aircrafts[$types[$model]][$model] = (isset($aircrafts[$types[$model]][$model]) ? $aircrafts[$types[$model]][$model] + $row['cnt'] : $row['cnt']);
            } else {
                $aircrafts['Other'][$craft] = (isset($aircrafts['Other'][$craft]) ? $aircrafts['Other'][$craft] + $row['cnt'] : $row['cnt']);
            }
            // Manufacturer
            $manufacturer = strtoupper($explode[0]);
            if (in_array($manufacturer, ['AIRBUS', 'BOEING', 'EMBRAER'])) {
                $by_manufacturer[$manufacturer] = (isset($by_manufacturer[$manufacturer]) ? $by_manufacturer[$manufacturer] + $row['cnt'] : $row['cnt']);
            }
        }
        $data = [
            'page_title'      => 'Aircraft Statistics',
            'slug_group'      => 'trip-support-data',
            'slug'            => '/office/journey/operator/aircraft/statistics',
            'user_session'    => $session->user,
            'roles'           => $session->roles,
            'current_role'    => $session->current_role,
            'aircrafts'       => $aircrafts,
            'by_manufacturer' => $by_manufacturer,
            'colors'          => $this->color_classes
        ];
        return view('journey_operator_aircraft_statistics', $data);
    }

    /************************************************************************
     * HOLIDAY
     ************************************************************************/

    /**
     * @return string
     */
    public function holiday(): string
    {
        $session   = session();
        $data           = [
            'page_title'   => 'Holiday',
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/holiday',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
        ];
        return view('journey_holiday', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function holidayList(): ResponseInterface
    {
        $model              = new JourneyHolidayModel();
        $search             = $this->request->getPost('search');
        $search_value       = $search['value'];
        $country_code       = $this->request->getPost('country_code');
        $start              = $this->request->getPost('start');
        $end                = $this->request->getPost('end');
        $result             = $model->getDataTables($search_value, $country_code, $start, $end);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * @param string $holiday_id
     * @return string
     */
    public function holidayEdit(string $holiday_id = 'new'): string
    {
        $session       = session();
        $holiday_model = new JourneyHolidayModel();
        $holiday       = [];
        $page_title    = 'New Holiday';
        $mode          = 'new';
        if (is_numeric($holiday_id)) {
            $holiday    = $holiday_model->getHoliday($holiday_id);
            if (empty($holiday)) {
                throw PageNotFoundException::forPageNotFound();
            }
            $page_title = 'Edit Holiday [' . $holiday['holiday_name'] . ']';
            $mode       = 'edit';
        }
        $data    = [
            'page_title'   => $page_title,
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/holiday',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'holiday'      => $holiday,
            'mode'         => $mode,
            'config'       => $holiday_model->getConfigurations()
        ];
        return view('journey_holiday_edit', $data);
    }

    /**
     * Save holiday
     * @return ResponseInterface
     */
    public function holidaySave(): ResponseInterface
    {
        $session = session();
        $model   = new JourneyHolidayModel();
        $mode    = $this->request->getPost('mode');
        $data    = [
            'country_code'    => $this->request->getPost('country_code'),
            'region_code'     => $this->request->getPost('region_code'),
            'holiday_date'    => $this->request->getPost('holiday_date'),
            'holiday_date_to' => $this->request->getPost('holiday_date_to'),
            'holiday_name'    => $this->request->getPost('holiday_name'),
            'created_by'      => $session->user_id,
        ];
        try {
            if ('new' == $mode) {
                $inserted_id = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Holiday has been added',
                        'url'    => base_url($session->locale . '/office/journey/holiday/edit/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                $id = $this->request->getPost('id');
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Holiday has been updated',
                        'url'    => base_url($session->locale . '/office/journey/holiday/edit/' . ($id * $model::ID_NONCE))
                    ]);
                }
            }
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'There was some unknown error, please try again later.'
            ]);
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    public function map(): string
    {
        $session       = session();
        $wishlist_raw  = ['AS', 'AT', 'BR', 'CA', 'CH', 'DE', 'DK', 'ES', 'FJ', 'FR', 'GB', 'GR', 'IS', 'IT', 'KR', 'NL', 'NO', 'NZ', 'PF', 'PG', 'SE', 'TR'];
        $banned_raw    = ['AF', 'CN', 'CU', 'IL', 'IQ', 'IR', 'KH', 'KP', 'LA', 'MM', 'RU', 'SY'];
        // Wishlist
        $wishlist      = ['name'  => 'Wishlist', 'color' => '#dcd368', 'data'  => []];
        foreach ($wishlist_raw as $country_code) {
            $wishlist['data'][] = ['id' => $country_code, 'detail' => 'Wishlist'];
        }
        // Banned
        $banned        = ['name'  => 'Banned', 'color' => '#dc6a68', 'data'  => []];
        foreach ($banned_raw as $country_code) {
            $banned['data'][] = ['id' => $country_code, 'detail' => 'Banned'];
        }
        // Visited
        $visited       = ['name'  => 'Visited Countries', 'color' => '#7ddd68', 'data'  => []];
        $journey_model = new JourneyMasterModel();
        $countries_yr  = [];
        $states        = [];
        $all_visited   = $journey_model->where('journey_status', 'as_planned')->where('country_code !=', 'SG')->findAll();
        foreach ($all_visited as $trip) {
            // Countries
            $start_year = substr($trip['date_entry'], 0, 4);
            $end_year   = (empty($trip['date_exit']) ? date('Y') : substr($trip['date_exit'], 0, 4));
            for ($year = $start_year; $year <= $end_year; $year++) {
                $countries_yr[$trip['country_code']][$year] = 1;
            }
            // States
            if (!empty($trip['visited_states'])) {
                $visited_states = explode(',', $trip['visited_states']);
                foreach ($visited_states as $visited_state) {
                    // Use key like this to ensure uniqueness
                    $states[$trip['country_code']][$visited_state] = 1;
                }
            }
        }
        // Finalize Visited Countries Structure
        $final_visited_countries = [];
        foreach ($countries_yr as $country_code => $country_years) {
            $list_of_years = [];
            foreach ($country_years as $year => $one) {
                $list_of_years[] = $year;
            }
            $final_visited_countries[] = ['id' => $country_code, 'detail' => 'Visited this country in ' . implode(', ', $list_of_years)];
        }
        $final_visited_countries[] = ['id' => 'TH', 'detail' => 'Lived in Thailand from 1989 to 2014'];
        $final_visited_countries[] = ['id' => 'SG', 'detail' => 'Visited Singapore in 2006, 2012, and moved in 2014'];
        $visited['data'] = $final_visited_countries;
        // Finalize States Structure
        $final_visited_states = [];
        foreach ($states as $country_code => $country_states) {
            foreach ($country_states as $state_code => $one) {
                $final_visited_states[$country_code][] = $state_code;
            }
        }
        $final_visited_states['SG'] = ['SG-01', 'SG-02', 'SG-03', 'SG-04', 'SG-05'];
        // Data
        $data          = [
            'page_title'        => 'Map',
            'slug_group'        => 'trip',
            'slug'              => '/office/journey/map',
            'user_session'      => $session->user,
            'roles'             => $session->roles,
            'current_role'      => $session->current_role,
            'visited_countries' => [
                $visited,
                $wishlist,
                $banned
            ],
            'visited_states'    => $final_visited_states
        ];
        return view('journey_map', $data);
    }

    /************************************************************************
     * EXPORT
     ************************************************************************/

    /**
     * @return string
     */
    public function bucketList(): string
    {
        $session = session();
        $model   = new JourneyBucketListModel();
        $data    = [
            'page_title'   => 'Bucket List',
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/bucket-list',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'categories'   => $model->getCategoryCode()
        ];
        return view('journey_bucket_list', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function bucketListRetrieve(): ResponseInterface
    {
        $model              = new JourneyBucketListModel();
        $columns            = [
            '',
            'activity_name',
            'category_code',
            'activity_location',
            'completed_dates',
            ''
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $category_code      = $this->request->getPost('category_code');
        $bucket_status      = $this->request->getPost('bucket_status');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $category_code ?? '', $bucket_status ?? '');
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * @param string $bucket_list_id
     * @return string
     */
    public function bucketListEdit(string $bucket_list_id = 'new'): string
    {
        $session       = session();
        $bucket_model  = new JourneyBucketListModel();
        $bucket_item   = [];
        $page_title    = 'New Bucket List Item';
        $mode          = 'new';
        if (is_numeric($bucket_list_id)) {
            $bucket_list_id = $bucket_list_id/$bucket_model::ID_NONCE;
            $bucket_item    = $bucket_model->find($bucket_list_id);
            if (empty($bucket_item)) {
                throw PageNotFoundException::forPageNotFound();
            }
            $page_title = 'Edit Bucket List [' . $bucket_item['activity_name'] . ']';
            $mode       = 'edit';
        }
        $data    = [
            'page_title'   => $page_title,
            'slug_group'   => 'trip',
            'slug'         => '/office/journey/bucket-list',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'mode'         => $mode,
            'bucket_item'  => $bucket_item,
            'config'       => $bucket_model->getConfigurations()
        ];
        return view('journey_bucket_list_edit', $data);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function bucketListSave(): ResponseInterface
    {
        $mode          = $this->request->getPost('mode');
        $bucket_model  = new JourneyBucketListModel();
        $log_model     = new LogActivityModel();
        $session       = session();
        $id            = $this->request->getPost('id');
        $data          = [];
        $fields        = [
            'activity_name',
            'activity_name_local',
            'activity_slug',
            'activity_location',
            'country_code',
            'category_code',
            'completed_dates',
            'description',
            'trip_codes',
            'building_height',
            'building_built_year',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        if ('edit' == $mode) {
            if ($bucket_model->update($id, $data)) {
                $log_model->insertTableUpdate('journey_bucket_list', $id, $data, $session->user_id);
                $new_id = $id * $bucket_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the bucket list item.',
                    'redirect' => base_url($session->locale . '/office/journey/bucket-list/edit/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $bucket_model->insert($data)) {
                $log_model->insertTableUpdate('journey_bucket_list', $id, $data, $session->user_id);
                $new_id = $id * $bucket_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created new bucket list item.',
                    'redirect' => base_url($session->locale . '/office/journey/bucket-list/edit/' . $new_id)
                ]);
            }
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * @return string
     */
    public function bucketListStatistics(): string
    {
        $session         = session();
        $bucket_model    = new JourneyBucketListModel();
        $category_count  = [];
        $completed_count = [];
        $year_accomplishment_count = [];
        $all_items = $bucket_model->findAll();
        foreach ($all_items as $item) {
            $category = $item['category_code'];
            $category_count[$category] = (isset($category_count[$category]) ? $category_count[$category] + 1 : 1);
            $completed = 'N';
            if (!empty($item['completed_dates'])) {
                $completed = 'Y';
            }
            $completed_count[$category][$completed] = (isset($completed_count[$category][$completed]) ? $completed_count[$category][$completed] + 1 : 1);
            if (!empty($item['completed_dates'])) {
                $completed_dates = explode(',', $item['completed_dates']);
                foreach ($completed_dates as $completed_date) {
                    $year = substr($completed_date, 0, 4);
                    $year_accomplishment_count[$year] = (isset($year_accomplishment_count[$year]) ? $year_accomplishment_count[$year] + 1 : 1);
                }
            }
        }
        ksort($year_accomplishment_count);
        $category_chart = [];
        $categories = $bucket_model->getCategoryCode();
        foreach ($category_count as $category => $count) {
            $category_chart[] = [
                'category' => $categories[$category],
                'count'    => $count
            ];
        }
        $completed_chart = [];
        $completes       = ['Y' => 'Completed', 'N' => 'Wishlisted'];
        $settings        = ['Y' => '0xa6e22e', 'N' => '0xf92672'];
        ksort($completed_count);
        foreach ($completed_count as $category => $counts) {
            ksort($counts);
            foreach ($counts as $status => $count) {
                $completed_chart[$categories[$category]][] = [
                    'status'   => $completes[$status],
                    'count'    => $count,
                    'settings' => [
                        'fill' => 'am5.color('.$settings[$status].')'
                    ]
                ];
            }
        }
        $year_chart = [];
        $first_year = array_keys($year_accomplishment_count)[0];
        for ($y = $first_year; $y <= date('Y'); $y++) {
            $year_chart[] = [
                'year'  => "$y",
                'count' => $year_accomplishment_count[$y] ?? 0
            ];
        }
        $data = [
            'page_title'                => 'Bucket List Statistics',
            'slug_group'                => 'trip',
            'slug'                      => '/office/journey/bucket-list/statistics',
            'user_session'              => $session->user,
            'roles'                     => $session->roles,
            'current_role'              => $session->current_role,
            'category_count'            => $category_chart,
            'completed_count'           => $completed_chart,
            'year_accomplishment_count' => $year_chart
        ];
        return view('journey_bucket_list_statistics', $data);
    }

    /************************************************************************
     * EXPORT
     ************************************************************************/

    public function export(): string
    {
        $journey_master     = new JourneyMasterModel();
        $journey_transport  = new JourneyTransportModel();
        $journey_accom      = new JourneyAccommodationModel();
        $journey_attraction = new JourneyAttractionModel();
        $journey_data       = [];
        $journey_raw        = $journey_master->select('journey_master.*, entry_port.port_name AS entry_port_name, entry_port.port_code_1 AS entry_port_code, exit_port.port_name AS exit_port_name, exit_port.port_code_1 AS exit_port_code')
            ->join('journey_port AS entry_port', 'journey_master.entry_port_id = entry_port.id', 'left outer')
            ->join('journey_port AS exit_port',  'journey_master.exit_port_id = exit_port.id', 'left outer')
            ->orderBy('date_entry', 'asc')->findAll();
        $transport_raw      = $journey_transport->select('journey_transport.*, journey_operator.operator_name, port_departure.port_name AS departure_port_name, port_departure.port_code_1 AS departure_port_code, port_arrival.port_name AS arrival_port_name, port_arrival.port_code_1 AS arrival_port_code')
            ->join('journey_operator', 'journey_transport.operator_id = journey_operator.id', 'left outer')
            ->join('journey_port AS port_departure', 'journey_transport.departure_port_id = port_departure.id', 'left outer')
            ->join('journey_port AS port_arrival',   'journey_transport.arrival_port_id = port_arrival.id', 'left outer')
            ->orderBy('departure_date_time', 'asc')->findAll();
        $accom_raw          = $journey_accom->orderBy('check_in_date', 'asc')->findAll();
        $attraction_raw     = $journey_attraction->orderBy('attraction_date', 'asc')->findAll();
        foreach ($journey_raw as $row) {
            $journey_data[$row['id']]['master'] = $row;
        }
        foreach ($transport_raw as $row) {
            $journey_data[$row['journey_id']]['transport'][] = $row;
        }
        foreach ($accom_raw as $row) {
            if (0 < $row['journey_id']) {
                $journey_data[$row['journey_id']]['accommodation'][] = $row;
            }
        }
        foreach ($attraction_raw as $row) {
            if (0 < $row['journey_id']) {
                $journey_data[$row['journey_id']]['attraction'][] = $row;
            }
        }
        $data = [
            'journey'   => $journey_data,
            'countries' => lang('ListCountries.countries')
        ];
        return view('journey_export', $data);
    }

    private function fix(): void
    {
        helper('math');
        $journey_transport = new JourneyTransportModel();
        $transport_raw     = $journey_transport->select('journey_transport.*, 
            port_departure.port_name AS departure_port_name, port_departure.location_latitude AS lat1, port_departure.location_longitude AS lon1,
            port_arrival.port_name AS arrival_port_name, port_arrival.location_latitude AS lat2, port_arrival.location_longitude AS lon2')
            ->join('journey_port AS port_departure', 'journey_transport.departure_port_id = port_departure.id', 'left outer')
            ->join('journey_port AS port_arrival',   'journey_transport.arrival_port_id = port_arrival.id', 'left outer')
            ->orderBy('id', 'asc')->findAll();
        echo '<pre>';
        foreach ($transport_raw as $row) {
//            echo '-- ROW DATA --<br>';
            $set = [];
            if (empty($row['distance_traveled'])) {
                echo '-- FR ' . $row['departure_port_name'] . ' TO ' . $row['arrival_port_name'] . '<br>';
                $distance = round(calculateDistance($row['lat1'], $row['lon1'], $row['lat2'], $row['lon2']));
                $set[]    = " distance_traveled = {$distance} ";
            }
            if (is_null($row['trip_duration'])) {
                if ('N' == $row['is_time_known']) {
                    $set[] = " trip_duration = 0 ";
                } else {
                    // DEPARTURE DATETIME
                    $departure = new \DateTime($row['departure_date_time'], new \DateTimeZone($row['departure_timezone']));
                    $arrival   = new \DateTime($row['arrival_date_time'], new \DateTimeZone($row['arrival_timezone']));
//                    echo "-- DEP {$row['departure_date_time']} - {$row['departure_timezone']}<br>";
//                    echo "-- ARR {$row['arrival_date_time']} - {$row['arrival_timezone']}<br>";
                    // calculate minutes of the difference between $different and $arrival
                    $diff = $arrival->diff($departure);
                    $min  = $diff->days * 24 * 60;
                    $min += $diff->h * 60;
                    $min += $diff->i;
//                    echo "-- MIN {$min}<br>";
                    $set[] = " trip_duration = {$min} ";
                }
            }
            if (!empty($set)) {
                $id = $row['id'];
                $update = implode(',', $set);
                echo "UPDATE journey_transport SET {$update} WHERE id = {$id} LIMIT 1;<br><br>";
            }
            unset($set);
            unset($id);
            unset($min);
            unset($distance);
        }
        echo '</pre>';
    }

    private function fix2(): void
    {
        $journey_transport = new JourneyTransportModel();
        $transport_raw     = $journey_transport->select('journey_transport.id, 
            port_departure.country_code AS departure_country,
            port_arrival.country_code AS arrival_country')
            ->join('journey_port AS port_departure', 'journey_transport.departure_port_id = port_departure.id', 'left outer')
            ->join('journey_port AS port_arrival',   'journey_transport.arrival_port_id = port_arrival.id', 'left outer')
            ->orderBy('id', 'asc')->findAll();
        foreach ($transport_raw as $row) {
            $id = $row['id'];
            $departure_country = $row['departure_country'];
            $arrival_country   = $row['arrival_country'];
            $is_domestic = 'I';
            if ($departure_country == $arrival_country) {
                $is_domestic = 'D';
            }
            echo "UPDATE journey_transport SET is_domestic = '{$is_domestic}' WHERE id = {$id} LIMIT 1;<br>";
        }
    }
}