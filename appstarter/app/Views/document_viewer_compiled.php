<html lang="en">
<head>
    <?php use Config\Services; $session = Services::session(); ?>
    <title>Work Documents</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= base_url('file/favicon.jpg') ?>" rel="icon">
    <link href="<?= base_url('appstack/css/app.css') ?>" rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        strong { font-weight: 700!important; }
        .container { max-width: 750px; font-family: 'Noto Serif', 'Noto Sans Thai', serif; }
        div, p { color: #111!important; }
        h1, h2, h3, h4, h5, h6 { margin: 1rem 0; font-family: 'Noto Sans', sans-serif; page-break-after: avoid; }
        .history-table td, .history-table th { padding: 1px!important; font-family: 'Noto Sans', sans-serif; font-size:0.8em; text-align: left; }
        li p { margin-bottom: 0!important; }
        .table>tbody>tr>td { border: none; vertical-align: top!important; }
        table { border: none; margin-bottom: 1rem; }
        blockquote {border-left: 4px solid #008800;padding: 1em 1.5em;margin: 1em 0;font-style: italic;color: #555;position: relative;}
        .table>:not(:first-child) { border-top-width: 0 !important; }
        @media print {
            body, .container { background-color:#fff; color: #000!important; }
            strong, b, p, td, th { color: #000!important; }
            .print-page-break { page-break-before: always; break-before: page; }
            .print-page-wrapper {
                height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }
            body::before {
                content: "CONFIDENTIAL";
                position: fixed;top: 40%;left: 5%;transform: rotate(-45deg);
                font-size: 3em;color: rgba(0, 0, 0, 0.1);z-index: 9999;
                pointer-events: none;width: 100%;text-align: center;
            }
        }
    </style>
</head>
<body>
<?php
function create_slug($title)
{
    $title = str_replace('@', '', $title);
    $split = explode('-', $title);
    return strtolower($split[0]);
}
?>
<div class="container">
    <div class="row">
        <div class="col">
            <h1>Table of Contents</h1>
            <ol>
            <?php foreach ($documents as $document) : ?>
                <li><a href="#<?= create_slug($document->doc_title) ?>"><?= $document->doc_title ?></a></li>
            <?php endforeach; ?>
            </ol>
        </div>
    </div>
    <div class="print-page-break"></div>
    <?php foreach ($documents as $document) : ?>
        <div class="row">
            <div class="col">
                <h1 id="<?= create_slug($document->doc_title) ?>"><?= $document->doc_title ?></h1>
                <hr class="my-0" />
                <p>by <?= $document->user_name_first . ' ' . $document->user_name_family ?></p>
                <br><br>
                <article>
                    <?= $document->doc_content ?>
                </article>
                <hr class="my-3" />
                <div class="text-center my-5 py-5">***</div>
                <p class="mb-3 pe-5">
                    <strong><?= $document->doc_title ?></strong> (version <?= $document->version_number ?>) is a document written by <?= $document->user_name_first . ' ' . $document->user_name_family ?> and published on <?= date(DATE_FORMAT_UI, strtotime($document->published_date)) ?>.<br><br>
                    <?php if ('internal' == $mode) : ?>
                        This document contains sensitive, unfiltered, and highly confidential information intended solely for personal reference or authorized internal use. It includes real names, project specifics, proprietary knowledge, and uncensored personal commentary. Distribution, sharing, or publication of this document outside its intended audience is strictly prohibited.<br><br>
                        By accessing this document, you acknowledge the confidentiality of its contents and agree not to reproduce, disclose, or discuss it in any public or unauthorized context. Breach of this confidentiality is a serious offense and may result in consequences, both legal and otherwise.
                    <?php else: ?>
                        This document has been prepared for public access and transparency. All content presented here is based on personal experiences and factual events to the best of my knowledge. Any sensitive, proprietary, or confidential information—including names, organizations, specific project data, and personal identifiers—has been redacted or anonymized for privacy and legal compliance.<br><br>
                        This version is intended for educational, reflective, or general awareness purposes only. Unauthorized use, misrepresentation, or reproduction of this content is strictly prohibited.
                    <?php endif; ?>
                </p>
                <div class="text-center my-5 py-5">*** END OF DOCUMENT ***</div>
            </div>
        </div>
        <div class="print-page-break"></div>
    <?php endforeach; ?>
</div>
<script src="<?= base_url('appstack/js/app.js') ?>"></script>
<script>
    $(document).ready(function () {
        $('blockquote > p').each(function () {
            $(this).replaceWith($(this).html());
        });
        $('s').each(function () {
            <?php if ('internal' == $mode) : ?>
            $(this).replaceWith($(this).text());
            <?php else: ?>
            const originalText = $(this).text();
            const redacted = 'x'.repeat(originalText.length);
            $(this).text(redacted).css({backgroundColor: 'black', color: 'black'});
            <?php endif; ?>
        });
        $('article table').each(function () {
            $(this).css({'font-family': 'inherit', color: 'inherit'}).addClass('table table-sm table-borderless');
        });
        $('article p').each(function () {
            if ($(this).text().trim() === '[NEW_PAGE]') {
                $(this).replaceWith('<div class="print-page-break"></div>');
            }
        });
        $('article').each(function () {
            const $article = $(this);
            let html = $article.html();
            html = html.replace(/\[link:([^\]]+)\](.*?)\[\/link\]/g, function (match, slug, label) {
                const url = '#' + slug;
                return `<a href="${url}">${label}</a>`;
            });
            $article.html(html);
        });
    });
</script>
</body>
</html>