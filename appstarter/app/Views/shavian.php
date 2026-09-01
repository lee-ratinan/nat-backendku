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
            <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
            <li class="breadcrumb-item active">𐑖𐑱𐑝𐑾𐑯</li>
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
                                <label for="text" class="form-label">This is the English/Shavian transcriber. You may input English or Shavian in the text box below.</label>
                                <textarea class="form-control" id="text" name="text" rows="5" placeholder="Enter text to transcribe / 𐑧𐑯𐑑𐑼 𐑑𐑧𐑒𐑕𐑑 𐑑 𐑑𐑮𐑨𐑯𐑕𐑒𐑮𐑲𐑚" required></textarea>
                            </div>
                            <button class="btn btn-primary" id="btn-transcribe">Transcribe</button>
                        </form>
                        <hr class="my-3">
                        <table class="table table-sm">
                            <tr>
                                <th>Original Text</th>
                                <th>Transcribed Text</th>
                            </tr>
                            <tr>
                                <td id="original-message">-</td>
                                <td id="transcribed-message">-</td>
                            </tr>
                            <tr>
                                <td colspan="2"><b>Error Message:</b><br><span id="error-message">-</span></td>
                            </tr>
                            <tr>
                                <td colspan="2"><small><b>Note:</b> The words with # are not found in the dictionary. The words in the [] are those with multiple transcriptions. Please check with the dictionary, <a href="https://readlex.pythonanywhere.com/" target="_blank">https://readlex.pythonanywhere.com/</a> to pick the right transcription.</small></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#btn-transcribe').click(function(event) {
            event.preventDefault();
            let text = $('#text').val();
            $('#btn-transcribe').prop('disabled', true);
            $.ajax({
                type: 'POST',
                url: '<?= base_url($session->locale . '/office/shavian') ?>',
                data: { mode: 'transcribe', text: text },
                success: function(response) {
                    $('#btn-transcribe').prop('disabled', false);
                    $('#original-message').text(response.original_message);
                    $('#transcribed-message').html(response.transcribed_message);
                    $('#error-message').text(response.error);
                },
                error: function() {
                    $('#btn-transcribe').prop('disabled', false);
                    $('#original-message').text('-');
                    $('#transcribed-message').text('-');
                    $('#error-message').text('Error: Unable to transcribe text.');
                }
            });
        });
    });
</script>
<?php $this->endSection() ?>
