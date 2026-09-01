<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
$this->section('content');
$session = session();
?>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a>
                </li>
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/shavian') ?>">𐑖𐑱𐑝𐑾𐑯</a>
                </li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $page_title ?></h5>
                        <div class="card-text">
                            <form id="transcriber-form">
                                <div class="mb-3">
                                    <label for="text" class="form-label">Input the Shavian message below.</label>
                                    <textarea class="form-control" id="text" name="text" rows="5"
                                              placeholder="Enter text to convert" required></textarea>
                                </div>
                                <button class="btn btn-primary" id="btn-convert">Convert to IPA</button>
                            </form>
                            <hr class="my-3">
                            <p>Please note: this converter will only convert Shavian alphabets to IPA, and will ignore
                                everything else.</p>
                            <table class="table table-sm">
                                <tr>
                                    <th>Original Text</th>
                                    <th>Converted Text</th>
                                </tr>
                                <tr>
                                    <td id="original-message">-</td>
                                    <td id="transcribed-message">-</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>Error Message:</b><br><span id="error-message">-</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#btn-convert').click(function (event) {
                event.preventDefault();
                let text = $('#text').val();
                $.ajax({
                    type: 'POST',
                    url: '<?= base_url($session->locale . '/office/shavian') ?>',
                    data: {mode: 'ipa', text: text},
                    success: function (data) {
                        $('#original-message').text(data.original_message);
                        $('#transcribed-message').text(data.converted_message);
                        $('#error-message').text(data.error);
                    },
                    error: function (data) {
                        $('#original-message').text('-');
                        $('#transcribed-message').text('-');
                        $('#error-message').text('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
<?php $this->endSection() ?>