<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ratinan Lee - <?= $job_title ?></title>
    <style>
        body {color:#444!important;font-family:Arial,sans-serif;font-size: 12px;margin:0 auto;width:700px;padding:15px 0;}
        table {width:700px!important;}
        td, p {color:#444!important;font-family:Arial,sans-serif;font-size: 12px;vertical-align:top;}
        h1 {font-size: 18px;}
        h2 {font-size: 16px;}
        h3 {font-size: 16px; text-transform: uppercase;}
        h4 {font-size: 14px;}
        h1, h2, h4 {margin: 0;}
        h3 {margin: 10px 0;}
        a {color:#444;text-decoration:none;}
        p {margin: 5px 0;}
        .center { text-align: center}
    </style>
</head>
<body>
<div class="center">
    <h1>RATINAN “NAT” LEELA-NGAMWONGSA</h1>
    <h2><?= $job_title ?> | Software Engineering | Cloud | Agile</h2>
    <p>
        <a href="https://lee.ratinan.com/whatsapp?text=Hi Nat, I click this link from your resume. Can we chat?">+65 9775 4577</a> -
        <a href="mailto:lee@ratinan.com">lee@ratinan.com</a> -
        <a href="https://www.linkedin.com/in/ratinanlee/">linkedin.com/in/ratinanlee</a> -
        <a href="https://lee.ratinan.com">lee.ratinan.com</a>
    </p>
    <hr>
</div>
<p><?= $summary ?></p>

<h3>Skills and Expertise</h3>
<?php foreach ($skills as $key => $skill): ?>
    <h4><?= $skill_head[$key] ?></h4>
    <p><?= $skill ?></p>
<?php endforeach; ?>

<h3>Experience</h3>
<?php foreach ($experiences as $experience): ?>
    <h4><?= $experience['title'] ?> - <em><?= $experience['company'] ?>, <?= $experience['location'] ?></em></h4>
    <p><?= $experience['period'] ?><br><?= str_replace("\n", '<br>', $experience['summary']) ?></p>
<?php endforeach; ?>

<div style="page-break-after: always;"></div>

<h3>Education</h3>
<?php foreach ($education as $row): ?>
    <p><b><?= $row['degree'] ?></b> - <em><?= $row['school'] ?>, <?= $row['location'] ?></em><br>Class of <?= $row['class_of'] ?></p>
<?php endforeach; ?>

<h3>Certifications</h3>
<?php foreach ($certifications as $certification): ?>
    <p><b><?= $certification['title'] ?></b> - <em><?= $certification['org'] ?>, <?= $certification['date'] ?></em></p>
<?php endforeach; ?>

<h3>Awards</h3>
<?php foreach ($awards as $award): ?>
    <p><b><?= $award['title'] ?></b> - <em><?= $award['org'] ?>, <?= $award['date'] ?></em><br><?= $award['summary'] ?></p>
<?php endforeach; ?>

<h3>Languages</h3>
<?php foreach ($languages as $language): ?>
    <p><b><?= $language['language'] ?></b> - <?= $language['level'] ?></p>
<?php endforeach; ?>

<h3>Additional Information</h3>
<?php foreach ($additional_information as $info): ?>
    <p><b><?= $info['label'] ?></b> - <?= $info['value'] ?></p>
<?php endforeach; ?>
<br>
</body>
</html>