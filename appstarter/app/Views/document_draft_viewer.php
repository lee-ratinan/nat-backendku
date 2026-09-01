<html lang="en">
<head>
    <title><?= strip_tags($document['doc_title']) ?> | Document</title>
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
        .history-table td, .history-table th { padding: 1px!important; }
        li p { margin-bottom: 0!important; }
        .table>tbody>tr>td { border: none; vertical-align: top!important; }
        table { border: none; margin-bottom: 1rem; }
        blockquote {border-left: 4px solid #008800;padding: 1em 1.5em;margin: 1em 0;font-style: italic;color: #555;position: relative;}
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
<div class="container">
    <div class="row">
        <div class="col">
            <div class="my-5 py-5 text-end">
                <br><br><br><br><br>
                <h1><?= $document['doc_title'] ?></h1>
                <br>
                <p>DRAFT VERSION</p>
            </div>
            <div class="print-page-break"></div>
            <h1><?= $document['doc_title'] ?></h1>
            <hr class="my-2" />
            <nav id="toc"></nav>
            <br>
            <article>
                <?= $document['doc_content'] ?>
            </article>
            <hr class="my-3" />
            <div class="text-center my-5 py-5">*** END OF DOCUMENT ***</div>
            <div class="print-page-break"></div>
            <div class="print-page-wrapper">
                <p class="mb-3 pe-5">
                    <strong><?= $document['doc_title'] ?></strong>
                </p>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('appstack/js/app.js') ?>"></script>
<script>
    $(document).ready(function () {
        $('blockquote > p').each(function () {
            $(this).replaceWith($(this).text());
        });
        $('article table').each(function () {
            $(this).css({'font-family': 'inherit', color: 'inherit'}).addClass('table table-sm table-borderless');
        });
        $('article p').each(function () {
            if ($(this).text().trim() === '[NEW_PAGE]') {
                $(this).replaceWith('<div class="print-page-break"></div>');
            }
        });
        const $toc = $('#toc');
        const $headings = $('article h2, article h3, article h4');
        let tocHtml = '<ul>';
        let prevLevel = 2;
        $headings.each(function(i) {
            const $el = $(this);
            const tagName = $el.prop('tagName').toLowerCase();
            const level = parseInt(tagName.replace('h', ''));
            let id = $el.attr('id');
            if (!id) {
                id = 'heading-' + i;
                $el.attr('id', id);
            }
            if (level > prevLevel) {
                tocHtml += '<ul>'.repeat(level - prevLevel);
            } else if (level < prevLevel) {
                tocHtml += '</ul>'.repeat(prevLevel - level);
            }
            tocHtml += `<li><a href="#${id}">${$el.text()}</a></li>`;
            prevLevel = level;
        });
        tocHtml += '</ul>'.repeat(prevLevel - 2); // close remaining tags
        tocHtml += '</ul>';
        $toc.html(tocHtml);
    });
</script>
</body>
</html>