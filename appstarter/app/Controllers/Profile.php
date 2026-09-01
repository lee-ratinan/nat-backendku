<?php

namespace App\Controllers;

use App\Models\ProfileIdentityModel;
use CodeIgniter\HTTP\ResponseInterface;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

class Profile extends BaseController
{

    const PERMISSION_REQUIRED = 'profile';
    private $experiences = [
        'moolahgo'  => [
            'title'    => 'Technical Lead',
            'company'  => 'Moolahgo',
            'location' => 'Singapore',
            'period'   => 'Jun 2021 - Sep 2024'
        ],
        'irvins'    => [
            'title'    => 'Technical Lead',
            'company'  => 'Irvins Salted Egg',
            'location' => 'Singapore',
            'period'   => 'Sep 2020 - May 2021'
        ],
        'secretlab' => [
            'title'    => 'Technical Lead',
            'company'  => 'Secretlab',
            'location' => 'Singapore',
            'period'   => 'Feb 2018 - Aug 2020'
        ],
        'buzzcity'  => [
            'title'    => 'Software Engineer',
            'company'  => 'BuzzCity, Mobads',
            'location' => 'Singapore',
            'period'   => 'Jul 2015 - Jun 2016 and Jan - Jun 2017'
        ],
        'dst'       => [
            'title'    => 'Programmer',
            'company'  => 'DST Worldwide Services',
            'location' => 'Bangkok, Thailand',
            'period'   => 'Jun 2012 - Jul 2014'
        ],
    ];
    private $education = [
        [
            'degree'   => 'Masters of Science in Information Systems',
            'school'   => 'Nanyang Technological University',
            'location' => 'Singapore',
            'class_of' => '2015',
        ],
        [
            'degree'   => 'Bachelor of Science (First Class Honours) in Computer Science',
            'school'   => 'Thammasat University',
            'location' => 'Bangkok, Thailand',
            'class_of' => '2012',
        ],
    ];
    private $certifications = [
        'Scrum.org'      => [
            'Professional Scrum Master I (PSM I)',
            'Professional Scrum Master II (PSM II)',
            'Professional Scrum Product Owner I (PSPO I)',
            'Professional Scrum Product Owner I (PSPO II)',
        ],
        'Scrum Alliance' => [
            'Certified Scrum Master (CSM)'
        ],
        'AWS'            => [
            'AWS Cloud Practitioner Essentials course on Coursera'
        ],
        'Google'         => [
            'Google Project Management Professional Certificate',
            'Google Data Analytics Professional Certificate',
            'Google UX Design Professional Certificate',
            'Google AI Essentials Certificate'
        ]
    ];

    /**
     * @param string $content
     * @param string $file_name
     * @return ResponseInterface|string
     */
    private function generatePdf(string $content, string $file_name): ResponseInterface|string
    {
        try {
            $html2pdf = new Html2Pdf('P', 'A4', 'en', true, 'UTF-8', [15, 15, 15, 15]);
            $html2pdf->setDefaultFont('Arial');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->pdf->SetTitle($file_name);
            $html2pdf->pdf->SetAuthor('Ratinan L.');
            $html2pdf->pdf->SetCreator('Ratinan L.');
            $html2pdf->pdf->SetSubject('Resume');
            $html2pdf->pdf->SetKeywords('resume, curriculum vitae, cv');
            $html2pdf->writeHTML($content);
            $file_content = $html2pdf->output($file_name);
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setBody($file_content);
        } catch (Html2PdfException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function index(): string
    {
        $session = session();
        $model   = new ProfileIdentityModel();
        $data    = [
            'page_title'       => 'Profile',
            'slug'             => '/office/profile/data',
            'user_session'     => $session->user,
            'roles'            => $session->roles,
            'current_role'     => $session->current_role,
            'document_types'   => $model->getDocumentTypes(),
            'documents'        => $model->orderBy('country_code', 'ASC')->findAll()
        ];
        return view('profile_data', $data);
    }

//    /**
//     * @param int $year
//     * @return string
//     */
//    public function plan(int $year = 2030): string
//    {
//        $session = session();
//        $data    = [
//            'page_title'       => 'Plan',
//            'slug'             => '/office/profile/plan',
//            'user_session'     => $session->user,
//            'roles'            => $session->roles,
//            'current_role'     => $session->current_role,
//            'year'             => $year
//        ];
//        return view('profile_plan', $data);
//    }
//
//    /**
//     * @param int $year
//     * @return string
//     */
//    public function planExport(int $year = 2030): string
//    {
//        $session = session();
//        $data    = [
//            'page_title'       => 'Plan',
//            'slug'             => '/office/profile/plan_export',
//            'user_session'     => $session->user,
//            'roles'            => $session->roles,
//            'current_role'     => $session->current_role,
//            'year'             => $year
//        ];
//        return view('profile_plan_export', $data);
//    }

    /**
     * @return string
     */
    public function resume(): string
    {
        $session = session();
        $data    = [
            'page_title'       => 'Resume',
            'slug_group'       => 'office',
            'slug'             => '/office/profile/resume',
            'user_session'     => $session->user,
            'roles'            => $session->roles,
            'current_role'     => $session->current_role
        ];
        return view('profile_resume', $data);
    }

    /**
     *
     */
    public function resumeBuilder()
    {
        $return   = $this->request->getPost('return') ?? 'html';
        $data = [
            'job_title'              => $this->request->getPost('job_title') ?? 'Scrum Master/Product Owner',
//            'summary'                => $this->request->getPost('summary'),
//            'skills'                 => $this->request->getPost('skills'),
//            'skill_head'             => $this->request->getPost('skill_head'),
//            'experiences'            => $this->request->getPost('experiences'),
//            'education'              => $this->request->getPost('education'),
//            'certifications'         => $this->request->getPost('certifications'),
//            'awards'                 => $this->request->getPost('awards'),
//            'languages'              => $this->request->getPost('languages'),
//            'additional_information' => $this->request->getPost('additional_information'),
        ];
        $file_name   = 'Ratinan_Lee-resume.pdf';
        $resume_html = view('profile_resume_builder_2025', $data);
        if ('html' == $return) {
            return $resume_html;
        }
        return $this->generatePdf($resume_html, $file_name);
    }

    /**
     * @return ResponseInterface|string
     */
    public function resumeCoverLetter(): ResponseInterface|string
    {
        $return = $this->request->getPost('return');
        $data   = [
            'company_name'   => ucwords(strtolower($this->request->getPost('company_name'))),
            'hiring_manager' => ucwords(strtolower($this->request->getPost('hiring_manager'))),
            'position'       => ucwords(strtolower($this->request->getPost('position'))),
            'paragraph_1'    => $this->request->getPost('paragraph-1'),
            'paragraph_2'    => $this->request->getPost('paragraph-2'),
            'paragraph_3'    => $this->request->getPost('paragraph-3'),
            'paragraph_4'    => $this->request->getPost('paragraph-4'),
            'return'         => $return
        ];
        $file_name   = 'Ratinan L - ' . $data['company_name'] . ' - Cover Letter.pdf';
        $letter_html = view('profile_cover_letter', $data);
        if ('html' == $return) {
            return $letter_html;
        }
        return $this->generatePdf($letter_html, $file_name);
    }
}