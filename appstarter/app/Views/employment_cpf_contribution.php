<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
<!--    <script src="--><?php //= base_url('assets/vendor/amcharts5/index.js') ?><!--"></script>-->
<!--    <script src="--><?php //= base_url('assets/vendor/amcharts5/xy.js') ?><!--"></script>-->
<!--    <script src="--><?php //= base_url('assets/vendor/amcharts5/percent.js') ?><!--"></script>-->
<!--    <script src="--><?php //= base_url('assets/vendor/amcharts5/themes/Animated.js') ?><!--"></script>-->
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment/cpf') ?>">CPF</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body pt-3">
                        <h5 class="card-title"><i class="fa-solid fa-piggy-bank fa-fw me-3"></i> <?= $page_title ?></h5>
                        <div class="row">
                            <div class="col text-end">
                                <button class="btn btn-outline-primary btn-tab" data-for-id="main_table">All Contributions</button>
                                <button class="btn btn-outline-primary btn-tab" data-for-id="by_company">Contributions by Company</button>
                                <button class="btn btn-outline-primary btn-tab" data-for-id="by_year">Contributions by Year</button>
                                <button class="btn btn-primary btn-tab" data-for-id="by_salary">Salary Records</button>
                            </div>
                        </div>
                        <div class="tab-table" id="div_main_table" style="display:none;">
                            <h3>Main Table</h3>
                            <p class="small">To search for transactions happened in 2025, search "D2025". To search for the contribution month of 2025, search "Y2025".</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tbl_main_table">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">Date</th>
                                        <th rowspan="2">Company</th>
                                        <th rowspan="2">For</th>
                                        <th colspan="2">Staff Contribution</th>
                                        <th colspan="2">Company Match</th>
                                        <th colspan="2">Total</th>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <th>YTD</th>
                                        <th>Amount</th>
                                        <th>YTD</th>
                                        <th>Amount</th>
                                        <th>YTD</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $total_ytd = 0.0; ?>
                                    <?php foreach ($contributions as $row): ?>
                                        <?php
                                        $total_ytd += @$row['transaction_amount'];
                                        if ($row['staff_contribution'] == $row['staff_ytd']) {
                                            $total_ytd = @$row['transaction_amount'];
                                        }
                                        ?>
                                        <tr>
                                            <td data-filter="D<?= substr($row['transaction_date'], 0, 4) ?>" data-sort="<?= $row['transaction_date'] ?>"><?= date(DATE_FORMAT_UI, strtotime($row['transaction_date'] )) ?></td>
                                            <td><?= $companies[$row['company_id']] ?></td>
                                            <td data-filter="Y<?= substr($row['contribution_month'], 0, 4) ?>" data-sort="<?= $row['contribution_month'] ?>"><?= date(MONTH_FORMAT_UI, strtotime($row['contribution_month'] . '-01')) ?></td>
                                            <td class="text-end" data-sort="<?= $row['staff_contribution'] ?>"><?= currency_format('SGD', @$row['staff_contribution']) ?></td>
                                            <td class="text-end" data-sort="<?= $row['staff_ytd'] ?>"><?= currency_format('SGD', @$row['staff_ytd']) ?></td>
                                            <td class="text-end" data-sort="<?= $row['company_match'] ?>"><?= currency_format('SGD', @$row['company_match']) ?></td>
                                            <td class="text-end" data-sort="<?= $row['company_ytd'] ?>"><?= currency_format('SGD', @$row['company_ytd']) ?></td>
                                            <td class="text-end" data-sort="<?= $row['transaction_amount'] ?>"><?= currency_format('SGD', @$row['transaction_amount']) ?></td>
                                            <td class="text-end" data-sort="<?= $total_ytd ?>"><?= currency_format('SGD', $total_ytd) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <hr />
                        </div>
                        <div class="tab-table" id="div_by_company" style="display:none;">
                            <h3>By Company</h3>
                            <div class="table-responsive">
                                <?php
                                $total_staff   = array_sum($sum_staff_contribution_by_company);
                                $total_company = array_sum($sum_company_match_by_company);
                                $total_total   = array_sum($sum_total_by_company);
                                $total_count   = array_sum($count_records_by_company);
                                ?>
                                <table class="table table-striped table-hover" id="tbl_by_company">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">Company</th>
                                        <th colspan="2">Contribution</th>
                                        <th colspan="2">Match</th>
                                        <th colspan="2">Total</th>
                                        <th rowspan="2"># Records</th>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <th>Average</th>
                                        <th>Total</th>
                                        <th>Average</th>
                                        <th>Total</th>
                                        <th>Average</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($company_ids as $company_id): ?>
                                        <tr>
                                            <td><?= $companies[$company_id] ?></td>
                                            <td class="text-end" data-sort="<?= $sum_staff_contribution_by_company[$company_id] ?>"><?= currency_format('SGD', $sum_staff_contribution_by_company[$company_id]) ?></td>
                                            <td class="text-end" data-sort="<?= $avg_staff_contribution_by_company[$company_id] ?>"><?= currency_format('SGD', $avg_staff_contribution_by_company[$company_id]) ?></td>
                                            <td class="text-end" data-sort="<?= $sum_company_match_by_company[$company_id] ?>"><?= currency_format('SGD', $sum_company_match_by_company[$company_id]) ?></td>
                                            <td class="text-end" data-sort="<?= $avg_company_match_by_company[$company_id] ?>"><?= currency_format('SGD', $avg_company_match_by_company[$company_id]) ?></td>
                                            <td class="text-end" data-sort="<?= $sum_total_by_company[$company_id] ?>"><?= currency_format('SGD', $sum_total_by_company[$company_id]) ?></td>
                                            <td class="text-end" data-sort="<?= $avg_total_by_company[$company_id] ?>"><?= currency_format('SGD', $avg_total_by_company[$company_id]) ?></td>
                                            <td class="text-end"><?= $count_records_by_company[$company_id] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td></td>
                                        <td class="text-end"><?= currency_format('SGD', $total_staff) ?></td>
                                        <td></td>
                                        <td class="text-end"><?= currency_format('SGD', $total_company) ?></td>
                                        <td></td>
                                        <td class="text-end"><?= currency_format('SGD', $total_total) ?></td>
                                        <td></td>
                                        <td class="text-end"><?= $total_count ?></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <hr />
                        </div>
                        <div class="tab-table" id="div_by_year" style="display:none;">
                            <h3>By Contribution Year (not by transaction date)</h3>
                            <div class="table-responsive">
                                <?php
                                $total_staff   = array_sum($sum_staff_contribution_by_year);
                                $total_company = array_sum($sum_company_match_by_year);
                                $total_total   = array_sum($sum_total_by_year);
                                $total_count   = array_sum($count_records_by_year);
                                ?>
                                <table class="table table-striped table-hover" id="tbl_by_year">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">Year</th>
                                        <th colspan="2">Contribution</th>
                                        <th colspan="2">Match</th>
                                        <th colspan="2">Total</th>
                                        <th rowspan="2"># Records</th>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <th>Average</th>
                                        <th>Total</th>
                                        <th>Average</th>
                                        <th>Total</th>
                                        <th>Average</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($con_years as $con_year): ?>
                                        <tr>
                                            <td><?= $con_year ?></td>
                                            <td class="text-end" data-sort="<?= $sum_staff_contribution_by_year[$con_year] ?>"><?= currency_format('SGD', $sum_staff_contribution_by_year[$con_year]) ?></td>
                                            <td class="text-end" data-sort="<?= $avg_staff_contribution_by_year[$con_year] ?>"><?= currency_format('SGD', $avg_staff_contribution_by_year[$con_year]) ?></td>
                                            <td class="text-end" data-sort="<?= $sum_company_match_by_year[$con_year] ?>"><?= currency_format('SGD', $sum_company_match_by_year[$con_year]) ?></td>
                                            <td class="text-end" data-sort="<?= $avg_company_match_by_year[$con_year] ?>"><?= currency_format('SGD', $avg_company_match_by_year[$con_year]) ?></td>
                                            <td class="text-end" data-sort="<?= $sum_total_by_year[$con_year] ?>"><?= currency_format('SGD', $sum_total_by_year[$con_year]) ?></td>
                                            <td class="text-end" data-sort="<?= $avg_total_by_year[$con_year] ?>"><?= currency_format('SGD', $avg_total_by_year[$con_year]) ?></td>
                                            <td class="text-end"><?= $count_records_by_year[$con_year] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td></td>
                                        <td class="text-end"><?= currency_format('SGD', $total_staff) ?></td>
                                        <td></td>
                                        <td class="text-end"><?= currency_format('SGD', $total_company) ?></td>
                                        <td></td>
                                        <td class="text-end"><?= currency_format('SGD', $total_total) ?></td>
                                        <td></td>
                                        <td class="text-end"><?= $total_count ?></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <hr />
                        </div>
                        <div class="tab-table" id="div_by_salary">
                            <h3>Contribution Records from Salary</h3>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tbl_by_salary">
                                    <thead>
                                    <tr>
                                        <th rowspan="2">Month</th>
                                        <th rowspan="2">Company</th>
                                        <th colspan="2">Salary Record</th>
                                        <th colspan="3">CPF Record</th>
                                    </tr>
                                    <tr>
                                        <th>Staff Contribution</th>
                                        <th>Total Salary</th>
                                        <th>Staff Contribution</th>
                                        <th>Company Match</th>
                                        <th>Total CPF</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($salary_records_salary as $month => $company_rows): ?>
                                        <?php foreach ($company_rows as $company_id => $record_types) : ?>
                                            <?php
                                            $salary_contribution = 0.0;
                                            $salary_total        = 0.0;
                                            foreach ($record_types as $rec) {
                                                $salary_contribution += ($rec['cpf_amt'] * -1);
                                                $salary_total        += $rec['salary'];
                                            }
                                            $cpf_contribution    = 0.0;
                                            $cpf_match           = 0.0;
                                            $cpf_total           = 0.0;
                                            if (isset($salary_records_cpf[$month][$company_id])) {
                                                foreach ($salary_records_cpf[$month][$company_id] as $rec) {
                                                    $cpf_contribution += $rec['contribution'];
                                                    $cpf_match        += $rec['match'];
                                                    $cpf_total        += $rec['total'];
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td data-sort="<?= $month ?>"><?= date(MONTH_FORMAT_UI, strtotime($month . '-01')) ?></td>
                                                <td><?= $companies[$company_id] ?></td>
                                                <td class="text-end" data-sort="<?= $salary_contribution ?>"><?= currency_format('SGD', $salary_contribution) ?></td>
                                                <td class="text-end" data-sort="<?= $salary_total ?>"><?= currency_format('SGD', $salary_total) ?></td>
                                                <td class="text-end <?= ($salary_contribution == $cpf_contribution ? '' : 'text-danger') ?>" data-sort="<?= $cpf_contribution ?>"><?= currency_format('SGD', $cpf_contribution) ?></td>
                                                <td class="text-end" data-sort="<?= $cpf_match ?>"><?= currency_format('SGD', $cpf_match) ?></td>
                                                <td class="text-end" data-sort="<?= $cpf_total ?>"><?= currency_format('SGD', $cpf_total) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <hr />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#tbl_main_table, #tbl_by_salary').DataTable({
                order: [[0, 'desc']],
                pageLength: 50
            });
            $('#tbl_by_company, #tbl_by_year').DataTable();
            $('.btn-tab').click(function () {
                $('.btn-tab').removeClass('btn-primary').addClass('btn-outline-primary');
                $(this).addClass('btn-primary').removeClass('btn-outline-primary');
                let target_id = $(this).data('for-id');
                $('.tab-table').slideUp();
                $('#div_' + target_id).slideDown();
            });
        });
    </script>
<?php $this->endSection() ?>