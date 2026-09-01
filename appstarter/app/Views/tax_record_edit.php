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
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/tax') ?>">Tax</a></li>
                <li class="breadcrumb-item"><a href="<?= $parent_link ?>">Edit Tax Year <?= $for_year ?></a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Tax Record</h5>
                        <?php
                        $fields = [
                            'tax_description',
                            'desc_type',
                            'money_amount',
                            'item_notes'
                        ];
                        foreach ($fields as $field) {
                            generate_form_field($field, $config[$field], @$tax_record[$field]);
                        }
                        ?>
                        <div class="text-end">
                            <button class="btn btn-primary btn-sm" id="btn-save-record"><i class="fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6>How</h6>
                        <ul>
                            <li>Record:
                                <ul>
                                    <li>Employment (___company name___)</li>
                                    <li>LESS Approved Donations/Personal Reliefs (Earned Income Relief/Provident Fund/Life Insurance)</li>
                                    <li>Investment/##### Income</li>
                                </ul>
                            </li>
                            <li>Calculation:
                                <ul>
                                    <li>FIRST/NEXT $##,###.## @ #.##%</li>
                                </ul>
                            </li>
                            <li>Payment/Withheld:
                                <ul>
                                    <li>Depending on the value.</li>
                                </ul>
                            </li>
                            <li>Refunded:
                                <ul>
                                    <li>Value must be negative.</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#btn-save-record').click(function (e) {
                e.preventDefault();
                let id = '<?= $tax_record['id'] ?? 0 ?>',
                    mode = '<?= $mode ?>',
                    tax_year_id = '<?= $tax_year_id ?>',
                    tax_description = $('#tax_description').val(),
                    desc_type = $('#desc_type').val(),
                    money_amount = $('#money_amount').val(),
                    item_notes = $('#item_notes').val();
                if ('' === tax_description || '' === desc_type || '' === money_amount) {
                    toastr.info('Please fill in the required fields.');
                    return false;
                }
                $.ajax({
                        url: '<?= base_url($session->locale . '/office/tax/record/edit') ?>',
                        type: 'post',
                        data: {
                            id: id,
                            mode: mode,
                            tax_year_id: tax_year_id,
                            tax_description: tax_description,
                            desc_type: desc_type,
                            money_amount: money_amount,
                            item_notes: item_notes
                        },
                        success: function (response) {
                            if ('success' === response.status) {
                                toastr.success(response.toast);
                                setTimeout(function () {
                                    window.location.href = response.url;
                                }, 1000);
                            } else {
                                toastr.error(response.toast ?? 'Failed to save tax record.');
                            }
                        },
                        error: function (xhr, status, error) {
                            let response = JSON.parse(xhr.responseText);
                            let error_message = (response.toast ?? '<?= lang('System.status_message.generic_error') ?>');
                            toastr.error(error_message);
                        }
                    }
                );
            });
        });
    </script>
<?php $this->endSection() ?>