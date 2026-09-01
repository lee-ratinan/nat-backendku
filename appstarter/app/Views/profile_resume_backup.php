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
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="job_title" name="job_title" value="Technical Lead" required>
                                <label for="job_title">Job Title</label>
                            </div>
                            <?php
                            $experience_year = date('Y') - 2012;
                            $fields = [
                                [
                                    'Summary',
                                    'summary',
                                    'Experienced [JOB-TITLE] with ' . $experience_year . '+ years in software development, cloud architecture, and Agile leadership. Expertise in optimising system performance, leading cross-functional teams, and scaling high-impact solutions. Proven track record of accelerating project completion by 20% and enhancing system capabilities 10x in e-commerce and FinTech sectors. Passionate about innovation, automation, and mentoring engineering teams to drive business success.',
                                ],
                                [
                                    'Moolahgo',
                                    'experience[moolahgo]',
                                    '- <em>Architected scalable financial systems</em>, expanding services to new markets and driving <b>300%</b> business growth in six months.
- <em>Led an Agile transformation</em>, optimising backlog refinement and eliminating bottlenecks, resulting in <b>20%</b> faster project delivery.
- <em>Developed and implemented secure remittance, KYC, and fraud detection features</em>, ensuring compliance with financial regulations.
- <em>Orchestrated cross-functional collaboration and mentored a team of six engineers</em>, fostering a high-performance culture that improved cross-functional collaboration.',
                                    100
                                ],
                                [
                                    'Irvins',
                                    'experience[irvins]',
                                    '- <em>Spearheaded the development of an integrated back-office system</em> for multiple Shopify stores, improving operational efficiency by <b>80%</b>.
- <em>Designed and enforced fraud prevention policies</em>, reducing international chargeback cases by <b>90%</b>.
- <em>Optimised infrastructure costs</em> by <b>50%</b> through system enhancements and cloud optimisations.',
                                    100
                                ],
                                [
                                    'Secretlab',
                                    'experience[secretlab]',
                                    '- <em>Developed an advanced backend system</em> that streamlined and automated logistics operations, saving <b>2+ hours</b> per day for teams.
- <em>Enabled the company to scale</em> <b>1000%+</b> in global orders by optimising backend processes and ensuring system reliability.
- <em>Led a system migration across multiple availability zones</em>, improving uptime, performance, and scalability.',
                                    100
                                ],
                                [
                                    'BuzzCity',
                                    'experience[buzzcity]',
                                    '- <em>Redesigned the publisher payout system</em>, enhancing performance and accelerating transaction processing by <b>80%</b>.
- <em>Engineered a new payout recording system</em>, increasing transaction reliability and eliminating reporting inefficiencies.
- <em>Mentored junior developers</em>, fostering a culture of technical excellence and continuous learning.',
                                    100
                                ],
                                [
                                    'DST',
                                    'experience[dst]',
                                    '- <em>Optimised fund redemption calculations</em>, cutting processing time by <b>50%</b> through algorithm and database optimisations.
- <em>Introduced Scrum & Kanban methodologies</em>, increasing team productivity by <b>30%</b> and improving project predictability.
- <em>Led multiple financial software projects</em>, ensuring timely delivery of reporting and transaction solutions.',
                                    100
                                ],
                            ];
                            foreach ($fields as $field) {
                                echo '<div class="form-floating mb-3">';
                                echo '<textarea id="' . $field[1] . '" name="' . $field[1] . '" class="form-control" style="height:' . ($field[3] ?? 100) . 'px;">' . $field[2] . '</textarea>';
                                echo '<label for="' . $field[1] . '">' . $field[0] . '</label>';
                                echo '</div>';
                            }
                            ?>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="return" name="return">
                                    <option value="pdf">PDF</option>
                                    <option value="html">HTML</option>
                                </select>
                                <label for="return">Return Format</label>
                            </div>
                            <div class="text-end">
                                <input class="btn btn-outline-primary btn-sm" type="submit" value="Generate Resume" />
                            </div>
                        </form>
                        <hr />
                        <h5 class="card-title">Cover Letter</h5>
                        <form method="POST" action="<?= base_url($session->locale . '/office/profile/resume/cover-letter') ?>" target="_blank">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name" required>
                                <label for="company_name">Company Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="hiring_manager" name="hiring_manager" value="Hiring Manager" required>
                                <label for="hiring_manager">Hiring Manager</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="position" name="position" placeholder="Position" required>
                                <label for="position">Position</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="company_industry" name="company_industry" placeholder="Company Industry" required>
                                <label for="company_industry">Company Industry [COMPANY]’s commitment to innovations and [COMPANY_INDUSTRY] aligns perfectly ...</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="prev_expertise" name="prev_expertise" placeholder="Previous Expertise" required>
                                <label for="prev_expertise">Previous Expertise (With a proven track record in [PREV_EXPERTISE] and ...)</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="prev_company" name="prev_company" placeholder="Previous Company" required>
                                <label for="prev_company">Previous Company (My previous experience at [PREVIOUS_COMPANY] has ...)</label>
                            </div>


                            COMPANY_INDUSTRY
                            <?php
                            $fields = [
                                [
                                    'Paragraph 1',
                                    'paragraph-1',
                                    'I am writing to express my keen interest in the [POSITION] position at [COMPANY], which was advertised on LinkedIn. With a proven track record in [PREV_EXPERTISE] and a passion for driving innovative solutions, I am confident that I can contribute significantly to your team.',
                                    100
                                ],
                                [
                                    'Paragraph 2',
                                    'paragraph-2',
                                    '[COMPANY]’s commitment to innovations and [COMPANY_INDUSTRY] aligns perfectly with my professional goals. My previous experience at [PREVIOUS_COMPANY] has equipped me with a deep understanding of the unique challenges and opportunities within the consumer product development sector.',
                                    100
                                ],
                                [
                                    'Paragraph 3',
                                    'paragraph-3',
                                    'As a [POSITION], I am adept at bridging the gap between business requirements, available products, and technology with a high satisfaction rate.',
                                    100
                                ],
                                [
                                    'Paragraph 4',
                                    'paragraph-4',
                                    'I am excited about the prospect of joining a dynamic team and contributing to [COMPANY]’s continued growth. I have attached my resume for your review, detailing my qualifications and experience. Thank you for your time and consideration.',
                                    100
                                ],
                            ];
                            foreach ($fields as $field) {
                                echo '<div class="form-floating mb-3">';
                                echo '<textarea id="' . $field[1] . '" name="' . $field[1] . '" class="form-control" style="height:' . ($field[3] ?? 100) . 'px;">' . $field[2] . '</textarea>';
                                echo '<label for="' . $field[1] . '">' . $field[0] . '</label>';
                                echo '</div>';
                            }
                            ?>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="return" name="return">
                                    <option value="pdf">PDF</option>
                                    <option value="html">HTML</option>
                                </select>
                                <label for="return">Return Format</label>
                            </div>
                            <div class="text-end">
                                <input class="btn btn-outline-primary btn-sm" type="submit" value="Generate Cover Letter" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let replace_text = function (target, value) {
                let ids = ['#paragraph-1', '#paragraph-2', '#paragraph-3', '#paragraph-4'];
                $.each(ids, function (i, id) {
                    let text = $(id).val();
                    $(id).val(text.replace(target, value));
                });
            };
            $('#company_name').change(function () {
                replace_text('[COMPANY]', $(this).val());
            });
            $('#position').change(function () {
                replace_text('[POSITION]', $(this).val());
            });
            $('#prev_expertise').change(function () {
                replace_text('[PREV_EXPERTISE]', $(this).val());
            });
            $('#prev_company').change(function () {
                replace_text('[PREVIOUS_COMPANY]', $(this).val());
            });
            $('#company_industry').change(function () {
                replace_text('[COMPANY_INDUSTRY]', $(this).val());
            });
        });
    </script>
<?php $this->endSection() ?>