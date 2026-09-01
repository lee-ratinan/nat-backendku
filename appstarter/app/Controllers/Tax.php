<?php

namespace App\Controllers;

use App\Models\TaxpayerInfoModel;
use App\Models\TaxRecordModel;
use App\Models\TaxYearModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Tax extends BaseController
{
    const PERMISSION_REQUIRED = 'finance';
    private array $countries = [
        'AU',
        'SG',
        'TH',
        'US',
    ];
    private array $currencies = [
        'AU' => 'AUD',
        'SG' => 'SGD',
        'TH' => 'THB',
        'US' => 'USD',
    ];
    private array $tax_brackets = [
        'AU' => [
            ['limit' => 18200, 'rate' => 0],
            ['limit' => 45000, 'rate' => 16],
            ['limit' => 135000, 'rate' => 30],
            ['limit' => 190000, 'rate' => 37],
            ['limit' => PHP_INT_MAX, 'rate' => 45],
        ],
        'SG' => [
            ['limit' => 20000, 'rate' => 0],
            ['limit' => 30000, 'rate' => 2],
            ['limit' => 40000, 'rate' => 3.5],
            ['limit' => 80000, 'rate' => 7],
            ['limit' => 120000, 'rate' => 11.5],
            ['limit' => 160000, 'rate' => 15],
            ['limit' => 200000, 'rate' => 18],
            ['limit' => 240000, 'rate' => 19],
            ['limit' => 280000, 'rate' => 19.5],
            ['limit' => 320000, 'rate' => 20],
            ['limit' => 500000, 'rate' => 22],
            ['limit' => 1000000, 'rate' => 23],
            ['limit' => PHP_INT_MAX, 'rate' => 24],
        ],
        'TH' => [
            ['limit' => 150000, 'rate' => 0],
            ['limit' => 300000, 'rate' => 5],
            ['limit' => 500000, 'rate' => 10],
            ['limit' => 750000, 'rate' => 15],
            ['limit' => 1000000, 'rate' => 20],
            ['limit' => 2000000, 'rate' => 25],
            ['limit' => 5000000, 'rate' => 30],
            ['limit' => PHP_INT_MAX, 'rate' => 35],
        ],
    ];
    private array $deductions = [
        'AU' => 0,
        'SG' => 1000,
        'TH' => 60000,
    ];
    private array $market_rates = [
        'AU' => [8000, 13000],
        'SG' => [7000, 14000],
        'TH' => [30000, 60000]
    ];

    /**
     * Calculate tax for various countries
     * @param float $annual_income
     * @param string $country_code
     * @return array
     */
    private function taxCalculation(float $annual_income, string $country_code): array
    {
        if (!isset($this->tax_brackets[$country_code]) || !isset($this->deductions[$country_code])) {
            return [];
        }
        $tax_brackets   = $this->tax_brackets[$country_code];
        $deduction      = $this->deductions[$country_code];
        if ('SG' == $country_code) {
            $cpf_annual = min(102000, $annual_income);
            $deduction += $cpf_annual * 0.2;
        } else if ('TH' == $country_code) {
            $deduction += 9000; // social security (750 monthly)
        }
        $taxable_income = $annual_income - $deduction;
        $taxable_income = (0 > $taxable_income) ? 0 : $taxable_income;
        $total_tax      = 0.00;
        $prev_limit     = 0;
        $line_details   = [];
        foreach ($tax_brackets as $tax_bracket) {
            $limit          = $tax_bracket['limit'];
            $rate           = $tax_bracket['rate'];
            // FOR AMOUNT <= 10,000,000 |     10.5%  | 1,000,000.00
            if ($taxable_income > $limit) {
                $this_amount  = ($limit - $prev_limit) * ($rate / 100);
                $total_tax   += $this_amount;
                $line_details[] = ['prev_limit' => $prev_limit, 'limit' => $limit, 'rate' => $rate, 'amount' => ($limit - $prev_limit), 'subtotal' => $this_amount];
            } else {
                $this_amount  = ($taxable_income - $prev_limit) * ($rate / 100);
                $total_tax   += $this_amount;
                $line_details[] = ['prev_limit' => $prev_limit, 'limit' => $limit, 'rate' => $rate, 'amount' => ($taxable_income - $prev_limit), 'subtotal' => $this_amount];
                break;
            }
            $prev_limit = $limit;
        }
        return [
            'deduction'      => $deduction,
            'taxable_income' => $taxable_income,
            'lines'          => $line_details,
            'total'          => $total_tax,
        ];
    }

    /**
     * Display the tax page
     * @return string
     */
    public function index(): string
    {
        $session   = session();
        $countries = [];
        foreach ($this->countries as $country_code) {
            $countries[$country_code] = lang('ListCountries.countries.' . $country_code . '.common_name');
        }
        $data    = [
            'page_title'   => 'Tax',
            'slug_group'   => 'tax',
            'slug'         => '/office/tax',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'countries'    => $countries
        ];
        return view('tax', $data);
    }

    /**
     * This API returns the list of tax records
     * @return ResponseInterface
     */
    public function masterList(): ResponseInterface
    {
        $model              = new TaxYearModel();
        $columns            = [
            '',
            'tax_year.id',
            'tax_year.tax_year',
            'tax_year.country_code',
            'tax_year.total_income',
            'tax_year.taxable_income',
            'tax_year.final_tax_amount',
            'taxpayer_info.taxpayer_id_value',
            'tax_year.google_drive_link',
        ];
        $order              = $this->request->getPost('order');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $country_code       = $this->request->getPost('country_code');
        $year               = $this->request->getPost('year');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $country_code, $year);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * Edit tax year
     * @param string $tax_id
     * @return string
     */
    public function masterEdit(string $tax_id = 'new'): string
    {
        $session          = session();
        $tax_year_model   = new TaxYearModel();
        $tax_record_model = new TaxRecordModel();
        $taxpayer_model   = new TaxpayerInfoModel();
        $mode             = 'new';
        $tax_id_for_link  = $tax_id;
        $page_title       = 'New Tax Year';
        if ('new' == $tax_id) {
            $tax_year    = [];
            $tax_records = [];
            $taxpayer    = [];
        } else {
            $tax_id      = intval($tax_id) / $tax_year_model::ID_NONCE;
            $tax_year    = $tax_year_model->find($tax_id);
            $tr_raw      = $tax_record_model->where('tax_year_id', $tax_id)->findAll();
            $tax_records = [];
            foreach ($tr_raw as $row) {
                $tax_records[$row['desc_type']][] = [
                    'id'              => $row['id'],
                    'new_id'          => $row['id'] * $tax_record_model::ID_NONCE,
                    'tax_description' => $row['tax_description'],
                    'money_amount'    => $row['money_amount'],
                    'item_notes'      => $row['item_notes'],
                ];
            }
            $taxpayer    = $taxpayer_model->find($tax_year['taxpayer_id']);
            $mode        = 'edit';
            $page_title  = 'Edit Tax Year [' . lang('ListCountries.countries.' . $tax_year['country_code'] . '.common_name') . ' / ' . $tax_year['tax_year'] . ']';
        }
        $data    = [
            'page_title'      => $page_title,
            'slug'            => 'tax',
            'user_session'    => $session->user,
            'roles'           => $session->roles,
            'current_role'    => $session->current_role,
            'mode'            => $mode,
            'tax_id_for_link' => $tax_id_for_link,
            'tax_year'        => $tax_year,
            'tax_records'     => $tax_records,
            'taxpayer'        => $taxpayer,
            'ty_config'       => $tax_year_model->getConfigurations(),
        ];
        return view('tax_edit', $data);
    }

    /**
     * Save the tax year
     * @return ResponseInterface
     */
    public function masterSave(): ResponseInterface
    {
        $session = session();
        $model   = new TaxYearModel();
        $mode    = $this->request->getPost('mode');
        $data    = [];
        $data['tax_year']          = $this->request->getPost('tax_year');
        $data['country_code']      = $this->request->getPost('country_code');
        $data['google_drive_link'] = $this->request->getPost('google_drive_link');
        $data['currency_code']     = $this->request->getPost('currency_code');
        $data['total_income']      = $this->request->getPost('total_income');
        $data['taxable_income']    = $this->request->getPost('taxable_income');
        $data['final_tax_amount']  = $this->request->getPost('final_tax_amount');
        $data['taxpayer_id']       = $this->request->getPost('taxpayer_id');
        try {
            if ('new' == $mode) {
                $data['created_by'] = $session->user_id;
                $inserted_id        = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Tax year has been added',
                        'url'    => base_url($session->locale . '/office/tax/edit/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                $id = $this->request->getPost('id');
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Tax year has been updated',
                        'url'    => base_url($session->locale . '/office/tax/edit/' . ($id * $model::ID_NONCE))
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
     * Edit tax record
     * @param int $tax_year_id
     * @param string|int $tax_record_id
     * @return string
     */
    public function recordEdit(string|int $tax_record_id, int $tax_year_id): string
    {
        $session           = session();
        $tax_year_model    = new TaxYearModel();
        $tax_record_model  = new TaxRecordModel();
        $parent_link       = base_url($session->locale . '/office/tax/edit/' . $tax_year_id);
        $tax_year_id       = $tax_year_id/$tax_year_model::ID_NONCE;
        $tax_year          = $tax_year_model->find($tax_year_id);
        $for               = '[' . lang('ListCountries.countries.' . $tax_year['country_code'] . '.common_name') . ' / ' . $tax_year['tax_year'] . ']';
        $mode              = 'new';
        $page_title        = 'New Tax Record ' . $for;
        $tax_record_config = $tax_record_model->getConfigurations();
        if ('new' == $tax_record_id) {
            $tax_record = [];
        } else {
            $tax_record_id = $tax_record_id/$tax_record_model::ID_NONCE;
            $mode          = 'edit';
            $page_title    = 'Edit Tax Record ' . $for;
            $tax_record    = $tax_record_model->find($tax_record_id);
        }
        $data = [
            'page_title'   => $page_title,
            'slug'         => 'tax',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'mode'         => $mode,
            'parent_link'  => $parent_link,
            'tax_year_id'  => $tax_year_id,
            'for_year'     => $for,
            'tax_record'   => $tax_record,
            'config'       => $tax_record_config,
        ];
        return view('tax_record_edit', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function recordSave(): ResponseInterface
    {
        $session  = session();
        $model    = new TaxRecordModel();
        $ty_model = new TaxYearModel();
        $mode     = $this->request->getPost('mode');
        $id       = $this->request->getPost('id');
        $data     = [];
        $fields   = [
            'tax_year_id',
            'tax_description',
            'desc_type',
            'money_amount',
            'item_notes',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        $tax_year_id = $data['tax_year_id']*$ty_model::ID_NONCE;
        try {
            if ('new' == $mode) {
                $data['created_by'] = $session->user_id;
                $inserted_id        = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Tax record has been added',
                        'url'    => base_url($session->locale . '/office/tax/record/edit/' . $tax_year_id . '/' . ($inserted_id * $model::ID_NONCE))
                    ]);
                }
            } else {
                if ($model->update($id, $data)) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Tax record has been updated',
                        'url'    => base_url($session->locale . '/office/tax/record/edit/' . $tax_year_id . '/' . ($id * $model::ID_NONCE))
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

    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Tax Calculator and Other Tools
    //////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Calculate tax for various countries
     * @return string
     */
    public function calculator(): string
    {
        $session      = session();
        $data         = [
            'page_title'   => 'Tax Calculator',
            'slug_group'   => 'tax',
            'slug'         => '/office/tax/calculator',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('tax_calculator', $data);
    }

    /**
     * Calculate tax for various countries
     * @return string
     */
    public function calculatorAjax(): string
    {
        $tax_country    = $this->request->getPost('tax_country');
        $monthly_income = $this->request->getPost('monthly_income');
        $annual_income  = $this->request->getPost('annual_income');
        $tax_details    = $this->taxCalculation($annual_income, $tax_country);
        $data           = [
            'monthly_income' => $monthly_income,
            'annual_income'  => $annual_income,
            'tax_country'    => $tax_country,
            'tax_details'    => $tax_details,
            'currency_code'  => $this->currencies[$tax_country],
            'after_tax'      => $annual_income-$tax_details['total']
        ];
        return view('tax_calculator_ajax', $data);
    }

    /**
     * This page shows the projection of the income tax for various countries
     * @return string
     */
    public function projection(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Tax Projection',
            'slug_group'   => 'tax',
            'slug'         => '/office/tax/projection',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('tax_projection', $data);
    }

    /**
     * Calculate tax projection for various countries
     * @return string
     */
    public function projectionAjax(): string
    {
        $tax_country = $this->request->getPost('tax_country');
        $min_income  = intval($this->request->getPost('min_income'));
        $max_income  = intval($this->request->getPost('max_income'));
        $step        = intval($this->request->getPost('step'));
        $tax_details = [];
        $graph       = [];
        for ($monthly_income = $min_income; $monthly_income <= $max_income; $monthly_income += $step) {
            $annual_income   = $monthly_income * 12;
            $calculation     = $this->taxCalculation($annual_income, $tax_country);
            $graph[]         = [
                'x' => $monthly_income,
                'y' => $calculation['total'],
                'l' => "Monthly income: " . number_format($monthly_income, 2) . "\nAnnual income: " . number_format($annual_income, 2) . "\nTax: " . number_format($calculation['total'], 2)
            ];
            $tax_details []  = [
                'monthly_income' => $monthly_income,
                'annual_income'  => $annual_income,
                'deduction'      => $calculation['deduction'],
                'taxable_income' => $calculation['taxable_income'],
                'total_tax'      => $calculation['total'],
                'lines'          => $calculation['lines'],
            ];
        }
        $data = [
            'tax_details' => $tax_details,
            'graph'       => $graph,
            'green_range' => $this->market_rates[$tax_country]
        ];
        return view('tax_projection_ajax', $data);
    }

    /**
     * This page shows the comparison of the income tax for various countries
     * @return string
     */
    public function comparison(): string
    {
        $session     = session();
        $usd_from    = $this->request->getGet('usd_from') ?? 1000;
        $usd_to      = $this->request->getGet('usd_to') ?? 15000;
        $rate['thb'] = $this->request->getGet('thbusd') ?? 34.5;
        $rate['sgd'] = $this->request->getGet('sgdusd') ?? 1.37;
        $rate['aud'] = $this->request->getGet('audusd') ?? 1.61;
        $data        = [];
        $chart       = [];
        $targets     = ['thb', 'sgd', 'aud'];
        $countries   = [
            'thb' => 'TH',
            'sgd' => 'SG',
            'aud' => 'AU',
        ];
        for ($usd = $usd_from; $usd <= $usd_to; $usd += 1000) {
            foreach ($targets as $currency) {
                $monthly_income  = $usd * $rate[$currency];
                $annual_income   = $monthly_income * 12;
                $tax_details     = $this->taxCalculation($annual_income, $countries[$currency]);
                $last_line       = end($tax_details['lines']);
                $temp[$currency] = [
                    'monthly_income'  => $monthly_income,
                    'annual_income'   => $annual_income,
                    'total_tax'       => $tax_details['total'],
                    'total_tax_usd'   => $tax_details['total'] / $rate[$currency],
                    'max_rate'        => $last_line['rate'] . '%',
                    'in_market_rate'  => $this->market_rates[$countries[$currency]][0] <= $monthly_income && $monthly_income <= $this->market_rates[$countries[$currency]][1],
                ];
            }
            $data[] = [
                'usd'        => $usd,
                'usd_annual' => $usd * 12,
                'thb'        => $temp['thb'],
                'sgd'        => $temp['sgd'],
                'aud'        => $temp['aud']
            ];
            $chart[] = [
                'x' => $usd,
                'y1' => $temp['thb']['total_tax_usd'],
                'y2' => $temp['sgd']['total_tax_usd'],
                'y3' => $temp['aud']['total_tax_usd'],
                'l' => "Income: " . currency_format('USD', $usd) . "\nTH Tax: " . number_format($temp['thb']['total_tax'], 2) . "\nSG Tax: " . number_format($temp['sgd']['total_tax'], 2) . "\nAU Tax: " . number_format($temp['aud']['total_tax'], 2)
            ];
            unset($temp);
        }
        $data    = [
            'page_title'   => 'Tax Comparison',
            'slug_group'   => 'tax',
            'slug'         => '/office/tax/comparison',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'data'         => $data,
            'chart'        => $chart,
            'usd_from'     => $usd_from,
            'usd_to'       => $usd_to,
            'rate'         => $rate
        ];
        return view('tax_comparison', $data);
    }

    /**
     * @return string
     */
    public function statistics(): string
    {
        $session   = session();
        $model     = new TaxYearModel();
        $raw       = $model->findAll();
        $countries = lang('ListCountries.countries');
        $records   = [];
        $totals    = [];
        $cc        = [];
        $chart     = [];
        foreach ($raw as $row) {
            $cc[$row['country_code']] = 1;
            $records[$row['country_code']][$row['tax_year']] = [
                'income'  => $row['total_income'],
                'taxable' => $row['taxable_income'],
                'tax'     => $row['final_tax_amount']
            ];
        }
        foreach ($cc as $country => $dummy) {
            $totals[$country]['income']  = array_sum(array_column($records[$country], 'income'));
            $totals[$country]['taxable'] = array_sum(array_column($records[$country], 'taxable'));
            $totals[$country]['tax']     = array_sum(array_column($records[$country], 'tax'));
            for ($y = 2010; $y <= date('Y'); $y++) {
                $chart[$country][$y] = $records[$country][$y]['tax'] ?? 0;
            }
        }
        $data    = [
            'page_title'   => 'Tax Statistics',
            'slug_group'   => 'tax',
            'slug'         => '/office/tax/statistics',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'totals'       => $totals,
            'countries'    => $countries,
            'chart'        => $chart,
        ];
        return view('tax_statistics', $data);
    }
}