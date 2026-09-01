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
                            <form id="keyboard-form">
                                <div class="mb-3">
                                    <label for="shavian" class="form-label">Output (Shavian):</label>
                                    <div style="min-height:30px;">: <span id="shavian"></span></div>
                                    <label for="ipa" class="form-label">Output (IPA):</label>
                                    <div style="min-height:30px;">/<span id="ipa"></span>/</div>
                                </div>
                            </form>
                            <div class="row">
                                <?php
                                function print_key($letter, $sound, $name) {
                                    echo '<div class="col mb-3 d-grid">';
                                    echo '<button class="btn btn-success btn-sm shavian-key" data-character="'.$letter.'" data-sound="'.$sound.'">';
                                    echo $letter . '<br>/' . $sound . '/<br>' . $name;
                                    echo '</button>';
                                    echo '</div>';
                                }
                                echo '<div class="col">Tall<br>Unvoiced</div>';
                                print_key('𐑐', 'p', 'peep');
                                print_key('𐑑', 't', 'tot');
                                print_key('𐑒', 'k', 'kick');
                                print_key('𐑓', 'f', 'fee');
                                print_key('𐑔', 'θ', 'thigh');
                                print_key('𐑕', 's', 'so');
                                print_key('𐑖', 'ʃ', 'sure');
                                print_key('𐑗', 'ʧ', 'church');
                                print_key('𐑘', 'j', 'yea');
                                print_key('𐑙', 'ŋ', 'hung');
                                echo '</div><div class="row">';
                                echo '<div class="col">Deep<br>Voiced</div>';
                                print_key('𐑚', 'b', 'bib');
                                print_key('𐑛', 'd', 'dead');
                                print_key('𐑜', 'ɡ', 'gag');
                                print_key('𐑝', 'v', 'vow');
                                print_key('𐑞', 'ð', 'they');
                                print_key('𐑟', 'z', 'zoo');
                                print_key('𐑠', 'ʒ', 'measure');
                                print_key('𐑡', 'ʤ', 'judge');
                                print_key('𐑢', 'w', 'woe');
                                print_key('𐑣', 'h', 'ha-ha');
                                echo '</div><div class="row">';
                                echo '<div class="col">Short (1)</div>';
                                print_key('𐑤', 'l', 'loll');
                                print_key('𐑮', 'r', 'roar');
                                print_key('𐑥', 'm', 'mime');
                                print_key('𐑯', 'n', 'nun');
                                print_key('𐑦', 'ɪ/i', 'if');
                                print_key('𐑰', 'iː', 'eat');
                                print_key('𐑧', 'ɛ', 'egg');
                                print_key('𐑱', 'eɪ', 'age');
                                print_key('𐑨', 'æ', 'ash');
                                print_key('𐑲', 'aɪ', 'ice');
                                echo '</div><div class="row">';
                                echo '<div class="col">Short (2)</div>';
                                print_key('𐑩', 'ə', 'ado');
                                print_key('𐑳', 'ʌ', 'up');
                                print_key('𐑪', 'ɒ', 'on');
                                print_key('𐑴', 'əʊ', 'oak');
                                print_key('𐑫', 'ʊ', 'wool');
                                print_key('𐑵', 'u(ː)', 'ooze');
                                print_key('𐑬', 'aʊ', 'out');
                                print_key('𐑶', 'ɔɪ', 'oil');
                                print_key('𐑭', 'ɑː', 'ah');
                                print_key('𐑷', 'ɔː', 'awe');
                                echo '</div><div class="row">';
                                echo '<div class="col">Compound</div>';
                                print_key('𐑸', 'ɑː(r)', 'are');
                                print_key('𐑹', 'ɔː(r)', 'or');
                                print_key('𐑺', 'ɛə(r)', 'air');
                                print_key('𐑻', 'ɜː(r)', 'err');
                                print_key('𐑼', 'ə(r)', 'array');
                                print_key('𐑽', 'ɪə(r)', 'ear');
                                print_key('𐑾', 'ɪə', 'Ian');
                                print_key('𐑿', 'ju(ː)', 'yew');
                                echo '<div class="col mb-3 d-grid"><button class="btn btn-warning btn-sm shavian-key" data-character=" " data-sound=" ">Space</button></div>';
                                echo '<div class="col mb-3 d-grid"><button class="btn btn-danger btn-sm btn-clear">Clear</button></div>';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.shavian-key').click(function() {
                let shavian = $('#shavian').html(),
                    ipa = $('#ipa').html(),
                    character = $(this).data('character'),
                    sound = $(this).data('sound');
                if ('ɪ/i' === sound) {
                    sound = '<span class="text-danger">ɪ/i</span>';
                }
                $('#shavian').html(shavian + character);
                $('#ipa').html(ipa + sound);
            });
            $('.btn-clear').click(function () {
                $('#shavian').html('');
                $('#ipa').html('');
            });
        });
    </script>
<?php $this->endSection() ?>