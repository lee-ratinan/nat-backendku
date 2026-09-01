<?php
$layout = getenv('LAYOUT_FILE_OFFICE');
$layout = (!empty($layout) ? $layout : 'system/_layout_office');
$this->extend($layout);
?>
<?= $this->section('content') ?>
<?php $session = session(); ?>
    <style>h6 {margin-top:1rem;}</style>
    <div class="pagetitle">
        <h1><?= $page_title ?></h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($session->locale . '/office/dashboard') ?>"><?= lang('System.dashboard.page_title') ?></a></li>
                <li class="breadcrumb-item active"><?= $page_title ?></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Resume Generation</h5>
                        <form method="POST" action="<?= base_url($session->locale . '/office/profile/resume/builder') ?>" target="_blank">
                            <table class="table table-sm table-striped table-hover">
                                <tbody>
                                <tr>
                                    <td class="text-center" colspan="5"><h4 class="mt-4">RATINAN “NAT” LEELA-NGAMWONGSA</h4></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <?php generate_form_field('job_title', [
                                            'label'       => 'Job Title',
                                            'value'       => 'Technical Lead',
                                            'placeholder' => 'Technical Lead',
                                            'type'        => 'text'
                                        ], 'Scrum Master'); ?>
                                    </td>
                                    <td colspan="2">
                                        | Software Engineering | Cloud | Agile
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center" colspan="5">+65 9775 4577 - lee@ratinan.com -
                                        linkedin.com/in/ratinanlee - lee.ratinan.com
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <?php
                                        $years = date('Y')-2012;
                                        generate_form_field('summary', [
                                            'label'       => 'Summary',
                                            'placeholder' => 'Summary',
                                            'type'        => 'textarea'
                                        ], 'Experienced [JOB-TITLE] with ' . $years . '+ years in software development, cloud architecture, and Agile leadership. Expertise in optimising system performance, leading cross-functional teams, and scaling high-impact solutions. Proven track record of accelerating project completion by 20% and enhancing system capabilities 10x in e-commerce and FinTech sectors. Passionate about innovation, automation, and mentoring engineering teams to drive business success.');
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b>SKILLS AND EXPERTISE</b></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                        generate_form_field('skill_head[agile]', [
                                            'label'       => 'Header',
                                            'placeholder' => 'Agile Methodologies & Project Management',
                                            'type'        => 'text'
                                        ], 'Agile Methodologies & Project Management');
                                        ?>
                                    </td>
                                    <td colspan="4">
                                        <?php generate_form_field('skills[agile]', [
                                            'label'       => 'Agile Methodologies',
                                            'placeholder' => 'Agile, Scrum, Kanban, Project Management, Sprint Planning, Risk Management, Stakeholder Management, Product Backlog Refinement, Release Planning, Coaching and Mentorship',
                                            'type'        => 'textarea'
                                        ], 'Agile, Scrum, Kanban, Project Management, Sprint Planning, Risk Management, Stakeholder Management, Product Backlog Refinement, Release Planning, Coaching and Mentorship'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php generate_form_field('skill_head[cloud]', [
                                            'label'       => 'Header',
                                            'placeholder' => 'Cloud and Infrastructure',
                                            'type'        => 'text'
                                        ], 'Cloud & Infrastructure'); ?>
                                    </td>
                                    <td colspan="4">
                                        <?php generate_form_field('skills[cloud]', [
                                            'label'       => 'Cloud and Infrastructure',
                                            'placeholder' => 'AWS (EC2, S3, RDS, Lambda), VPS, Docker, Microservices, Cloud Computing',
                                            'type'        => 'textarea'
                                        ], 'AWS (EC2, S3, RDS, Lambda), VPS, Docker, Microservices, Cloud Computing'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php generate_form_field('skill_head[software]', [
                                            'label'       => 'Header',
                                            'placeholder' => 'Software Development and Architecture',
                                            'type'        => 'text'
                                        ], 'Software Development & Architecture'); ?>
                                    </td>
                                    <td colspan="4">
                                        <?php generate_form_field('skills[software]', [
                                            'label'       => 'Software Development and Architecture',
                                            'placeholder' => 'PHP, Laravel, CodeIgniter, Python, API Development, CI/CD, Software Design, UX/UI, Performance Engineering',
                                            'type'        => 'textarea'
                                        ], 'PHP, Laravel, CodeIgniter, Python, API Development, CI/CD, Software Design, UX/UI, Performance Engineering'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php generate_form_field('skill_head[database]', [
                                            'label'       => 'Header',
                                            'placeholder' => 'Database and Data Analysis',
                                            'type'        => 'text'
                                        ], 'Database & Data Analysis'); ?>
                                    </td>
                                    <td colspan="4">
                                        <?php generate_form_field('skills[database]', [
                                            'label'       => 'Database and Data Analysis',
                                            'placeholder' => 'SQL, MySQL, PostgreSQL, NoSQL, Data Analytics, AI, Machine Learning',
                                            'type'        => 'textarea'
                                        ], 'SQL, MySQL, PostgreSQL, NoSQL, Data Analytics, AI, Machine Learning'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php generate_form_field('skill_head[version_control]', [
                                            'label'       => 'Header',
                                            'placeholder' => 'Version Control and Collaboration Tools',
                                            'type'        => 'text'
                                        ], 'Version Control & Collaboration Tools'); ?>
                                    </td>
                                    <td colspan="4">
                                        <?php generate_form_field('skills[version_control]', [
                                            'label'       => 'Version Control and Collaboration Tools',
                                            'placeholder' => 'Git, BitBucket, Jira, Asana, Trello',
                                            'type'        => 'textarea'
                                        ], 'Git, BitBucket, Jira, Asana, Trello'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php generate_form_field('skill_head[leadership]', [
                                            'label'       => 'Header',
                                            'placeholder' => 'Leadership & Teamwork',
                                            'type'        => 'text'
                                        ], 'Leadership & Teamwork'); ?>
                                    </td>
                                    <td colspan="4">
                                        <?php generate_form_field('skills[leadership]', [
                                            'label'       => 'Leadership & Teamwork',
                                            'placeholder' => 'Cross-Functional Collaboration, Mentorship & Coaching, Conflict Resolution, Critical Thinking, Decision-Making, Adaptability, Communication Skills, Stakeholder Management',
                                            'type'        => 'textarea'
                                        ], 'Cross-Functional Collaboration, Mentorship & Coaching, Conflict Resolution, Critical Thinking, Decision-Making, Adaptability, Communication Skills, Stakeholder Management'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b>EXPERIENCE</b></td>
                                </tr>
                                <?php
                                ///////// EXPERIENCE //////////
                                $experience = [
                                    'moolahgo'  => [
                                        'job_title' => 'Technical Lead',
                                        'company'   => 'Moolahgo',
                                        'location'  => 'Singapore',
                                        'period'    => 'Jun 2021 - Sep 2024',
                                        'summary'   => '- <em>Architected scalable financial systems</em>, expanding services to new markets and driving <b>300%</b> business growth in six months.
- <em>Acted as Scrum Master</em>, optimising backlog refinement and eliminating bottlenecks, resulting in <b>20%</b> faster project delivery.
- <em>Developed and implemented secure remittance, KYC, and fraud detection features</em>, ensuring compliance with financial regulations.
- <em>Managed vendor relationships</em> to ensure smooth integration of third-party financial services, aligning external capabilities with internal Scrum workflows.
- <em>Orchestrated cross-functional collaboration and mentored a team of six engineers</em>, fostering a high-performance culture that improved cross-functional collaboration.'
                                    ],
                                    'irvins'    => [
                                        'job_title' => 'Technical Lead',
                                        'company'   => 'Irvins Salted Egg',
                                        'location'  => 'Singapore',
                                        'period'    => 'Sep 2020 - May 2021',
                                        'summary'   => '- <em>Spearheaded the development of an integrated back-office system</em> for multiple Shopify stores, improving operational efficiency by <b>80%</b>.
- <em>Designed and enforced fraud prevention policies</em>, reducing international chargeback cases by <b>90%</b>.
- <em>Optimised infrastructure costs</em> by <b>50%</b> through system enhancements and cloud optimisations.'
                                    ],
                                    'secretlab' => [
                                        'job_title' => 'Technical Lead',
                                        'company'   => 'Secretlab',
                                        'location'  => 'Singapore',
                                        'period'    => 'Feb 2018 - Aug 2020',
                                        'summary'   => '- <em>Developed an advanced backend system</em> that streamlined and automated logistics operations, saving <b>2+ hours</b> per day for teams.
- <em>Enabled the company to scale</em> <b>1000%+</b> in global orders by optimising backend processes and ensuring system reliability.
- <em>Led a system migration across multiple availability zones</em>, improving uptime, performance, and scalability.'
                                    ],
                                    'buzzcity'  => [
                                        'job_title' => 'Software Engineer',
                                        'company'   => 'BuzzCity, Mobads',
                                        'location'  => 'Singapore',
                                        'period'    => 'Jul 2015 - Jun 2016 and Jan - Jun 2017',
                                        'summary'   => '- <em>Redesigned the publisher payout system</em>, enhancing performance and accelerating transaction processing by <b>80%</b>.
- <em>Engineered a new payout recording system</em>, increasing transaction reliability and eliminating reporting inefficiencies.
- <em>Mentored junior developers</em>, fostering a culture of technical excellence and continuous learning.'
                                    ],
                                    'dst'       => [
                                        'job_title' => 'Programmer',
                                        'company'   => 'DST Worldwide Services',
                                        'location'  => 'Bangkok, Thailand',
                                        'period'    => 'Jun 2012 - Jul 2014',
                                        'summary'   => '- <em>Optimised fund redemption calculations</em>, cutting processing time by <b>50%</b> through algorithm and database optimisations.
- <em>Introduced Scrum & Kanban methodologies</em>, increasing team productivity by <b>30%</b> and improving project predictability.
- <em>Led multiple financial software projects</em>, ensuring timely delivery of reporting and transaction solutions.'
                                    ]
                                ];
                                foreach ($experience as $key => $values) {
                                    echo '<tr>';
                                    echo '<td>';
                                    generate_form_field('experiences[' . $key . '][title]', [
                                        'label'       => 'Job Title',
                                        'value'       => $values['job_title'],
                                        'placeholder' => 'Job Title',
                                        'type'        => 'text'
                                    ], $values['job_title']);
                                    echo '</td>';
                                    echo '<td>';
                                    generate_form_field('experiences[' . $key . '][company]', [
                                        'label'       => 'Company',
                                        'value'       => $values['company'],
                                        'placeholder' => 'Company',
                                        'type'        => 'text'
                                    ], $values['company']);
                                    echo '</td><td>';
                                    generate_form_field('experiences[' . $key . '][location]', [
                                        'label'       => 'Location',
                                        'value'       => $values['location'],
                                        'placeholder' => 'Location',
                                        'type'        => 'text'
                                    ], $values['location']);
                                    echo '</td><td colspan="2">';
                                    generate_form_field('experiences[' . $key . '][period]', [
                                        'label'       => 'Period',
                                        'value'       => $values['period'],
                                        'placeholder' => 'Period',
                                        'type'        => 'text'
                                    ], $values['period']);
                                    echo '</td>';
                                    echo '</tr>';
                                    echo '<tr>';
                                    echo '<td colspan="5">';
                                    generate_form_field('experiences[' . $key . '][summary]', [
                                        'label'       => 'Summary',
                                        'placeholder' => 'Summary',
                                        'type'        => 'textarea'
                                    ], $values['summary']);
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="5"><b>EDUCATION</b></td>
                                </tr>
                                <?php
                                ///////// EDUCATION //////////
                                $education = [
                                    'ntu' => [
                                        'degree'   => 'Masters of Science in Information Systems',
                                        'school'   => 'Nanyang Technological University',
                                        'location' => 'Singapore',
                                        'class_of' => '2015',
                                    ],
                                    'tu'  => [
                                        'degree'   => 'Bachelor of Science (First Class Honours) in Computer Science',
                                        'school'   => 'Thammasat University',
                                        'location' => 'Bangkok, Thailand',
                                        'class_of' => '2012',
                                    ]
                                ];
                                foreach ($education as $key => $values) {
                                    echo '<tr>';
                                    echo '<td colspan="3">';
                                    generate_form_field('education[' . $key . '][degree]', [
                                        'label'       => 'Degree',
                                        'value'       => $values['degree'],
                                        'placeholder' => 'Degree',
                                        'type'        => 'text'
                                    ], $values['degree']);
                                    echo '</td>';
                                    echo '<td colspan="2">';
                                    generate_form_field('education[' . $key . '][school]', [
                                        'label'       => 'School',
                                        'value'       => $values['school'],
                                        'placeholder' => 'School',
                                        'type'        => 'text'
                                    ], $values['school']);
                                    echo '</td></tr><tr><td colspan="3">';
                                    generate_form_field('education[' . $key . '][location]', [
                                        'label'       => 'Location',
                                        'value'       => $values['location'],
                                        'placeholder' => 'Location',
                                        'type'        => 'text'
                                    ], $values['location']);
                                    echo '</td><td colspan="2">';
                                    generate_form_field('education[' . $key . '][class_of]', [
                                        'label'       => 'Class of',
                                        'value'       => $values['class_of'],
                                        'placeholder' => 'Class of',
                                        'type'        => 'text'
                                    ], $values['class_of']);
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="5"><b>CERTIFICATIONS</b></td>
                                </tr>
                                <?php
                                ///////// CERTIFICATIONS //////////
                                $certifications = [
                                    'psm1'  => [
                                        'title' => 'Professional Scrum Master I (PSM I)',
                                        'org'   => 'Scrum.org',
                                        'date'  => 'Oct 2024'
                                    ],
                                    'psm2'  => [
                                        'title' => 'Professional Scrum Master II (PSM II)',
                                        'org'   => 'Scrum.org',
                                        'date'  => 'Nov 2024'
                                    ],
                                    'pspo1' => [
                                        'title' => 'Professional Scrum Product Owner I (PSPO I)',
                                        'org'   => 'Scrum.org',
                                        'date'  => 'Oct 2024'
                                    ],
                                    'pspo2' => [
                                        'title' => 'Professional Scrum Product Owner II (PSPO II)',
                                        'org'   => 'Scrum.org',
                                        'date'  => 'Feb 2025'
                                    ],
                                    'csm'   => [
                                        'title' => 'Certified ScrumMaster (CSM)',
                                        'org'   => 'Scrum Alliance',
                                        'date'  => 'Feb 2025'
                                    ],
                                    'aws'   => [
                                        'title' => 'AWS Cloud Practitioner Essentials course on Coursera',
                                        'org'   => 'AWS',
                                        'date'  => 'Sep 2024'
                                    ],
                                    'pm'    => [
                                        'title' => 'Project Management Professional Certificate',
                                        'org'   => 'Google',
                                        'date'  => 'Sep 2024'
                                    ],
                                    'ai'    => [
                                        'title' => 'AI Essentials Certificate',
                                        'org'   => 'Google',
                                        'date'  => 'Sep 2024'
                                    ],
                                    'da'    => [
                                        'title' => 'Data Analytics Professional Certificate',
                                        'org'   => 'Google',
                                        'date'  => 'Oct 2024'
                                    ],
                                    'ux'    => [
                                        'title' => 'UX Design Professional Certificate',
                                        'org'   => 'Google',
                                        'date'  => 'Sep 2024'
                                    ]
                                ];
                                foreach ($certifications as $key => $values) {
                                    echo '<tr>';
                                    echo '<td colspan="3">';
                                    generate_form_field('certifications[' . $key . '][title]', [
                                        'label'       => 'Title',
                                        'value'       => $values['title'],
                                        'placeholder' => 'Title',
                                        'type'        => 'text'
                                    ], $values['title']);
                                    echo '</td>';
                                    echo '<td>';
                                    generate_form_field('certifications[' . $key . '][org]', [
                                        'label'       => 'Organisation',
                                        'value'       => $values['org'],
                                        'placeholder' => 'Organisation',
                                        'type'        => 'text'
                                    ], $values['org']);
                                    echo '</td><td>';
                                    generate_form_field('certifications[' . $key . '][date]', [
                                        'label'       => 'Date',
                                        'value'       => $values['date'],
                                        'placeholder' => 'Date',
                                        'type'        => 'text'
                                    ], $values['date']);
                                    echo '</td></tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="5"><b>AWARDS</b></td>
                                </tr>
                                <?php
                                $awards = [
                                    'ai' => [
                                        'title'   => 'Singapore FinTech AI Award',
                                        'org'     => 'Singapore FinTech Festival',
                                        'date'    => 'Nov 2023',
                                        'summary' => 'Recognised for <em>leading the design and facilitation of AI-driven financial transaction algorithms</em>, enhancing customer experience and security.'
                                    ]
                                ];
                                foreach ($awards as $key => $values) {
                                    echo '<tr>';
                                    echo '<td colspan="2">';
                                    generate_form_field('awards[' . $key . '][title]', [
                                        'label'       => 'Title',
                                        'value'       => $values['title'],
                                        'placeholder' => 'Title',
                                        'type'        => 'text'
                                    ], $values['title']);
                                    echo '</td><td colspan="2">';
                                    generate_form_field('awards[' . $key . '][org]', [
                                        'label'       => 'Organisation',
                                        'value'       => $values['org'],
                                        'placeholder' => 'Organisation',
                                        'type'        => 'text'
                                    ], $values['org']);
                                    echo '</td><td>';
                                    generate_form_field('awards[' . $key . '][date]', [
                                        'label'       => 'Date',
                                        'value'       => $values['date'],
                                        'placeholder' => 'Date',
                                        'type'        => 'text'
                                    ], $values['date']);
                                    echo '</td></tr><tr><td colspan="5">';
                                    generate_form_field('awards[' . $key . '][summary]', [
                                        'label'       => 'Summary',
                                        'placeholder' => 'Summary',
                                        'type'        => 'textarea'
                                    ], $values['summary']);
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="5"><b>LANGUAGES</b></td>
                                </tr>
                                <?php
                                $languages = [
                                    'en' => [
                                        'language' => 'English',
                                        'level'    => 'Fluent'
                                    ],
                                    'th' => [
                                        'language' => 'Thai',
                                        'level'    => 'Native'
                                    ]
                                ];
                                foreach ($languages as $id => $values) {
                                    echo '<tr>';
                                    echo '<td colspan="2">';
                                    generate_form_field('languages[' . $id . '][language]', [
                                        'label'       => 'Language',
                                        'value'       => $values['language'],
                                        'placeholder' => $values['language'],
                                        'type'        => 'text'
                                    ], $values['language']);
                                    echo '</td>';
                                    echo '<td colspan="3">';
                                    generate_form_field('languages[' . $id . '][level]', [
                                        'label'       => 'Level',
                                        'value'       => $values['level'],
                                        'placeholder' => $values['level'],
                                        'type'        => 'text'
                                    ], $values['level']);
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="5"><b>ADDITIONAL INFORMATION</b></td>
                                </tr>
                                <?php
                                $additional_information = [
                                    [
                                        'label' => 'Nationality',
                                        'value' => 'Thai'
                                    ],
                                    [
                                        'label' => 'Residency Status',
                                        'value' => 'Singapore Permanent Resident'
                                    ]
                                ];
                                foreach ($additional_information as $id => $values) {
                                    echo '<tr>';
                                    echo '<td colspan="2">';
                                    generate_form_field('additional_information[' . $id . '][label]', [
                                        'label'       => 'Label',
                                        'value'       => $values['label'],
                                        'placeholder' => $values['label'],
                                        'type'        => 'text'
                                    ], $values['label']);
                                    echo '</td>';
                                    echo '<td colspan="3">';
                                    generate_form_field('additional_information[' . $id . '][value]', [
                                        'label'       => 'Value',
                                        'value'       => $values['value'],
                                        'placeholder' => $values['value'],
                                        'type'        => 'text'
                                    ], $values['value']);
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="return" name="return">
                                    <option value="pdf">PDF</option>
                                    <option value="html">HTML</option>
                                </select>
                                <label for="return">Return Format</label>
                            </div>
                            <div class="text-end">
                                <input class="btn btn-outline-primary btn-sm" type="submit" value="Generate Resume"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let fix_summary = function (job_title) {
                let summary = 'Experienced [JOB-TITLE] with <?= $years ?>+ years in software development, cloud architecture, and Agile leadership. Expertise in optimising system performance, leading cross-functional teams, and scaling high-impact solutions. Proven track record of accelerating project completion by 20% and enhancing system capabilities 10x in e-commerce and FinTech sectors. Passionate about innovation, automation, and mentoring engineering teams to drive business success.';
                $('#summary').val(summary.replace('[JOB-TITLE]', job_title));
            };
            fix_summary($('#job_title').val());
            $('#job_title').on('change', function () {
                let job_title = $(this).val();
                fix_summary(job_title);
            });
        });
    </script>
<?php $this->endSection() ?>