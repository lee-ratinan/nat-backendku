<?php $session = session(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Bootstrap 5 Admin &amp; Dashboard Template">
    <meta name="author" content="Nat Lee">
    <title>Resume | <?= $session->app_name ?></title>
    <link href="<?= base_url('file/favicon.jpg') ?>" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP&family=Noto+Serif+Thai&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        body { font-size: 12px !important; font-family: 'Raleway', 'Noto Serif Thai', 'Noto Serif JP', sans-serif; }
        td { padding:0 !important; }
        .page {
            width: calc(794px - 45px * 2);  /* A4 width (21cm) ≈ 794px, minus padding */
            height: calc(1123px - 45px * 2); /* A4 height (29.7cm) ≈ 1123px */
            padding: 45px; box-sizing: border-box; background: white; margin: auto auto 1rem; box-shadow: 0 0 1rem rgba(0,0,0,0.1);
        }
        h1, h2 { font-weight: bold; margin: 0; }
        h3, h4, h5, h6 { font-weight: bold; margin: .5rem 0; }
        h1 {font-size:15px;}
        h2 {font-size:14px;}
        h3, h4, h5, h6 {font-size:12px;}
        b { font-weight:600; }
        em { font-size:0.9em; }
        small { font-size:0.85em; }
        a { color:#080; text-decoration:none; }
        @page { size: A4; margin: 0; }
        @media print { .page { width: 17cm; height: 25.7cm; padding: 2cm; margin: 0; box-shadow: none; page-break-after: always; } }
    </style>
</head>
<body>
<div class="page">
    <?php
    function print_link($label, $link = ''): string
    {
        if (empty($link) || '#' == $link) {
            return $label;
        }
        return '<a href="' . $link . '" target="_blank">' . $label . '</a>';
    }
    ?>
    <h1 class="text-center">Ratinan Leela-ngamwongsa</h1>
    <h2 class="text-center"><?= $job_title ?? 'Scrum Master' ?> | Agile/Scrum | Artificial Intelligence</h2>
    <p class="text-center">
        <a href="tel:+6597754577">+65 9775 4577</a> &middot;
        <a href="mailto:lee@ratinan.com" target="_blank">lee@ratinan.com</a> &middot;
        <a href="https://lee.ratinan.com" target="_blank">lee.ratinan.com</a> &middot;
        <a href="https://www.linkedin.com/in/ratinanlee/" target="_blank">LinkedIn</a>
    </p>
    <h3>CORE STRENGTHS</h3>
    <p>Strong Agile/Scrum Leadership · AI Applications · FinTech Development · Full-Stack Engineering · Bilingual Communication (English/Thai)</p>
    <h3>EXPERIENCE</h3>
    <?php
    function build_experience($experience): void
    {
        echo '<table class="table table-sm table-borderless">';
        foreach ($experience as $row) {
            // ROW 1
            echo '<tr><td><b>' . $row[0] . '</b></td><td class="text-end"></td></tr>';
            // ROW 2
            echo '<tr><td>' . $row[1] . ', ' . $row[3] . '</td><td class="text-end">' . $row[2] . '</td></tr>';
            // ROW 3
            echo '<tr><td colspan="2" class="pb-2"><small>' . $row[4] . '</small></td></tr>';
        }
        echo '</table>';
    }
    build_experience([
        [
            'Project Manager, System Engineer',
            'SilverLake Axis',
            'Apr 2025 - present',
            'Singapore',
            '- Delivered <b>banking application</b> components aligned with <b>corporate compliance standards</b>, legacy system specifications, and strict <b>regulation guidelines</b>. Proposed improvements to <b>web caching strategies</b> and advocated for secure coding practices in line with <b>OWASP standards</b>.<br>
- Maintained a <b>100% on-time</b> deployment rate, contributing to platform stability and service continuity. Supported <b>cross-regional teams</b> by leveraging bilingual communication (English/Thai) for smoother coordination.'
        ],
        [
            'Web Developer',
            'Freelance at Fastwork',
            'Nov 2024 - present',
            'Remote',
            '- Created <b>SEO-friendly</b>, responsive websites for diverse clients including a golf school, spa, freight forwarder, and more.<br>
- Delivered <b>cost-effective</b> and visually appealing solutions using <b>WordPress</b> and other <b>open-source</b> tools, tailored to each client’s needs.'
        ],
        [
            'Senior Tech Lead',
            'Moolahgo',
            'Jun 2021 - Sep 2024',
            'Singapore',
            '- Architected scalable <b>FinTech systems</b>, including a <b>secured API</b> and a <b>text-based AI assistant</b> that expanded services, and drove <b>300% revenue growth</b> in six months.<br>
- Acted as <b>Scrum Master</b>, removing bottlenecks and improving delivery speed by <b>20%</b>.<br>
- Led development of <b>secure remittance</b>, <b>KYC</b>, and <b>fraud detection</b> systems, ensuring full <b>regulatory compliance</b>. Managed <b>vendor partnerships</b> and aligned third-party services with Agile workflows.<br>
- Mentored a team of six engineers, fostering a <b>high-performance</b> and <b>collaborative</b> culture.'
        ],
        [
            'Tech Lead',
            'Irvins Salted Egg',
            'Sep 2020 - May 2021',
            'Singapore',
            '- Developed a <b>back-office</b> system across multiple <b>Shopify</b> stores, boosting efficiency by <b>80%</b>.<br>
- Instituted <b>fraud prevention</b> policies, reducing chargebacks by <b>90%</b>.<br>
- Reduced <b>cloud infrastructure costs</b> by <b>50%</b> through performance enhancement and <b>optimisation strategies</b>.'
        ],
        [
            'Tech Lead',
            'Secretlab',
            'Feb 2018 - Aug 2020',
            'Singapore',
            '- Developed an <b>advanced backend system</b> that streamlined and automated various operations across departments, praised for <b>saving 2+ hours</b> per day for the logistic teams.<br>
- Enabled <b>1000%+</b> growth in global orders by improving <b>system scalability</b> and <b>reliability</b>.<br>
- Led a <b>multi-zone system migration</b>, enhancing system reliability and <b>performance</b> even more.'
        ],
        [
            'Software Engineer',
            'BuzzCity/Mobads,',
            'Jul 2015 - Jun 2017',
            'Singapore',
            '- Rebuilt the <b>publisher payout</b> and generic <b>accounting system</b>, improving speed by <b>80%</b>.<br>
- Engineered a new <b>publisher revenue calculation</b> system, increasing transaction <b>reliability</b> and <b>reporting accuracy</b>.<br>
- Mentored junior developers, fostering a culture of <b>technical excellence</b> and <b>continuous learning</b>.'
        ],
        [
            'Programmer',
            'DST Worldwide Services',
            'Jun 2012 - Jul 2014',
            'Bangkok, Thailand',
            '- <b>Optimised</b> fund redemption logic, <b>halving processing</b> times.<br>
- Introduced <b>Scrum and Kanban</b>, improving team output by <b>30%</b>.<br>
- Led multiple <b>financial software projects</b>, ensuring timely, reliable delivery.'
        ],
    ]);
    ?>
</div>
<div class="page">
    <!-- EDUCATION -->
    <h3>EDUCATION</h3>
    <?php
    function build_education($education): void
    {
        echo '<table class="table table-sm table-borderless">';
        foreach ($education as $row) {
            echo '<tr>';
            echo '<td><b>' . $row['degree'] . '</b><br>' . $row['school'] . '</td><td class="text-end">' . $row['when'] . '<br>' . $row['where'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    build_education([
        [
            'school' => 'Nanyang Technological University, <em>Wee Kim Wee School of Communication and Information</em>',
            'where'  => 'Singapore',
            'degree' => 'Master of Science in Information Systems',
            'when'   => 'May 2015',
        ],
        [
            'school' => 'Thammasat University, <em>Sirindhorn International Institute of Technology</em>',
            'where'  => 'Pathum Thani, Thailand',
            'degree' => 'Bachelor of Science (First Class Honours) in Computer Science',
            'when'   => 'Mar 2012',
        ]
    ]);
    ?>
    <!-- AWARDS -->
    <h3>AWARDS</h3>
    <?php
    function build_awards($awards): void
    {
        echo '<table class="table table-sm table-borderless">';
        foreach ($awards as $award) {
            echo '<tr>';
            echo '<td><b>' . print_link($award[0], $award[3]) . '</b></td>';
            echo '<td class="text-end">' . $award[1] . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="2"><em>' . $award[2] . '</em></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    build_awards([
        ['Singapore FinTech AI Award', 'Singapore FinTech Festival, Nov 2023', 'Recognised for leading the design and facilitation of <b>AI-driven financial transaction algorithms</b>, enhancing <b>customer experience</b> and <b>security</b>.', 'https://www.mas.gov.sg/news/media-releases/2023/mas-and-sfa-announce-finalists-for-the-2023-singapore-fintech-festival-global-fintech-awards']
    ]);
    ?>
    <!-- CERTIFICATIONS -->
    <h3>CERTIFICATIONS</h3>
    <?php
    function build_certifications($certifications): void
    {
        echo '<table class="table table-sm table-borderless">';
        foreach ($certifications as $org => $certs) {
            echo '<tr>';
            echo '<td colspan="2"><b>' . $org . '</b></td>';
            echo '</tr>';
            foreach ($certs as $cert) {
                echo '<tr>';
                echo '<td>- ' . print_link($cert[0], $cert[2]) . '</td>';
                echo '<td class="text-end">' . $cert[1] . '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
    }
    build_certifications([
        'Scrum.org' => [
            ['Professional Scrum Master I & II (PSM I, II)', 'Oct/Nov 2024', '#'],
            ['Professional Scrum Product Owner I & II (PSPO I, II)', 'Oct 2024/Feb 2025', '#'],
        ],
        'Scrum Alliance' => [
            ['Certified ScrumMaster (CSM)', 'Feb 2025', '#']
        ],
        'Coursera' => [
            ['AWS Cloud Practitioner Essentials course on Coursera', 'Sep 2024', '#'],
            ['Google Project Management, AI Essentials, UX Design, Data Analytics', 'Sep/Oct 2024', '#'],
        ],
        'NTU FlexiMasters' => [
            ['Computational Thinking, Computer Vision, AI Foundation, Game Theory', 'Apr-Jun 2024', '#'],
        ]
    ])
    ?>
    <!-- LANGUAGES -->
    <h3>LANGUAGES</h3>
    <?php
    function build_languages($languages) {
        echo '<table class="table table-sm table-borderless">';
        foreach ($languages as $language) {
            echo '<tr>';
            echo '<td>' . print_link($language[0], $language[3]) . '</td>';
            echo '<td>' . $language[1] . '</td>';
            echo '<td class="text-end"><em>' . $language[2] . '</em></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    build_languages([
        ['English', 'C1/C2 Proficient', 'EF SET', '#'],
        ['Thai (ภาษาไทย)', 'Native', '', '#'],
        ['Japanese （日本語）', 'Pre-A1', 'Expected JLPT N5 (A1) in 2026', '#']
    ]);
    ?>
</div>
</body>
</html>