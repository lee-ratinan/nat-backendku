<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/employment') ?>">Employment</a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h3>Income of <?= $year ?></h3>
                        <?php for ($y = 2010; $y <= date('Y'); $y++) : ?>
                            <a class="btn btn-<?= ($y == $year ? '' : 'outline-') ?>success btn-sm mb-2" href="<?= base_url($lang . '/office/employment/company/total-income/' . $y) ?>"><?= $y ?></a>
                        <?php endfor; ?>
                        <div class="table-responsive">
                            <?php
                            $fields = [
                                'base_amount',
                                'other_amount',
                                'taxes',
                                'claim_amount',
                                'social_security',
                                'provident_fund',
                                'total'
                            ];
                            ?>
                            <?php foreach ($income_records as $currency_code => $records) : ?>
                                <?php
                                foreach ($fields as $field) {
                                    $total[$field] = 0;
                                }
                                ?>
                                <h4 class="mt-3"><?= $currency_code ?></h4>
                                <table class="table table-borderless table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th style="min-width:80px;">Type</th>
                                        <th style="min-width:120px;">Company</th>
                                        <th class="text-start" style="min-width:120px;">Date</th>
                                        <th style="min-width:120px;">Country</th>
                                        <th class="text-end" style="min-width:120px;">Amount</th>
                                        <th class="text-end" style="min-width:120px;">Other Amount</th>
                                        <th class="text-end" style="min-width:120px;">Taxes</th>
                                        <th class="text-end" style="min-width:120px;">Claim Amount</th>
                                        <th class="text-end" style="min-width:120px;">Social Security</th>
                                        <th class="text-end" style="min-width:120px;">Provident Fund</th>
                                        <th class="text-end" style="min-width:120px;">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($records as $record) : ?>
                                        <tr>
                                            <td><?= $record['type'] ?></td>
                                            <td><?= $record['company_name'] ?></td>
                                            <td class="text-start" data-sort="<?= $record['pay_date'] ?>"><?= date(DATE_FORMAT_UI, strtotime($record['pay_date'])) ?></td>
                                            <td><?= lang('ListCountries.countries.' . $record['country_code'] . '.common_name') ?></td>
                                            <td class="text-end"><?= currency_format($currency_code, $record['base_amount'] ?? 0.0) ?></td>
                                            <td class="text-end"><?= currency_format($currency_code, $record['other_amount'] ?? 0.0) ?></td>
                                            <td class="text-end"><?= currency_format($currency_code, $record['taxes'] ?? 0.0) ?></td>
                                            <td class="text-end"><?= currency_format($currency_code, $record['claim_amount'] ?? 0.0) ?></td>
                                            <td class="text-end"><?= currency_format($currency_code, $record['social_security'] ?? 0.0) ?></td>
                                            <td class="text-end"><?= currency_format($currency_code, $record['provident_fund'] ?? 0.0) ?></td>
                                            <td class="text-end"><?= currency_format($currency_code, $record['total'] ?? 0.0) ?></td>
                                        </tr>
                                        <?php
                                        foreach ($fields as $field) {
                                            $total[$field] += $record[$field];
                                        }
                                        ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <?php foreach ($fields as $field) : ?>
                                            <td class="text-end"><?= currency_format($currency_code, $total[$field]) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    </tfoot>
                                </table>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('table').DataTable({
                fixedHeader: true,
                searching: true,
                pageLength: 25,
                order: [[1, 'asc']]
            });
        });
    </script>
<?php $this->endSection() ?>