<?php

namespace App\Controllers;

use App\Models\CompanyCPFInvestmentSnapshotModel;
use App\Models\CompanyCPFModel;
use App\Models\CompanyCPFStatementModel;
use App\Models\CompanyFreelanceClientModel;
use App\Models\CompanyFreelanceIncomeModel;
use App\Models\CompanyFreelanceProjectModel;
use App\Models\CompanyMasterModel;
use App\Models\CompanyPartTimePeriodModel;
use App\Models\CompanyPartTimeScheduleModel;
use App\Models\CompanySalaryModel;
use App\Models\LogActivityModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;
use Exception;
use ReflectionException;

class Employment extends BaseController
{

    const PERMISSION_REQUIRED = 'finance';
    private array $currencies = [
        'AUD',
        'SGD',
        'THB',
        'USD',
    ];
    private array $countries = [
        'AU',
        'GB',
        'ID',
        'MY',
        'SG',
        'TH',
        'TW',
        'US'
    ];

    /************************************************************************
     * COMPANY
     ************************************************************************/

    /**
     * @return string
     */
    public function index(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Employment',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'countries'    => $this->countries,
        ];
        return view('employment_company', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function companyList(): ResponseInterface
    {
        $model              = new CompanyMasterModel();
        $columns            = [
            '',
            '',
            'company_legal_name',
            'company_country_code',
            'company_hq_country_code',
            'employment_start_date',
            'employment_end_date',
            'position_titles'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $country_code       = $this->request->getPost('country_code');
        $year               = $this->request->getPost('year');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $country_code, $year);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * @param string $company_id
     * @return string
     */
    public function companyEdit(string $company_id = 'new'): string
    {
        $session       = session();
        $company_model = new CompanyMasterModel();
        $page_title    = 'New Company';
        $mode          = 'new';
        if ('new' != $company_id && is_numeric($company_id)) {
            $company_id = $company_id/$company_model::ID_NONCE;
            $company    = $company_model->find($company_id);
            $page_title = 'Edit [' . $company['company_trade_name'] . ']';
            $mode       = 'edit';
        } else {
            $company    = [];
        }
        $data    = [
            'page_title'   => $page_title,
            'slug_group'   => 'employment',
            'slug'         => '/office/employment',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'mode'         => $mode,
            'company'      => $company,
            'config'       => $company_model->getConfigurations([], $this->countries, $this->currencies)
        ];
        return view('employment_company_edit', $data);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function companySave(): ResponseInterface
    {
        $mode          = $this->request->getPost('mode');
        $company_model = new CompanyMasterModel();
        $log_model     = new LogActivityModel();
        $session       = session();
        $id            = $this->request->getPost('id');
        $data          = [];
        $fields        = [
            'company_legal_name',
            'company_trade_name',
            'company_slug',
            'company_other_names',
            'company_address',
            'company_country_code',
            'company_hq_country_code',
            'company_currency_code',
            'company_website',
            'company_details',
            'company_registration',
            'company_color',
            'employment_start_date',
            'employment_end_date',
            'position_titles'
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        if ('edit' == $mode) {
            if ($company_model->update($id, $data)) {
                $log_model->insertTableUpdate('company_master', $id, $data, $session->user_id);
                $new_id = $id * $company_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the company.',
                    'redirect' => base_url($session->locale . '/office/employment/company/edit/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $company_model->insert($data)) {
                $log_model->insertTableUpdate('company_master', $id, $data, $session->user_id);
                $new_id = $id * $company_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created new company.',
                    'redirect' => base_url($session->locale . '/office/employment/company/edit/' . $new_id)
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
     * @throws Exception
     */
    public function companyStats(): string
    {
        $session           = session();
        $company_model     = new CompanyMasterModel();
        $companies         = $company_model->orderBy('company_trade_name')->findAll();
        $duration          = [];
        $country_days      = [];
        $country_companies = [];
        $main_chart        = [];
        $charts            = [];
        $home_count        = [];
        $home_chart        = [];
        foreach ($companies as $company) {
            if ('0000-00-00' == $company['employment_end_date']) {
                $company['employment_end_date'] = null;
            }
            $start_date = new DateTime($company['employment_start_date']);
            $end_date   = (empty($company['employment_end_date']) ? new DateTime('now') : new DateTime($company['employment_end_date']));
            $diff       = $start_date->diff($end_date);
            $days       = $diff->days;
            $length     = (0 < $diff->y ? $diff->y . 'y ' : '') . (0 < $diff->m ? $diff->m . 'm ' : '') . (0 < $diff->d ? $diff->d . 'd' : '');
            $duration[] = [
                'name'      => $company['company_trade_name'],
                'country'   => $company['company_country_code'],
                'days'      => $days,
                'length'    => $length,
                'dates'     => [$company['employment_start_date'], $company['employment_end_date']],
            ];
            $main_chart[] = [
                'company' => $company['company_trade_name'],
                'days'    => $days,
                'label'   => $length,
            ];
            $home_count[$company['company_hq_country_code']]     = (isset($home_count[$company['company_hq_country_code']]) ? $home_count[$company['company_hq_country_code']] + 1 : 1);
            $country_days[$company['company_country_code']]      = (isset($country_days[$company['company_country_code']]) ? $country_days[$company['company_country_code']] + $days : $days);
            $country_companies[$company['company_country_code']] = (isset($country_companies[$company['company_country_code']]) ? $country_companies[$company['company_country_code']] + 1 : 1);
        }
        $country_length = [];
        foreach ($country_days as $country_code => $days) {
            $y        = floor($days/365);
            $m        = round(($days % 365)/30);
            $country_length[$country_code] = ($y > 0 ? $y . 'y ' : '') . ($m > 0 ? $m . 'm ' : '');
            $charts[] = [
                'country'   => lang('ListCountries.countries.' . $country_code . '.common_name'),
                'days'      => $days,
                'companies' => $country_companies[$country_code]
            ];
        }
        foreach ($home_count as $country_code => $count) {
            $home_chart[] = [
                'country'   => lang('ListCountries.countries.' . $country_code . '.common_name'),
                'count'     => $count
            ];
        }
        $data = [
            'page_title'        => 'Company Statistics',
            'slug_group'        => 'employment',
            'slug'              => '/office/employment/company/stats',
            'user_session'      => $session->user,
            'roles'             => $session->roles,
            'current_role'      => $session->current_role,
            'duration'          => $duration,
            'country_days'      => $country_days,
            'country_companies' => $country_companies,
            'country_length'    => $country_length,
            'charts'            => $charts,
            'main_chart'        => $main_chart,
            'home_chart'        => $home_chart,
        ];
        return view('employment_company_stats', $data);
    }
    /************************************************************************
     * SALARY
     ************************************************************************/

    /**
     * @return string
     */
    public function salary(): string
    {
        $session      = session();
        $company      = new CompanyMasterModel();
        $company_raw  = $company->orderBy('company_trade_name', 'asc')->findAll();
        $company_list = [];
        foreach ($company_raw as $row) {
            $company_list[$row['company_country_code']][] = [
                'id'   => $row['id'],
                'name' => $row['company_trade_name']
            ];
        }
        $data         = [
            'page_title'   => 'Salary',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/salary',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'currencies'   => $this->currencies,
            'companies'    => $company_list,
        ];
        return view('employment_salary', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function salaryList(): ResponseInterface
    {
        $model              = new CompanySalaryModel();
        $columns            = [
            '',
            '',
            'pay_date',
            'company_legal_name',
            'tax_year',
            'tax_country_code',
            'payment_method',
            'payment_currency',
            'pay_type',
            'base_amount',
            'allowance_amount',
            'training_amount',
            'overtime_amount',
            'adjustment_amount',
            'bonus_amount',
            'subtotal_amount',
            'social_security_amount',
            'us_tax_fed_amount',
            'us_tax_state_amount',
            'us_tax_city_amount',
            'us_tax_med_ee_amount',
            'us_tax_oasdi_ee_amount',
            'th_tax_amount',
            'sg_tax_amount',
            'au_tax_amount',
            'claim_amount',
            'provident_fund_amount',
            'total_amount',
            'payment_details'
        ];
        $order              = $this->request->getPost('order');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $currency_code      = $this->request->getPost('currency_code');
        $company_id         = intval($this->request->getPost('company_id'));
        $year               = $this->request->getPost('year');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $currency_code, $company_id, $year);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data'],
            'footer'          => $result['footer']
        ]);
    }

    /**
     * @param string $salary_id
     * @return string
     */
    public function salaryEdit(string $salary_id = 'new'): string
    {
        $session       = session();
        $salary_model  = new CompanySalaryModel();
        $page_title    = 'New Salary';
        $mode          = 'new';
        if ('new' != $salary_id && is_numeric($salary_id)) {
            $salary_id  = $salary_id/$salary_model::ID_NONCE;
            $salary     = $salary_model->find($salary_id);
            $page_title = 'Edit [' . date(MONTH_FORMAT_UI, strtotime($salary['pay_date'])) . ' Salary]';
            $mode       = 'edit';
        } else {
            $salary    = [];
        }
        $data      = [
            'page_title'   => $page_title,
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/salary',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'mode'         => $mode,
            'salary'       => $salary,
            'config'       => $salary_model->getConfigurations([], $this->countries, $this->currencies)
        ];
        return view('employment_salary_edit', $data);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function salarySave(): ResponseInterface
    {
        $mode         = $this->request->getPost('mode');
        $salary_model = new CompanySalaryModel();
        $log_model    = new LogActivityModel();
        $session      = session();
        $id           = $this->request->getPost('id');
        $data         = [];
        $fields       = [
            'company_id',
            'pay_date',
            'tax_year',
            'tax_country_code',
            'payment_method',
            'payment_currency',
            'pay_type',
            'base_amount',
            'allowance_amount',
            'training_amount',
            'overtime_amount',
            'adjustment_amount',
            'bonus_amount',
            'subtotal_amount',
            'social_security_amount',
            'us_tax_fed_amount',
            'us_tax_state_amount',
            'us_tax_city_amount',
            'us_tax_med_ee_amount',
            'us_tax_oasdi_ee_amount',
            'th_tax_amount',
            'sg_tax_amount',
            'au_tax_amount',
            'claim_amount',
            'provident_fund_amount',
            'total_amount',
            'payment_details',
            'google_drive_link'
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        if ('edit' == $mode) {
            if ($salary_model->update($id, $data)) {
                $log_model->insertTableUpdate('company_salary', $id, $data, $session->user_id);
                $new_id = $id * $salary_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the salary.',
                    'redirect' => base_url($session->locale . '/office/employment/salary/edit/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $salary_model->insert($data)) {
                $log_model->insertTableUpdate('company_salary', $id, $data, $session->user_id);
                $new_id = $id * $salary_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created new salary.',
                    'redirect' => base_url($session->locale . '/office/employment/salary/edit/' . $new_id)
                ]);
            }
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * @param string $currency_code
     * @return string
     */
    public function salaryStatisticsCurrency(string $currency_code = ''): string
    {
        $session       = session();
        $locale        = $this->request->getLocale();
        $company_model = new CompanyMasterModel();
        $salary_model  = new CompanySalaryModel();
        if (empty($currency_code)) {
            $currency_code = 'SGD';
        }
        $companies     = $company_model->where('company_currency_code', $currency_code)->findAll();
        if (empty($companies)) {
            throw new PageNotFoundException();
        }
        $company_list  = [];
        $company_ids   = [];
        foreach ($companies as $company) {
            $company_list[$company['id']] = $company['company_trade_name'];
            $company_ids[]                = $company['id'];
        }
        $currency_list  = [];
        $dedupe_ccy     = $company_model->select('company_currency_code')->distinct()->findAll();
        foreach ($dedupe_ccy as $ccy) {
            $currency_list[] = $ccy['company_currency_code'];
        }
        $salary_records = $salary_model->whereIn('company_id', $company_ids)->whereIn('pay_type', ['salary', 'claim', 'other'])->findAll();
        $salary_by_year = [];
        $base_amounts   = [];
        foreach ($salary_records as $salary_record) {
            $year                              = substr($salary_record['tax_year'], 0, 4);
            $base_amounts[$year][]             = $salary_record['base_amount'] ?? 0.0;
            $salary_by_year[$year]['subtotal'] = (isset($salary_by_year[$year]['subtotal']) ? $salary_by_year[$year]['subtotal'] += $salary_record['subtotal_amount'] : $salary_record['subtotal_amount']);
            $salary_by_year[$year]['total']    = (isset($salary_by_year[$year]['total'])    ? $salary_by_year[$year]['total']    += $salary_record['total_amount']    : $salary_record['total_amount']);
        }
        $chart_data     = [];
        $max_bases      = [];
        $max_base_d     = [];
        $max_base_h     = [];
        $chart_data_2   = [];
        ksort($salary_by_year);
        $start_year     = ('SGD' == $currency_code ? 2015 : 2010);
        for ($y = $start_year; $y <= date('Y'); $y++) {
            if (isset($base_amounts[$y])) {
                $max_base       = max($base_amounts[$y]);
                $chart_data[]   = [
                    'year'     => "$y",
                    'subtotal' => round($salary_by_year[$y]['subtotal']),
                    'total'    => round($salary_by_year[$y]['total'])
                ];
                $chart_data_2[] = [
                    'year'     => "$y",
                    'base'     => round($max_base)
                ];
                $max_bases[$y]  = $max_base;
                $max_base_d[$y] = $max_base / 21;
                $max_base_h[$y] = $max_base / 168;
            } else {
                $chart_data[]   = [
                    'year'     => "$y",
                    'subtotal' => 0,
                    'total'    => 0
                ];
                $chart_data_2[] = [
                    'year'     => "$y",
                    'base'     => 0
                ];
                $max_bases[$y]  = 0.0;
                $max_base_d[$y] = 0.0;
                $max_base_h[$y] = 0.0;
            }
        }
        $data           = [
            'lang'           => $locale,
            'page_title'     => 'Salary Statistics - by Currency',
            'slug_group'     => 'employment',
            'slug'           => '/office/employment/salary/stats/currency/',
            'user_session'   => $session->user,
            'roles'          => $session->roles,
            'current_role'   => $session->current_role,
            'currency_code'  => $currency_code,
            'company_list'   => $company_list,
            'currency_list'  => $currency_list,
            'max_bases'      => $max_bases,
            'max_base_d'     => $max_base_d,
            'max_base_h'     => $max_base_h,
            'salary_by_year' => $salary_by_year,
            'chart_data'     => $chart_data,
            'chart_data_2'   => $chart_data_2,
        ];
        return view('employment_salary_statistics_currency', $data);
    }

    /**
     * @param int $company_id
     * @return string
     */
    public function salaryStatisticsCompany(int $company_id = 0): string
    {
        $session        = session();
        $locale         = $this->request->getLocale();
        $company_model  = new CompanyMasterModel();
        $salary_model   = new CompanySalaryModel();
        if (0 == $company_id) {
            $company    = $company_model->where('company_country_code', 'SG')->orderBy('employment_start_date', 'DESC')->first();
            $company_id = $company['id'];
        } else {
            $company    = $company_model->find($company_id);
        }
        $salaries       = $salary_model->where('company_id', $company_id)->whereIn('pay_type', ['salary', 'claim', 'other'])->findAll();
        $base_amount    = 0;
        $base_amounts   = [];
        $by_year        = [];
        $for_c3         = [];
        foreach ($salaries as $salary) {
            if ($salary['base_amount'] != $base_amount && $salary['base_amount'] > 0) {
                $base_amounts[$salary['pay_date']] = $salary['base_amount'];
                $base_amount                       = $salary['base_amount'];
            }
            $year                       = substr($salary['pay_date'], 0, 4);
            $by_year[$year]['subtotal'] = (isset($by_year[$year]['subtotal']) ? $by_year[$year]['subtotal'] += $salary['subtotal_amount'] : $salary['subtotal_amount']);
            $by_year[$year]['total']    = (isset($by_year[$year]['total'])    ? $by_year[$year]['total']    += $salary['total_amount']    : $salary['total_amount']);
            $month_year                 = substr($salary['pay_date'], 0, 7);
            $for_c3[$month_year][]      = [
                'subtotal' => round($salary['subtotal_amount']),
                'total'    => round($salary['total_amount'])
            ];
        }
        $chart_data     = [];
        foreach ($by_year as $year => $data) {
            $chart_data[] = [
                'year'     => "$year",
                'subtotal' => round($data['subtotal']),
                'total'    => round($data['total'])
            ];
        }
        $chart_data_2   = [];
        foreach ($base_amounts as $date => $amount) {
            $chart_data_2[] = [
                'month' => date(MONTH_FORMAT_UI, strtotime($date)),
                'base'  => round($amount)
            ];
        }
        $chart_data_3   = [];
        foreach ($for_c3 as $month_year => $data) {
            $total    = 0;
            $subtotal = 0;
            foreach ($data as $row) {
                $total    += $row['total'];
                $subtotal += $row['subtotal'];
            }
            $chart_data_3[] = [
                'month'    => strtotime($month_year . '-01') * 1000,
                'total'    => round($total),
                'subtotal' => round($subtotal)
            ];
        }
        $company_ids    = $salary_model->select('company_id')->distinct()->findAll();
        $company_ids    = array_column($company_ids, 'company_id');
        $data           = [
            'lang'           => $locale,
            'page_title'     => 'Salary Statistics - by Company',
            'slug_group'     => 'employment',
            'slug'           => '/office/employment/salary/stats/company/',
            'user_session'   => $session->user,
            'roles'          => $session->roles,
            'current_role'   => $session->current_role,
            'company_id'     => $company_id,
            'company'        => $company,
            'currency_code'  => $company['company_currency_code'],
            'company_list'   => $company_model->whereIn('id', $company_ids)->findAll(),
            'chart_data'     => $chart_data,
            'chart_data_2'   => $chart_data_2,
            'chart_data_3'   => $chart_data_3,
        ];
        return view('employment_salary_statistics_company', $data);
    }

    /************************************************************************
     * CPF
     ************************************************************************/

    /**
     * @return string
     */
    public function cpf(): string
    {
        $session = session();
        $model   = new CompanyMasterModel();
        $data    = [
            'page_title'   => 'CPF',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/cpf',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'companies'    => $model
                ->where('company_country_code', 'SG')
                ->groupStart()
                ->where('employment_end_date >=', '2020-01-02')
                ->orWhere('employment_end_date', null)
                ->groupEnd()
                ->orderBy('company_legal_name', 'asc')->findAll()
        ];
        return view('employment_cpf', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function cpfList(): ResponseInterface
    {
        $model              = new CompanyCPFModel();
        $columns            = [
            '',
            'user_id',
            'transaction_date',
            'transaction_code',
            'ordinary_amount',
            'ordinary_balance',
            'special_amount',
            'special_balance',
            'medisave_amount',
            'medisave_balance',
            'transaction_amount',
            'account_balance',
            'contribution_month',
            'company_id',
            'staff_contribution',
            'staff_ytd',
            'company_match',
            'company_ytd',
        ];
        $order              = $this->request->getPost('order');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $transaction_code   = $this->request->getPost('transaction_code');
        $company_id         = intval($this->request->getPost('company_id'));
        $year               = $this->request->getPost('year');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $transaction_code, $company_id, $year);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * @param string $cpf_id
     * @return string
     */
    public function cpfEdit(string $cpf_id = 'new'): string
    {
        $session       = session();
        $cpf_model     = new CompanyCPFModel();
        $page_title    = 'New CPF';
        $cpf           = [];
        $cpf_latest    = [];
        $cpf_last_con  = [];
        $mode          = 'new';
        if ('new' != $cpf_id && is_numeric($cpf_id)) {
            $cpf_id     = $cpf_id/$cpf_model::ID_NONCE;
            $cpf        = $cpf_model->find($cpf_id);
            $page_title = 'View CPF [' . $cpf['transaction_code'] . ' - ' . date(MONTH_FORMAT_UI, strtotime($cpf['transaction_date'])) . ']';
            $mode       = 'edit';
        } else {
            $cpf_latest   = $cpf_model->orderBy('id', 'desc')->first();
            $cpf_last_con = $cpf_model->where('transaction_code', 'CON')->orderBy('id', 'desc')->first();
        }
        $data = [
            'page_title'   => $page_title,
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/cpf',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'cpf'          => $cpf,
            'config'       => $cpf_model->getConfigurations(),
            'mode'         => $mode,
            'cpf_latest'   => $cpf_latest,
            'cpf_last_con' => $cpf_last_con
        ];
        return view('employment_cpf_edit', $data);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function cpfSave(): ResponseInterface
    {
        $cpf_model = new CompanyCPFModel();
        $log_model = new LogActivityModel();
        $session   = session();
        $data      = [];
        $fields    = [
            'transaction_date',
            'transaction_code',
            'ordinary_amount',
            'ordinary_balance',
            'special_amount',
            'special_balance',
            'medisave_amount',
            'medisave_balance',
            'transaction_amount',
            'account_balance',
            'contribution_month',
            'company_id',
            'staff_contribution',
            'staff_ytd',
            'company_match',
            'company_ytd',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        $data['created_by'] = $session->user_id;
        if (0 > $data['company_id']) {
            $data['company_id'] = null;
        }
        // INSERT
        if ($id = $cpf_model->insert($data)) {
            $log_model->insertTableUpdate('company_cpf', $id, $data, $session->user_id);
            $new_id = $id * $cpf_model::ID_NONCE;
            return $this->response->setJSON([
                'status'   => 'success',
                'toast'    => 'Successfully created new CPF record.',
                'redirect' => base_url($session->locale . '/office/employment/cpf/edit/' . $new_id)
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * CPF Annual Statement
     * @return string
     */
    public function cpfStatement(): string
    {
        $session = session();
        $model   = new CompanyCPFStatementModel();
        $data    = [
            'page_title'   => 'CPF Statement',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/cpf',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'statements'   => $model->findAll(),
            'nonce'        => $model::ID_NONCE
        ];
        return view('employment_cpf_statement', $data);
    }

    /**
     * @param string $cpf_statement_id
     * @return string
     */
    public function cpfStatementEdit(string $cpf_statement_id = 'new'): string
    {
        $session    = session();
        $model      = new CompanyCPFStatementModel();
        $page_title = 'New CPF Statement';
        $mode       = 'new';
        $statement  = [];
        if ('new' != $cpf_statement_id && is_numeric($cpf_statement_id)) {
            $cpf_id     = $cpf_statement_id/$model::ID_NONCE;
            $statement  = $model->find($cpf_id);
            $page_title = 'View CPF Statement [' . $statement['statement_year'] . ']';
            $mode       = 'edit';
        }
        $data    = [
            'page_title'   => $page_title,
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/cpf',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'statement'    => $statement,
            'mode'         => $mode,
            'config'       => $model->getConfiguration()
        ];
        return view('employment_cpf_statement_edit', $data);
    }

    /**
     * Save CPF Statement
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function cpfStatementSave(): ResponseInterface
    {
        $cpf_model = new CompanyCPFStatementModel();
        $log_model = new LogActivityModel();
        $session   = session();
        $data      = [];
        $fields    = [
            'statement_year',
            'google_drive_url'
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        $data['created_by'] = $session->user_id;
        // INSERT
        if ($id = $cpf_model->insert($data)) {
            $log_model->insertTableUpdate('company_cpf_statement', $id, $data, $session->user_id);
            return $this->response->setJSON([
                'status'   => 'success',
                'toast'    => 'Successfully created new CPF record.',
                'redirect' => base_url($session->locale . '/office/employment/cpf/statement/')
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * @return string
     */
    public function cpfNow(): string
    {
        $session   = session();
        $cpf_model = new CompanyCPFModel();
        $inv_model = new CompanyCPFInvestmentSnapshotModel();
        $latest    = $cpf_model->orderBy('id', 'desc')->first();
        $inv       = $inv_model->orderBy('id', 'desc')->first();
        $fields    = ['ordinary_balance', 'special_balance', 'medisave_balance'];
        $chart_1   = [];
        foreach ($fields as $field) {
            $chart_1[] = ['account' => ucwords(str_replace('_balance', '', $field) . ' account'), 'value' => $latest[$field] ?? 0];
        }
        $fields    = ['staff_ytd', 'company_ytd'];
        $chart_2   = [];
        foreach ($fields as $field) {
            $chart_2[] = ['contributor' => ucfirst(str_replace('_ytd', '', $field)) . ' YTD', 'value' => $latest[$field] ?? 0];
        }
        $data      = [
            'page_title'   => 'CPF Current Balance',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/cpf/now',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'chart_1'      => $chart_1,
            'chart_2'      => $chart_2,
            'inv'          => $inv
        ];
        return view('employment_cpf_now', $data);
    }

    /**
     * @param string $filter_type
     * @param string $filter_value
     * @return string
     */
    public function cpfGrowth(string $filter_type = 'account', string $filter_value = 'all'): string
    {
        $session        = session();
        $model          = new CompanyCPFModel();
        $records        = [];
        $target_column  = '';
        $current_column = '';
        if ('account' == $filter_type){
            // filter by account: all, ordinary, special, medisave
            $filter_columns = [
                'all'      => 'transaction_amount',
                'ordinary' => 'ordinary_amount',
                'special'  => 'special_amount',
                'medisave' => 'medisave_amount',
            ];
            if (!isset($filter_columns[$filter_value])) {
                $filter_value = 'all';
            }
            $target_columns = [
                'all'      => 'account_balance',
                'ordinary' => 'ordinary_balance',
                'special'  => 'special_balance',
                'medisave' => 'medisave_balance',
            ];
            $current_column = $filter_columns[$filter_value];
            $records        = $model->where($current_column . ' !=', 0)->findAll();
            $target_column  = $target_columns[$filter_value];
        } else if ('contributor' == $filter_type) {
            // filter by contributor: all, company, staff | TC='CON'
            $records = $model->where('transaction_code', 'CON')->findAll();
            if ('all' == $filter_value) {
                $target_column = 'total_contribution';
            } else if ('company' == $filter_value) {
                $target_column = 'company_match_cumulative';
            } else {
                $target_column = 'staff_contribution_cumulative';
            }
        } else if ('tc' == $filter_type) {
            // filter by transaction code:
            $records       = $model->where('transaction_code', $filter_value)->findAll();
            $target_column = 'transaction_amount_cumulative';
        }
        $chart_data = [];
        $cumulative = 0;
        if (in_array($target_column, ['account_balance', 'ordinary_balance', 'special_balance', 'medisave_balance'])) {
            foreach ($records as $record) {
                $chart_data[] = [
                    'date'    => intval(strtotime($record['transaction_date']) . '000'),
                    'dt_str'  => $record['transaction_date'],
                    'current' => floatval($record[$current_column]),
                    'value'   => floatval($record[$target_column])
                ];
            }
        } else if ('total_contribution' == $target_column) {
            foreach ($records as $record) {
                $current      = floatval($record['staff_contribution']) + floatval($record['company_match']);
                $cumulative  += $current;
                $chart_data[] = [
                    'date'    => intval(strtotime($record['transaction_date']) . '000'),
                    'dt_str'  => $record['transaction_date'],
                    'current' => $current,
                    'value'   => $cumulative
                ];
            }
        } else {
            $target_column = str_replace('_cumulative', '', $target_column);
            foreach ($records as $record) {
                $current      = floatval($record[$target_column]);
                $cumulative  += $current;
                $chart_data[] = [
                    'date'    => intval(strtotime($record['transaction_date']) . '000'),
                    'dt_str'  => $record['transaction_date'],
                    'current' => $current,
                    'value'   => $cumulative
                ];
            }
        }
        // TC
        $tc_list    = $model->select('transaction_code')->distinct()->findAll();
        $tc_list    = array_column($tc_list, 'transaction_code');
        $data       = [
            'page_title'     => 'CPF Current Balance',
            'slug_group'     => 'employment',
            'slug'           => '/office/employment/cpf/growth',
            'user_session'   => $session->user,
            'roles'          => $session->roles,
            'current_role'   => $session->current_role,
            'current_filter' => $filter_type . '/' . $filter_value,
            'chart_data'     => $chart_data,
            'tc_list'        => array_values($tc_list)
        ];
        return view('employment_cpf_growth', $data);
    }

    /**
     * @param string $year (optional)
     * @return string
     */
    public function cpfStatistics(string $year = ''): string
    {
        $session         = session();
        $cpf_model       = new CompanyCPFModel();
        $statement_model = new CompanyCPFStatementModel();
        if (empty($year)) {
            $year = date('Y');
        }
        $records         = $cpf_model
            ->where('transaction_date >=', $year . '-01-01')
            ->where('transaction_date <=', $year . '-12-31')
            ->findAll();
        $summary         = [];
        $by_tc           = [];
        $by_ac           = [];
        $contribution    = [
            'employee'   => 0,
            'employer'   => 0
        ];
        $con_chart       = [];
        foreach ($records as $record) {
            // by TC and ACC
            $tc  = $record['transaction_code'];
            $acc = ['ordinary', 'special', 'medisave'];
            foreach ($acc as $account) {
                if (0 > $record[$account . '_amount']) {
                    $summary['tc'][$tc]['neg'][]      = abs($record[$account . '_amount']);
                    $summary['ac'][$account]['neg'][] = abs($record[$account . '_amount']);
                } else if (0 < $record[$account . '_amount']) {
                    $summary['tc'][$tc]['pos'][]      = $record[$account . '_amount'];
                    $summary['ac'][$account]['pos'][] = $record[$account . '_amount'];
                }
            }
            // contribution
            $contribution['employee']             += $record['staff_contribution'];
            $contribution['employer']             += $record['company_match'];
        }
        foreach ($summary as $type => $categories) {
            ksort($categories);
            foreach ($categories as $category => $data) {
                if ('tc' == $type) {
                    $by_tc[] = [
                        'transaction_code' => $category,
                        'neg'              => (isset($data['neg']) ? array_sum($data['neg']) : 0),
                        'pos'              => (isset($data['pos']) ? array_sum($data['pos']) : 0)
                    ];
                } else if ('ac' == $type) {
                    $by_ac[] = [
                        'account'          => ucfirst($category) . ' Account',
                        'neg'              => (isset($data['neg']) ? array_sum($data['neg']) : 0),
                        'pos'              => (isset($data['pos']) ? array_sum($data['pos']) : 0)
                    ];
                }
            }
        }
        foreach ($contribution as $type => $amount) {
            $con_chart[] = [
                'contributor' => ucfirst($type),
                'amount'      => $amount
            ];
        }
        $data            = [
            'page_title'   => 'CPF Statistics of ' . $year,
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/cpf/stats',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'year'         => $year,
            'contribution' => $con_chart,
            'statement'    => $statement_model->where('statement_year', $year)->first(),
            'by_tc'        => $by_tc,
            'by_ac'        => $by_ac,
        ];
        return view('employment_cpf_statistics', $data);
    }

    public function cpfContribution(): string
    {
        $session       = session();
        $cpf_model     = new CompanyCPFModel();
        $company_model = new CompanyMasterModel();
        $salary_model  = new CompanySalaryModel();
        $pt_model      = new CompanyPartTimePeriodModel();
        $company_raw   = $company_model
            ->where('company_country_code', 'SG')
            ->groupStart()
            ->where('employment_end_date >=', '2020-01-02')
            ->orWhere('employment_end_date', null)
            ->groupEnd()
            ->orderBy('company_legal_name', 'asc')->findAll();
        $companies     = [];
        foreach ($company_raw as $company) {
            $companies[$company['id']]               = $company['company_trade_name'];
        }
        // SUM DATA ARRAYS
        $sum_company_match_by_company      = [];
        $sum_staff_contribution_by_company = [];
        $sum_total_by_company              = [];
        $avg_company_match_by_company      = [];
        $avg_staff_contribution_by_company = [];
        $avg_total_by_company              = [];
        $count_records_by_company          = [];
        $sum_company_match_by_year         = [];
        $sum_staff_contribution_by_year    = [];
        $sum_total_by_year                 = [];
        $avg_company_match_by_year         = [];
        $avg_staff_contribution_by_year    = [];
        $avg_total_by_year                 = [];
        $count_records_by_year             = [];
        $company_ids                       = [];
        $con_years                         = [];
        $salary_records_cpf                = [];
        $salary_records_salary             = [];
        // PROCESS CPF
        $contributions = $cpf_model->where('transaction_code', 'CON')->orderBy('transaction_date', 'asc')->findAll();
        foreach ($contributions as $row) {
            $match        = 0.0;
            $contribution = 0.0;
            $total        = 0.0;
            if ($row['company_match'] && 0 < $row['company_match']) {
                $match = $row['company_match'];
            }
            if ($row['staff_contribution'] && 0 < $row['staff_contribution']) {
                $contribution = $row['staff_contribution'];
            }
            if ($row['transaction_amount'] && 0 < $row['transaction_amount']) {
                $total = $row['transaction_amount'];
            }
            // by company
            $company_id                                     = $row['company_id'];
            $company_ids[$company_id]                       = 1;
            $count_records_by_company[$company_id]          = isset($count_records_by_company[$company_id]) ? $count_records_by_company[$company_id] + 1 : 1;
            $sum_company_match_by_company[$company_id]      = isset($sum_company_match_by_company[$company_id]) ? $sum_company_match_by_company[$company_id] + $match : $match;
            $sum_staff_contribution_by_company[$company_id] = isset($sum_staff_contribution_by_company[$company_id]) ? $sum_staff_contribution_by_company[$company_id] + $contribution : $contribution;
            $sum_total_by_company[$company_id]              = isset($sum_total_by_company[$company_id]) ? $sum_total_by_company[$company_id] + $total : $total;
            // by year
            $con_year                                       = substr($row['contribution_month'], 0, 4);
            $con_years[$con_year]                           = 1;
            $count_records_by_year[$con_year]               = isset($count_records_by_year[$con_year]) ? $count_records_by_year[$con_year] + 1 : 1;
            $sum_company_match_by_year[$con_year]           = isset($sum_company_match_by_year[$con_year]) ? $sum_company_match_by_year[$con_year] + $match : $match;
            $sum_staff_contribution_by_year[$con_year]      = isset($sum_staff_contribution_by_year[$con_year]) ? $sum_staff_contribution_by_year[$con_year] + $contribution : $contribution;
            $sum_total_by_year[$con_year]                   = isset($sum_total_by_year[$con_year]) ? $sum_total_by_year[$con_year] + $total : $total;
            // salary records
            $salary_records_cpf[$row['contribution_month']][$company_id][] = [
                'match'        => $match,
                'contribution' => $contribution,
                'total'        => $total
            ];
        }
        foreach ($count_records_by_company as $company_id => $count) {
            $avg_company_match_by_company[$company_id]      = $sum_company_match_by_company[$company_id] / $count;
            $avg_staff_contribution_by_company[$company_id] = $sum_staff_contribution_by_company[$company_id] / $count;
            $avg_total_by_company[$company_id]              = $sum_total_by_company[$company_id] / $count;
        }
        foreach ($count_records_by_year as $con_year => $count) {
            $avg_company_match_by_year[$con_year]      = $sum_company_match_by_year[$con_year] / $count;
            $avg_staff_contribution_by_year[$con_year] = $sum_staff_contribution_by_year[$con_year] / $count;
            $avg_total_by_year[$con_year]              = $sum_total_by_year[$con_year] / $count;
        }
        // PROCESS SALARY
        $salary_raw                                    = $salary_model->where('tax_country_code', 'SG')->where('provident_fund_amount <', 0)->findAll();
        foreach ($salary_raw as $row) {
            $pay_month = substr($row['pay_date'], 0, 7);
            // salary records
            $salary_records_salary[$pay_month][$row['company_id']][] = [
                'salary'  => $row['subtotal_amount'],
                'cpf_amt' => $row['provident_fund_amount'],
            ];
        }
        // PROCESS PT (SG)
        $pt_raw  = $pt_model->select('company_pt_period.*, company_master.company_trade_name, company_master.company_country_code')
            ->join('company_master', 'company_master.id = company_pt_period.company_id')
            ->where('company_master.company_country_code', 'SG')
            ->findAll();
        $pt_recs = [];
        foreach ($pt_raw as $row) {
            $pt_month = substr($row['period_start'], 0, 7);
            $pt_recs[$row['company_id']][$pt_month]['salary'][]  = $row['subtotal_income'];
            $pt_recs[$row['company_id']][$pt_month]['cpf_amt'][] = -$row['income_deduction'];
        }
        foreach ($pt_recs as $company_id => $pt_rec_months) {
            foreach ($pt_rec_months as $pt_month => $pt_rec) {
                $salary_records_salary[$pt_month][$company_id][] = [
                    'salary'  => array_sum($pt_rec['salary']),
                    'cpf_amt' => array_sum($pt_rec['cpf_amt']),
                ];
            }
        }
        // DATA
        $data = [
            'page_title'                        => 'CPF Contribution',
            'slug_group'                        => 'employment',
            'slug'                              => '/office/employment/cpf/contribution',
            'user_session'                      => $session->user,
            'roles'                             => $session->roles,
            'current_role'                      => $session->current_role,
            'companies'                         => $companies,
            'contributions'                     => $contributions,
            'company_ids'                       => array_keys($company_ids),
            'con_years'                         => array_keys($con_years),
            'sum_company_match_by_company'      => $sum_company_match_by_company,
            'sum_staff_contribution_by_company' => $sum_staff_contribution_by_company,
            'sum_total_by_company'              => $sum_total_by_company,
            'avg_company_match_by_company'      => $avg_company_match_by_company,
            'avg_staff_contribution_by_company' => $avg_staff_contribution_by_company,
            'avg_total_by_company'              => $avg_total_by_company,
            'count_records_by_company'          => $count_records_by_company,
            'sum_company_match_by_year'         => $sum_company_match_by_year,
            'sum_staff_contribution_by_year'    => $sum_staff_contribution_by_year,
            'sum_total_by_year'                 => $sum_total_by_year,
            'avg_company_match_by_year'         => $avg_company_match_by_year,
            'avg_staff_contribution_by_year'    => $avg_staff_contribution_by_year,
            'avg_total_by_year'                 => $avg_total_by_year,
            'count_records_by_year'             => $count_records_by_year,
            'salary_records_cpf'                => $salary_records_cpf,
            'salary_records_salary'             => $salary_records_salary
        ];
        return view('employment_cpf_contribution', $data);
    }

    public function cpfInvestment(): string
    {
        $session   = session();
        $cpf_model = new CompanyCPFModel();
        $inv_model = new CompanyCPFInvestmentSnapshotModel();
        $records   = $cpf_model->where('transaction_code', 'INV')->findAll();
        $inv       = $inv_model->orderBy('snapshot_date', 'DESC')->findAll(25);
        $total_inv = 0.0;
        $total_fee = 0.0;
        foreach ($records as $record) {
            $total = $record['transaction_amount'] * -1;
            if (10 < $total) {
                $total_inv += $total;
            } else {
                $total_fee += $total;
            }
        }
        $graph = [];
        foreach ($inv as $row) {
            $graph[$row['snapshot_date']] = [
                'date'  => intval(strtotime($row['snapshot_date']) . '000'),
                'value' => floatval($row['investment_value'])
            ];
        }
        $latest_inv_value = array_values($graph)[0]['value'];
        $latest_date      = array_values($graph)[0]['date'];
        ksort($graph);
        $data  = [
            'page_title'                => 'CPF Investment',
            'slug_group'                => 'employment',
            'slug'                      => '/office/employment/cpf/investment',
            'user_session'              => $session->user,
            'roles'                     => $session->roles,
            'current_role'              => $session->current_role,
            'total_investment_deducted' => $total_inv,
            'total_fees'                => $total_fee,
            'chart_data'                => array_values($graph),
            'latest_inv_value'          => $latest_inv_value,
            'latest_date'               => $latest_date,
            'config'                    => $inv_model->getConfiguration()
        ];
        return view('employment_cpf_investment', $data);
    }

    /**
     * @throws ReflectionException
     */
    public function cpfInvestmentSnapshot(): ResponseInterface
    {
        $cpf_model = new CompanyCPFInvestmentSnapshotModel();
        $log_model = new LogActivityModel();
        $session   = session();
        $data      = [];
        $fields    = [
            'snapshot_date',
            'investment_value',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        $data['created_by'] = $session->user_id;
        // INSERT
        if ($id = $cpf_model->insert($data)) {
            $log_model->insertTableUpdate('company_cpf_investment_snapshot', $id, $data, $session->user_id);
            return $this->response->setJSON([
                'status'   => 'success',
                'toast'    => 'Successfully created new CPF snapshot.',
                'redirect' => base_url($session->locale . '/office/employment/cpf/investment/')
            ]);
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /************************************************************************
     * Freelance
     ************************************************************************/

    /**
     * @return string
     */
    public function freelance(): string
    {
        $session       = session();
        $company      = new CompanyMasterModel();
        $company_raw  = $company->orderBy('company_trade_name', 'asc')->findAll();
        $company_list = [];
        foreach ($company_raw as $row) {
            $company_list[$row['company_country_code']][] = [
                'id'   => $row['id'],
                'name' => $row['company_trade_name']
            ];
        }
        $data          = [
            'page_title'   => 'Freelance',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/freelance',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'companies'    => $company_list
        ];
        return view('employment_freelance', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function freelanceList(): ResponseInterface
    {
        $model              = new CompanyFreelanceProjectModel();
        $columns            = [
            '',
            'company_legal_name',
            'project_title',
            'client_name',
            'freelance_client_id',
            'project_start_date',
            'project_end_date'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $company_id         = intval($this->request->getPost('company_id'));
        $year               = $this->request->getPost('year');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $company_id, $year);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * @param string $freelance_project_id
     * @return string
     */
    public function freelanceEdit(string $freelance_project_id = 'new'): string
    {
        $session       = session();
        $project_model = new CompanyFreelanceProjectModel();
        $page_title    = 'New Freelance Project';
        $project       = [];
        $mode          = 'new';
        if ('new' != $freelance_project_id && is_numeric($freelance_project_id)) {
            $freelance_project_id = $freelance_project_id/$project_model::ID_NONCE;
            $project              = $project_model->find($freelance_project_id);
            $page_title           = 'Edit Freelance Project [' . $project['project_title'] . ']';
            $mode                 = 'edit';
        }
        $data          = [
            'page_title'   => $page_title,
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/freelance',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'project'      => $project,
            'config'       => $project_model->getConfigurations(),
            'mode'         => $mode
        ];
        return view('employment_freelance_edit', $data);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function freelanceSave(): ResponseInterface
    {
        $mode          = $this->request->getPost('mode');
        $project_model = new CompanyFreelanceProjectModel();
        $log_model     = new LogActivityModel();
        $session       = session();
        $id            = $this->request->getPost('id');
        $data          = [];
        $fields        = [
            'company_id',
            'project_title',
            'project_slug',
            'project_start_date',
            'project_end_date',
            'client_name',
            'freelance_client_id',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        if ('edit' == $mode) {
            if ($project_model->update($id, $data)) {
                $log_model->insertTableUpdate('company_freelance_project', $id, $data, $session->user_id);
                $new_id = $id * $project_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the company.',
                    'redirect' => base_url($session->locale . '/office/employment/freelance/edit/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $project_model->insert($data)) {
                $log_model->insertTableUpdate('company_freelance_project', $id, $data, $session->user_id);
                $new_id = $id * $project_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created new company.',
                    'redirect' => base_url($session->locale . '/office/employment/freelance/edit/' . $new_id)
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
     * @throws Exception
     */
    public function freelanceStats(): string
    {
        $lang    = $this->request->getLocale();
        $freelance_model = new CompanyFreelanceProjectModel();
        $freelance_projects = $freelance_model
            ->select('company_freelance_project.*, company_master.company_trade_name, company_freelance_client.client_company_name')
            ->join('company_master', 'company_master.id = company_freelance_project.company_id')
            ->join('company_freelance_client', 'company_freelance_project.freelance_client_id = company_freelance_client.id')
            ->findAll();
        $by_company = [];
        $by_year    = [];
        foreach ($freelance_projects as $project) {
            $year             = substr($project['project_start_date'], 0, 4);
            $start_date       = new DateTime($project['project_start_date']);
            $end_date         = (empty($project['project_end_date']) ? new DateTime('now') : new DateTime($project['project_end_date']));
            $diff             = $start_date->diff($end_date);
            $by_year[$year][] = [
                'company_name'  => $project['company_trade_name'],
                'client_name'   => $project['client_company_name'],
                'project_title' => $project['project_title'],
                'start_date'    => $project['project_start_date'],
                'end_date'      => $project['project_end_date'],
                'days'          => $diff->days,
            ];
            $by_company[$project['company_trade_name']][$project['client_company_name']][] = $project['project_title'];
        }
        $session = session();
        $data    = [
            'lang'         => $lang,
            'page_title'   => 'Freelance Statistics',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/freelance/stats',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'by_company'   => $by_company,
            'by_year'      => $by_year,
        ];
        return view('employment_freelance_stats', $data);
    }

    /**
     * @return string
     */
    public function freelanceClient(): string
    {
        $session       = session();
        $model         = new CompanyFreelanceClientModel();
        $data          = [
            'page_title'   => 'Freelance Clients',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/freelance-client',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'countries'    => $model->getCountries(),
            'client_types' => $model->getClientTypes(),
        ];
        return view('employment_freelance_client', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function freelanceClientList(): ResponseInterface
    {
        $model              = new CompanyFreelanceClientModel();
        $columns            = [
            '',
            'client_company_name',
            'client_type',
            'country_code'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $client_type        = $this->request->getPost('client_type');
        $country_code       = $this->request->getPost('country_code');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $client_type, $country_code);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * Edit Freelance Client
     * @param string $freelance_client_id
     * @return string
     */
    public function freelanceClientEdit(string $freelance_client_id = 'new'): string
    {
        $session      = session();
        $client_model = new CompanyFreelanceClientModel();
        $page_title   = 'New Freelance Client';
        $client       = [];
        $mode         = 'new';
        if ('new' != $freelance_client_id && is_numeric($freelance_client_id)) {
            $freelance_client_id = $freelance_client_id/$client_model::ID_NONCE;
            $client              = $client_model->find($freelance_client_id);
            $page_title          = 'Edit Freelance Client [' . $client['client_company_name'] . ']';
            $mode                = 'edit';
        }
        $data          = [
            'page_title'   => $page_title,
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/freelance-client',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'client'       => $client,
            'config'       => $client_model->getConfigurations(),
            'mode'         => $mode
        ];
        return view('employment_freelance_client_edit', $data);
    }

    /**
     * @throws ReflectionException
     */
    public function freelanceClientSave()
    {
        $mode          = $this->request->getPost('mode');
        $client_model  = new CompanyFreelanceClientModel();
        $log_model     = new LogActivityModel();
        $session       = session();
        $id            = $this->request->getPost('id');
        $data          = [];
        $fields        = [
            'client_company_name',
            'client_type',
            'country_code'
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        if ('edit' == $mode) {
            if ($client_model->update($id, $data)) {
                $log_model->insertTableUpdate('company_freelance_client', $id, $data, $session->user_id);
                $new_id = $id * $client_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the freelance client.',
                    'redirect' => base_url($session->locale . '/office/employment/freelance-client/edit/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $client_model->insert($data)) {
                $log_model->insertTableUpdate('company_freelance_client', $id, $data, $session->user_id);
                $new_id = $id * $client_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created new freelance client.',
                    'redirect' => base_url($session->locale . '/office/employment/freelance-client/edit/' . $new_id)
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
    public function freelanceIncome(): string
    {
        $session       = session();
        $company_model = new CompanyMasterModel();
        $company_raw   = $company_model->select('company_master.*')
            ->join('company_freelance_project', 'company_freelance_project.company_id = company_master.id')
            ->findAll();
        $company_list  = [];
        foreach ($company_raw as $row) {
            $company_list[$row['id']] = $row['company_trade_name'];
        }
        $data          = [
            'page_title'   => 'Freelance Income',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/freelance-income',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'companies'    => $company_list
        ];
        return view('employment_freelance_income', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function freelanceIncomeList(): ResponseInterface
    {
        $model              = new CompanyFreelanceIncomeModel();
        $columns            = [
            '',
            'google_drive_link',
            'company_freelance_project.project_title',
            'company_master.company_trade_name',
            'pay_date',
            'payment_method',
            'payment_currency',
            'base_amount',
            'deduction_amount',
            'claim_amount',
            'subtotal_amount',
            'tax_amount',
            'total_amount',
            'payment_details',
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $company_id         = intval($this->request->getPost('company_id'));
        $project_id         = intval($this->request->getPost('project_id'));
        $year               = $this->request->getPost('year');
        $search_value       = $search['value'];
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $company_id, $project_id, $year);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data'],
            'footer'          => $result['footer']
        ]);
    }

    /**
     * @param string $freelance_income_id
     * @return string
     */
    public function freelanceIncomeEdit(string $freelance_income_id = 'new'): string
    {
        $session       = session();
        $income_model  = new CompanyFreelanceIncomeModel();
        $page_title    = 'New Freelance Income';
        $income        = [];
        $mode          = 'new';
        if ('new' != $freelance_income_id && is_numeric($freelance_income_id)) {
            $freelance_income_id = $freelance_income_id/$income_model::ID_NONCE;
            $income              = $income_model->find($freelance_income_id);
            $page_title          = 'Edit Freelance Income [' . date(DATE_FORMAT_UI, strtotime($income['pay_date'])) . ']';
            $mode                = 'edit';
        }
        $data          = [
            'page_title'   => $page_title,
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/freelance-income',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'income'       => $income,
            'mode'         => $mode,
            'config'       => $income_model->getConfigurations([], $this->currencies)
        ];
        return view('employment_freelance_income_edit', $data);
    }

    /**
     * @return ResponseInterface
     * @throws ReflectionException
     */
    public function freelanceIncomeSave(): ResponseInterface
    {
        $mode          = $this->request->getPost('mode');
        $income_model  = new CompanyFreelanceIncomeModel();
        $log_model     = new LogActivityModel();
        $session       = session();
        $id            = $this->request->getPost('id');
        $data          = [];
        $fields        = [
            'project_id',
            'pay_date',
            'payment_method',
            'payment_currency',
            'base_amount',
            'deduction_amount',
            'claim_amount',
            'subtotal_amount',
            'tax_amount',
            'total_amount',
            'payment_details',
            'google_drive_link',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = ($value !== null && $value !== '') ? $value : null;
        }
        if ('edit' == $mode) {
            if ($income_model->update($id, $data)) {
                $log_model->insertTableUpdate('company_freelance_income', $id, $data, $session->user_id);
                $new_id = $id * $income_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully updated the income.',
                    'redirect' => base_url($session->locale . '/office/employment/freelance-income/edit/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $income_model->insert($data)) {
                $log_model->insertTableUpdate('company_freelance_income', $id, $data, $session->user_id);
                $new_id = $id * $income_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created new income.',
                    'redirect' => base_url($session->locale . '/office/employment/freelance-income/edit/' . $new_id)
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
    public function freelanceIncomeStats(): string
    {
        $lang          = $this->request->getLocale();
        $income_model  = new CompanyFreelanceIncomeModel();
        $income_data   = $income_model
            ->select('company_freelance_income.*, company_master.company_trade_name, company_freelance_project.project_title')
            ->join('company_freelance_project', 'company_freelance_project.id = company_freelance_income.project_id')
            ->join('company_master', 'company_master.id = company_freelance_project.company_id')
            ->findAll();
        $by_year       = [];
        $total_by_year = [];
        $taxes_by_year = [];
        foreach ($income_data as $row) {
            $year             = substr($row['pay_date'], 0, 4);
            $by_year[$year][] = [
                'company_name'    => $row['company_trade_name'],
                'project_title'   => $row['project_title'],
                'pay_date'        => $row['pay_date'],
                'currency'        => $row['payment_currency'],
                'subtotal_amount' => $row['subtotal_amount'],
                'tax_amount'      => $row['tax_amount'],
                'total_amount'    => $row['total_amount'],
            ];
            $total_by_year[$row['payment_currency']][$year] = (isset($total_by_year[$row['payment_currency']][$year]) ? $total_by_year[$row['payment_currency']][$year] + $row['total_amount'] : $row['total_amount']);
            $taxes_by_year[$row['payment_currency']][$year] = (isset($taxes_by_year[$row['payment_currency']][$year]) ? $taxes_by_year[$row['payment_currency']][$year] + $row['tax_amount']   : $row['tax_amount']);
        }
        $chart_data    = [];
        foreach ($total_by_year as $currency => $years) {
            foreach ($years as $year => $amount) {
                $chart_data[$currency][] = [
                    'year'  => "$year",
                    'taxes' => round($taxes_by_year[$currency][$year]),
                    'total' => round($amount)
                ];
            }
        }
        ksort($by_year);
        $session       = session();
        $data          = [
            'lang' => $lang,
            'page_title'   => 'Freelance Income Statistics',
            'slug_group'   => 'employment',
            'slug'         => '/office/employment/freelance-income/stats',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'by_year'      => $by_year,
            'chart_data'   => $chart_data
        ];
        return view('employment_freelance_income_stats', $data);
    }

    /**
     * @param string $year
     * @return string
     */
    public function totalIncome(string $year = ''): string
    {
        $lang    = $this->request->getLocale();
        $session = session();
        if (empty($year)) {
            $year = date('Y');
        }
        $company_model          = new CompanyMasterModel();
        $salary_model           = new CompanySalaryModel();
        $freelance_income_model = new CompanyFreelanceIncomeModel();
        $part_time_income_model = new CompanyPartTimePeriodModel();
        $company_list           = $company_model->where('employment_start_date <=', $year . '-12-31')
            ->groupStart()
            ->where('employment_end_date >=', $year . '-01-01')
            ->orWhere('employment_end_date IS NULL')
            ->groupEnd()
            ->findAll();
        $company_info           = [];
        foreach ($company_list as $company) {
            $company_info[$company['id']] = [
                'company_name'  => $company['company_trade_name'],
                'country_code'  => $company['company_country_code'],
                'currency_code' => $company['company_currency_code'],
            ];
        }
        $salary_records         = $salary_model->where('pay_date >=', $year . '-01-01')
            ->whereIn('pay_type', ['salary', 'claim', 'other'])
            ->where('pay_date <=', $year . '-12-31')
            ->orderBy('pay_date', 'ASC')
            ->findAll();
        $freelance_records     = $freelance_income_model
            ->select('company_freelance_income.*, company_freelance_project.company_id')
            ->join('company_freelance_project', 'company_freelance_income.project_id = company_freelance_project.id')
            ->where('pay_date >=', $year . '-01-01')
            ->where('pay_date <=', $year . '-12-31')
            ->orderBy('pay_date', 'ASC')
            ->findAll();
        $part_time_records     = $part_time_income_model
            ->where('period_end >=', $year . '-01-01')
            ->where('period_end <=', $year . '-12-31')
            ->orderBy('period_end', 'ASC')
            ->findAll();
        $income_records        = [];
        foreach ($salary_records as $record) {
            $income_records[$record['payment_currency']][] = [
                'type'            => 'SL',
                'company_name'    => $company_info[$record['company_id']]['company_name'],
                'pay_date'        => $record['pay_date'],
                'country_code'    => $record['tax_country_code'],
                'base_amount'     => $record['base_amount'],
                'other_amount'    => $record['allowance_amount'] + $record['training_amount'] + $record['overtime_amount'] + $record['adjustment_amount'] + $record['bonus_amount'],
                'taxes'           => $record['us_tax_fed_amount'] + $record['us_tax_state_amount'] + $record['us_tax_city_amount'] + $record['us_tax_med_ee_amount'] + $record['us_tax_oasdi_ee_amount'] + $record['th_tax_amount'] + $record['sg_tax_amount'] + $record['au_tax_amount'],
                'claim_amount'    => $record['claim_amount'],
                'social_security' => $record['social_security_amount'],
                'provident_fund'  => $record['provident_fund_amount'],
                'total'           => $record['total_amount'],
            ];
        }
        foreach ($freelance_records as $record) {
            $income_records[$record['payment_currency']][] = [
                'type'            => 'FL',
                'company_name'    => $company_info[$record['company_id']]['company_name'],
                'pay_date'        => $record['pay_date'],
                'country_code'    => $company_info[$record['company_id']]['country_code'],
                'base_amount'     => $record['base_amount'],
                'other_amount'    => $record['deduction_amount'],
                'taxes'           => $record['tax_amount'],
                'claim_amount'    => $record['claim_amount'],
                'social_security' => 0,
                'provident_fund'  => 0,
                'total'           => $record['total_amount'],
            ];
        }
        foreach ($part_time_records as $record) {
            $currency = $company_info[$record['company_id']]['currency_code'];
            $income_records[$currency][] = [
                'type'            => 'PT',
                'company_name'    => $company_info[$record['company_id']]['company_name'],
                'pay_date'        => $record['period_end'],
                'country_code'    => $company_info[$record['company_id']]['country_code'],
                'base_amount'     => $record['subtotal_income'],
                'other_amount'    => 0,
                'taxes'           => 0,
                'claim_amount'    => 0,
                'social_security' => 0,
                'provident_fund'  => -$record['income_deduction'],
                'total'           => $record['total_income'],
            ];
        }
        $data                  = [
            'lang'              => $lang,
            'page_title'        => 'Total Income',
            'slug_group'        => 'employment',
            'slug'              => '/office/employment/company/total-income',
            'user_session'      => $session->user,
            'roles'             => $session->roles,
            'current_role'      => $session->current_role,
            'year'              => $year,
            'income_records'    => $income_records
        ];
        return view('employment_total_income', $data);
    }

    /********************************************************************************************
     * PART-TIME
     ********************************************************************************************/

    public function partTime(): string
    {
        $lang      = $this->request->getLocale();
        $ptp_model = new CompanyPartTimePeriodModel();
        $periods   = $ptp_model->findAll();
        $results   = [];
        foreach ($periods as $row) {
            $results[$row['id']] = date(DATE_FORMAT_UI, strtotime($row['period_start'])) . ' - ' . date(DATE_FORMAT_UI, strtotime($row['period_end']));
        }
        $data = [
            'lang'       => $lang,
            'page_title' => 'Part Time Schedule',
            'slug_group' => 'employment',
            'slug'       => '/office/employment/part-time',
            'periods'    => $results,
        ];
        return view('employment_part_time', $data);
    }

    public function partTimeList(): ResponseInterface
    {
        $model              = new CompanyPartTimeScheduleModel();
        $columns            = [
            'period_start',
            'scheduled_start',
            'scheduled_end',
            'scheduled_hours',
            'scheduled_break',
            'work_location'
        ];
        $order              = $this->request->getPost('order');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $start_date         = $this->request->getPost('start_date');
        $end_date           = $this->request->getPost('end_date');
        $period_id          = $this->request->getPost('period_id');
        $period_id          = intval($period_id);
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $start_date, $end_date, $period_id);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data'],
            'footer'          => $result['footer']
        ]);
    }

    public function partTimeEdit(int|string $schedule_id)
    {
        $lang  = $this->request->getLocale();
        $model = new CompanyPartTimeScheduleModel();
        $mode  = 'edit';
        $row   = [];
        $page  = 'Edit Part Time Schedule';
        if ('new' == $schedule_id) {
            $mode        = 'new';
            $schedule_id = 0;
            $page        = 'New Part Time Schedule';
        } else {
            $schedule_id = $schedule_id/$model::ID_NONCE;
            $row         = $model->find($schedule_id);
        }
        $data = [
            'lang'       => $lang,
            'page_title' => $page,
            'slug_group' => 'employment',
            'slug'       => '/office/employment/part-time/edit',
            'config'     => $model->getConfigurations(),
            'mode'       => $mode,
            'id'         => $schedule_id,
            'row'        => $row,
        ];
        return view('employment_part_time_edit', $data);
    }

    public function partTimeSave(): ResponseInterface
    {
        $pt_model  = new CompanyPartTimeScheduleModel();
        $log_model = new LogActivityModel();
        $session   = session();
        $id        = $this->request->getPost('id');
        $data      = [];
        $fields    = [
            'period_id',
            'scheduled_start',
            'scheduled_end',
            'scheduled_hours',
            'scheduled_break',
            'work_location',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        try {
            if (0 < $id) {
                if ($data['scheduled_start'] == $data['scheduled_end']) {
                    // DELETE CASE
                    if ($pt_model->delete($id)) {
                        $log_model->insertTableUpdate('company_pt_schedule', $id, [], $session->user_id);
                        return $this->response->setJSON([
                            'status'   => 'success',
                            'toast'    => 'Successfully deleted the part-time schedule.',
                            'redirect' => base_url($session->locale . '/office/employment/part-time')
                        ]);
                    }
                } else {
                    if ($pt_model->update($id, $data)) {
                        $log_model->insertTableUpdate('company_pt_schedule', $id, $data, $session->user_id);
                        return $this->response->setJSON([
                            'status'   => 'success',
                            'toast'    => 'Successfully updated the part-time schedule.',
                            'redirect' => base_url($session->locale . '/office/employment/part-time')
                        ]);
                    }
                }
            } else {
                $data['created_by'] = $session->user_id;
                // INSERT
                if ($id = $pt_model->insert($data)) {
                    $log_model->insertTableUpdate('company_pt_schedule', $id, $data, $session->user_id);
                    return $this->response->setJSON([
                        'status'   => 'success',
                        'toast'    => 'Successfully created new part-time schedule.',
                        'redirect' => base_url($session->locale . '/office/employment/part-time')
                    ]);
                }
            }
            return $this->response->setJSON([
                'status'  => 'error',
                'toast'   => lang('System.status_message.generic_error')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'toast'   => $e->getMessage()
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        }
    }

    public function partTimePayPeriod(): string
    {
        $lang    = $this->request->getLocale();
        $data                  = [
            'lang'              => $lang,
            'page_title'        => 'Part Time Pay Period',
            'slug_group'        => 'employment',
            'slug'              => '/office/employment/part-time/pay-period',
        ];
        return view('employment_part_time_period', $data);
    }

    public function partTimePayPeriodList(): ResponseInterface
    {
        $model              = new CompanyPartTimePeriodModel();
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column       = 'period_start';
        $order_direction    = 'desc';
        $start_date         = $this->request->getPost('start_date');
        $end_date           = $this->request->getPost('end_date');
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $start_date, $end_date);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data'],
            'footer'          => $result['footer']
        ]);
    }

    public function partTimePayPeriodEdit(int|string $period_id): string
    {
        $lang  = $this->request->getLocale();
        $model = new CompanyPartTimePeriodModel();
        $mode  = 'edit';
        $row   = [];
        $page  = 'Edit Part Time Period';
        if ('new' == $period_id) {
            $mode      = 'new';
            $period_id = 0;
            $page      = 'New Part Time Period';
        } else {
            $period_id = $period_id/$model::ID_NONCE;
            $row       = $model->find($period_id);
        }
        $data = [
            'lang'       => $lang,
            'page_title' => $page,
            'slug_group' => 'employment',
            'slug'       => '/office/employment/part-time/period/edit',
            'config'     => $model->getConfigurations(),
            'mode'       => $mode,
            'id'         => $period_id,
            'row'        => $row,
        ];
        return view('employment_part_time_period_edit', $data);
    }

    public function partTimePayPeriodSave(): ResponseInterface
    {
        $pt_model  = new CompanyPartTimePeriodModel();
        $log_model = new LogActivityModel();
        $session   = session();
        $id        = $this->request->getPost('id');
        $data      = [];
        $fields    = [
            'company_id',
            'period_start',
            'period_end',
            'actual_hours',
            'subtotal_income',
            'income_deduction',
            'total_income',
            'average_hourly_income',
            'google_drive_link',
        ];
        foreach ($fields as $field) {
            $value        = $this->request->getPost($field);
            $data[$field] = (!empty($value)) ? $value : null;
        }
        try {
            if (0 < $id) {
                if ($pt_model->update($id, $data)) {
                    $log_model->insertTableUpdate('company_pt_period', $id, $data, $session->user_id);
                    return $this->response->setJSON([
                        'status'   => 'success',
                        'toast'    => 'Successfully updated the part-time pay period.',
                        'redirect' => base_url($session->locale . '/office/employment/part-time/pay-period')
                    ]);
                }
            } else {
                $data['created_by'] = $session->user_id;
                // INSERT
                if ($id = $pt_model->insert($data)) {
                    $log_model->insertTableUpdate('company_pt_period', $id, $data, $session->user_id);
                    return $this->response->setJSON([
                        'status'   => 'success',
                        'toast'    => 'Successfully created new part-time pay period.',
                        'redirect' => base_url($session->locale . '/office/employment/part-time/pay-period')
                    ]);
                }
            }
            return $this->response->setJSON([
                'status'  => 'error',
                'toast'   => lang('System.status_message.generic_error')
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'toast'   => $e->getMessage()
            ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
        }
    }

    public function partTimeStatistics(): string
    {
        $lang       = $this->request->getLocale();
        $ptp_model  = new CompanyPartTimePeriodModel();
        $periods    = $ptp_model->findAll();
        $results    = [];
        $table      = [];
        $totals     = [];
        $monthly    = [];
        foreach ($periods as $row) {
            // chart
            $results[] = [
                'date'     => date('d M Y', strtotime($row['period_end'])),
                'subtotal' => floatval($row['subtotal_income']),
                'total'    => floatval($row['total_income'])
            ];
            // deductions
            $month                        = date('Y-m', strtotime($row['period_end']));
            $table[$month]['subtotal'][]  = floatval($row['subtotal_income']);
            $table[$month]['deduction'][] = floatval($row['income_deduction']);
            $table[$month]['total'][]     = floatval($row['total_income']);
            $totals[$month]               = (isset($totals[$month]) ? $totals[$month] + floatval($row['total_income']) : floatval($row['total_income']));
        }
        foreach ($totals as $month => $total) {
            $monthly[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'total' => $total,
            ];
        }
        $data = [
            'lang'           => $lang,
            'page_title'     => 'Part Time Statistics',
            'slug_group'     => 'employment',
            'slug'           => '/office/employment/part-time/stats',
            'chart_data'     => $results,
            'height'         => count($results) * 40 . 'px',
            'table'          => $table,
            'totals'         => $totals,
            'monthly'        => $monthly,
            'monthly_height' => count($monthly) * 40 . 'px',
        ];
        return view('employment_part_time_statistics', $data);
    }

    public function partTimeCalendar(string $month = ''): string
    {
        $lang      = $this->request->getLocale();
        if (empty($month)) {
            $month = date('Y-m');
        }
        $m_obj     = strtotime($month . '-01');
        $start     = date(DATE_FORMAT_DB, $m_obj) . ' 00:00:00';
        $end       = date('Y-m-t', $m_obj) . ' 23:59:59';
        $pts_model = new CompanyPartTimeScheduleModel();
        $schedules = $pts_model->where('scheduled_start >=', $start)
            ->where('scheduled_end <=', $end)
            ->orderBy('scheduled_start', 'ASC')
            ->findAll();
        $calendar  = [];
        foreach ($schedules as $day) {
            $date            = date('j', strtotime($day['scheduled_start']));
            $calendar[$date] = [
                'start'    => date(TIME_FORMAT_UI, strtotime($day['scheduled_start'])),
                'end'      => date(TIME_FORMAT_UI, strtotime($day['scheduled_end'])),
                'hours'    => $day['scheduled_hours'],
                'break'    => $day['scheduled_break'],
                'location' => $day['work_location'],
            ];
        }
        $data      = [
            'lang'       => $lang,
            'page_title' => 'Part Time Calendar',
            'slug_group' => 'employment',
            'slug'       => '/office/employment/part-time/calendar',
            'yyyymm'     => $month,
            'month'      => date('M Y', $m_obj),
            'calendar'   => $calendar,
            'day_count'  => date('t', $m_obj),
            'dow_first'  => date('N', $m_obj),
        ];
        return view('employment_part_time_calendar', $data);
    }
}