<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ratinan Lee - <?= $job_title ?></title>
    <style>
        body {color:#444!important;font-family:Arial,sans-serif;font-size: 12px;margin:0 auto;width:700px;}
        table {width:700px!important;}
        td, p {color:#444!important;font-family:Arial,sans-serif;font-size: 12px;vertical-align:top;}
        h1 {font-size: 18px;}
        h2 {font-size: 16px;}
        h3 {font-size: 14px;}
        h1, h2 {margin: 0;text-align: center;}
        h3 {margin: 10px 0;}
        a {color:#444;text-decoration:none;}
        p {margin: 5px 0;}
        .bb {border-bottom: 1px solid #444;}
        .center {text-align: center;}
        .mt-3 {margin-top:10px;}
        .mb-0 {margin-bottom:0;}
        .my-0 {margin:0;}
    </style>
</head>
<body>
<h1 style="margin-top:10px">RATINAN “NAT” LEELA-NGAMWONGSA</h1>
<h2 style="margin-bottom:8px"><?= $job_title ?> | Software Engineering | Cloud | Agile</h2>
<p class="center">
    <a href="https://wa.me/6597754577">+65 9775 4577</a> -
    <a href="mailto:lee@ratinan.com">lee@ratinan.com</a> -
    <a href="https://www.linkedin.com/in/ratinanlee/" target="_blank">linkedin.com/in/ratinanlee</a> -
    <a href="https://lee.ratinan.com" target="_blank">lee.ratinan.com</a>
</p>
<p><?= str_replace('[JOB-TITLE]', $job_title, $summary) ?></p>
<h3 class="center bb">Skills and Expertise</h3>
<p><b>Agile Methodologies</b><br>Agile, Scrum, Kanban, Project Management, Sprint Planning, Risk Management, Stakeholder Management, Product Backlog Refinement, Release Planning, Coaching and Mentorship</p>
<p><b>Cloud and Infrastructure</b><br>AWS (EC2, S3, RDS, Lambda), VPS, Docker, Microservices, Cloud Computing</p>
<p><b>Software Development and Architecture</b><br>PHP, Laravel, CodeIgniter, Python, API Development, CI/CD, Software Design, UX/UI, Performance Engineering</p>
<p><b>Database and Data Analysis</b><br>SQL, MySQL, PostgreSQL, NoSQL, Data Analytics, AI, Machine Learning</p>
<p><b>Version Control and Collaboration Tools</b><br>Git, BitBucket, Jira, Asana, Trello</p>
<p><b>Leadership & Teamwork</b><br>Cross-Functional Collaboration, Mentorship & Coaching, Conflict Resolution, Critical Thinking, Decision-Making, Adaptability, Communication, Stakeholder Management</p>
<p><b></b><br></p>

<h3 class="center bb">Experience</h3>
<?php
foreach ($experiences as $key => $values) {
    echo '<p class="mt-3 mb-0"><b>' . $values['title'] . '</b><br>' . $values['company'] . ' - ' . $values['location'] . ' | ' . $values['period'] . '</p>';
    echo '<p class="my-0">' . str_replace("\n", '</p><p class="my-0">', $experience[$key]) . '</p>';
    }
?>
<h3 class="center bb">Education</h3>
<p>
    <?php
    foreach ($education as $values) {
        echo '<b>' . $values['degree'] . '</b>, ' . $values['school'] . ', ' . $values['location'] . ' - ' . $values['class_of'] . '<br>';
    }
    ?>
</p>
<div style="page-break-after: always;"></div>
<h3 class="center bb">Certifications</h3>
<p>
    <b>Professional Scrum Master I (PSM I)</b> - Scrum.org<br>
    <b>Professional Scrum Master II (PSM II)</b> - Scrum.org<br>
    <b>Professional Scrum Product Owner I (PSPO I)</b> - Scrum.org<br>
    <b>Professional Scrum Product Owner II (PSPO II)</b> - Scrum.org<br>
    <b>Certified ScrumMaster (CSM)</b> - Scrum Alliance<br>
    <b>AWS Cloud Practitioner Essentials course on Coursera</b> - AWS<br>
    <b>Project Management Professional Certificate</b> - Google<br>
    <b>AI Essentials Certificate</b> - Google<br>
    <b>Data Analytics Professional Certificate</b> - Google<br>
    <b>UX Design Professional Certificate</b> - Google
</p>
<h3 class="center bb">Awards</h3>
<p>
    <b>Singapore FinTech AI Award</b>, Singapore FinTech Festival - Nov 2023<br>
    - Recognised for <em>leading the design and facilitation of AI-driven financial transaction algorithms</em>, enhancing customer experience and security.
</p>

<h3 class="center bb">Languages</h3>
<p>
    <b>English</b> (fluent)<br>
    <b>Thai</b> (native)<br>
</p>
<h3 class="center bb">Additional Information</h3>
<p>
    <b>Nationality:</b> Thai<br>
    <b>Residency Status:</b> Singapore Permanent Resident<br>
</p>
<br>
</body>
</html>