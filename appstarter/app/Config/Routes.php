<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');
// Login
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::loginScript');
$routes->post('update-expired-password', 'Auth::updateExpiredPassword');
$routes->post('resend-otp', 'Auth::resendOTP');
$routes->post('verify-otp', 'Auth::verifyOTP');
$routes->post('google-signin', 'Auth::loginGoogle');
// Forgot Password
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::forgotPasswordScript');
// Reset Password (click from email)
$routes->get('reset-password/(:any)/(:any)', 'Auth::resetPassword/$1/$2');
$routes->post('reset-password', 'Auth::resetPasswordScript');
// Register - reserved for future use
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::registerScript');
// Logout
$routes->get('logout', 'Auth::logout');
// Files
$routes->get('file/(:any)', 'File::index/$1/0');
$routes->get('download/(:any)', 'File::index/$1/1');
// Cron
$routes->get('cron/run-monthly', 'Cron::runMonthly');
// API
$routes->get('api/journey-stats', 'Api::getJourneyStats');
// SYSTEM
$routes->group('{locale}/office', ['filter' => 'auth'], static function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Office::index');
    $routes->get('dashboard', 'Office::index');
    $routes->get('profile', 'Office::profile');
    $routes->post('profile', 'Office::profileScript');
    $routes->get('switch-role', 'Office::switchRole');
    $routes->post('switch-role', 'Office::switchRoleScript');
    // User
    $routes->get('user', 'User::index');
    $routes->post('user', 'User::list');
    $routes->get('user/create', 'User::edit/new');
    $routes->get('user/edit/(:any)', 'User::edit/$1');
    $routes->post('user/edit', 'User::editScript');
    $routes->get('public-profile/(:any)', 'User::publicProfile/$1');
    // Role
    $routes->get('role', 'Role::index');
    $routes->post('role', 'Role::list');
    $routes->get('role/create', 'Role::edit/new-role');
    $routes->get('role/edit/(:any)', 'Role::edit/$1');
    $routes->post('role/edit', 'Role::editScript');
    $routes->get('role/feature', 'Role::feature');
    // Organization
    $routes->get('organization', 'Organization::index');
    $routes->post('organization', 'Organization::update');
    // Log
    $routes->get('log', 'Log::index');
    $routes->post('log', 'Log::list');
    $routes->get('log/email', 'Log::email');
    $routes->post('log/email', 'Log::emailList');
    $routes->get('log/log-file', 'Log::fileList');
    $routes->get('log/log-file/(:any)', 'Log::fileView/$1');
    /////////////////////////////////////////////////////////////////////////////
    // ALL OTHER CUSTOM ROUTES
    /////////////////////////////////////////////////////////////////////////////
    // FINANCE - EMPLOYMENT
    // company_master table - company with filter by year, country, currency, etc
    $routes->get('employment', 'Employment::index');
    $routes->post('employment/company', 'Employment::companyList');
    $routes->get('employment/company/view/(:any)', 'Employment::companyView/$1');
    $routes->get('employment/company/create', 'Employment::companyEdit/new');
    $routes->get('employment/company/edit/(:any)', 'Employment::companyEdit/$1');
    $routes->post('employment/company/edit', 'Employment::companySave');
    $routes->get('employment/company/stats', 'Employment::companyStats');
    // company_salary table - with filter by year, company, currency, etc
    $routes->get('employment/salary', 'Employment::salary');
    $routes->post('employment/salary', 'Employment::salaryList');
    $routes->get('employment/salary/create', 'Employment::salaryEdit/new');
    $routes->get('employment/salary/edit/(:any)', 'Employment::salaryEdit/$1');
    $routes->post('employment/salary/edit', 'Employment::salarySave');
    $routes->get('employment/salary/stats/currency', 'Employment::salaryStatisticsCurrency');
    $routes->get('employment/salary/stats/currency/(:any)', 'Employment::salaryStatisticsCurrency/$1');
    $routes->get('employment/salary/stats/company', 'Employment::salaryStatisticsCompany');
    $routes->get('employment/salary/stats/company/(:any)', 'Employment::salaryStatisticsCompany/$1');
    // company_cpf table - with filter by year, company (contribution), transaction code, etc
    $routes->get('employment/cpf', 'Employment::cpf');
    $routes->post('employment/cpf', 'Employment::cpfList');
    $routes->get('employment/cpf/create', 'Employment::cpfEdit/new');
    $routes->get('employment/cpf/edit/(:any)', 'Employment::cpfEdit/$1');
    $routes->post('employment/cpf/edit', 'Employment::cpfSave');
    $routes->get('employment/cpf/statement', 'Employment::cpfStatement');
    $routes->get('employment/cpf/statement/create', 'Employment::cpfStatementEdit/new');
    $routes->get('employment/cpf/statement/edit/(:any)', 'Employment::cpfStatementEdit/$1');
    $routes->post('employment/cpf/statement/edit', 'Employment::cpfStatementSave');
    $routes->get('employment/cpf/now', 'Employment::cpfNow');
    $routes->get('employment/cpf/growth', 'Employment::cpfGrowth');
    $routes->get('employment/cpf/growth/(:any)/(:any)', 'Employment::cpfGrowth/$1/$2');
    $routes->get('employment/cpf/stats', 'Employment::cpfStatistics');
    $routes->get('employment/cpf/stats/(:any)', 'Employment::cpfStatistics/$1');
    $routes->get('employment/cpf/contribution', 'Employment::cpfContribution');
    $routes->get('employment/cpf/investment', 'Employment::cpfInvestment');
    $routes->post('employment/cpf/investment/snapshot', 'Employment::cpfInvestmentSnapshot');
    // company_freelance_project
    $routes->get('employment/freelance', 'Employment::freelance');
    $routes->post('employment/freelance', 'Employment::freelanceList');
    $routes->get('employment/freelance/create', 'Employment::freelanceEdit/new');
    $routes->get('employment/freelance/edit/(:any)', 'Employment::freelanceEdit/$1');
    $routes->post('employment/freelance/edit', 'Employment::freelanceSave');
    $routes->get('employment/freelance/stats', 'Employment::freelanceStats');
    // company_freelance_client
    $routes->get('employment/freelance-client', 'Employment::freelanceClient');
    $routes->post('employment/freelance-client', 'Employment::freelanceClientList');
    $routes->get('employment/freelance-client/create', 'Employment::freelanceClientEdit/new');
    $routes->get('employment/freelance-client/edit/(:any)', 'Employment::freelanceClientEdit/$1');
    $routes->post('employment/freelance-client/edit', 'Employment::freelanceClientSave');
    // company_freelance_income
    $routes->get('employment/freelance-income', 'Employment::freelanceIncome');
    $routes->post('employment/freelance-income', 'Employment::freelanceIncomeList');
    $routes->get('employment/freelance-income/create', 'Employment::freelanceIncomeEdit/new');
    $routes->get('employment/freelance-income/edit/(:any)', 'Employment::freelanceIncomeEdit/$1');
    $routes->post('employment/freelance-income/edit', 'Employment::freelanceIncomeSave');
    $routes->get('employment/freelance-income/stats', 'Employment::freelanceIncomeStats');
    // company_pt_period +company_pt_schedule
    $routes->get('employment/part-time', 'Employment::partTime'); // list schedule
    $routes->post('employment/part-time', 'Employment::partTimeList'); // list API
    $routes->get('employment/part-time/edit/(:any)', 'Employment::partTimeEdit/$1'); // add/edit schedule
    $routes->post('employment/part-time/edit', 'Employment::partTimeSave'); // save schedule
    $routes->get('employment/part-time/pay-period', 'Employment::partTimePayPeriod'); // list period
    $routes->post('employment/part-time/pay-period', 'Employment::partTimePayPeriodList');
    $routes->get('employment/part-time/pay-period/edit/(:any)', 'Employment::partTimePayPeriodEdit/$1'); // edit pay period
    $routes->post('employment/part-time/pay-period/edit', 'Employment::partTimePayPeriodSave'); // save pay period
    $routes->get('employment/part-time/stats', 'Employment::partTimeStatistics');
    $routes->get('employment/part-time/calendar', 'Employment::partTimeCalendar');
    $routes->get('employment/part-time/calendar/(:any)', 'Employment::partTimeCalendar/$1');
    // Total income
    $routes->get('employment/company/total-income', 'Employment::totalIncome');
    $routes->get('employment/company/total-income/(:num)', 'Employment::totalIncome/$1');
    // DOCUMENT
    $routes->get('document', 'Document::index');
    $routes->post('document', 'Document::list');
    $routes->get('document/create', 'Document::edit/new');
    $routes->get('document/edit/(:num)', 'Document::edit/$1');
    $routes->post('document/edit', 'Document::save');
    $routes->post('document/delete', 'Document::deleteVersion');
    $routes->post('document/autosave', 'Document::autosave');
    $routes->get('document/draft-document/(:any)', 'Document::viewDraft/$1');
    $routes->get('document/public-document/compiled-work', 'Document::view/public/compiled-work');
    $routes->get('document/public-document/(:any)', 'Document::view/public/$1');
    $routes->get('document/public-document/(:any)/(:any)', 'Document::view/public/$1/$2');
    $routes->get('document/internal-document/(:any)', 'Document::view/internal/$1');
    $routes->get('document/internal-document/(:any)/(:any)', 'Document::view/internal/$1/$2');
    $routes->get('document/fake-name-document/(:any)', 'Document::view/fake-name/$1');
    $routes->get('document/fake-name-document/(:any)/(:any)', 'Document::view/fake-name/$1/$2');
    // FINANCE - TAX
    // tax_master and tax_breakdown tables
    $routes->get('tax', 'Tax::index');
    $routes->post('tax', 'Tax::masterList');
    $routes->get('tax/create', 'Tax::masterEdit/new');
    $routes->get('tax/edit/(:num)', 'Tax::masterEdit/$1');
    $routes->post('tax/edit', 'Tax::masterSave'); // both master and breakdown
    $routes->get('tax/record/edit/create/(:num)', 'Tax::recordEdit/new/$1');
    $routes->get('tax/record/edit/(:num)/(:num)', 'Tax::recordEdit/$2/$1');
    $routes->post('tax/record/edit', 'Tax::recordSave');
    // tax calculator app
    $routes->get('tax/statistics', 'Tax::statistics'); // statistics
    $routes->get('tax/calculator', 'Tax::calculator'); // just a simple tax calculator
    $routes->post('tax/calculator', 'Tax::calculatorAjax'); // just a simple tax calculator
    $routes->get('tax/projection', 'Tax::projection');
    $routes->post('tax/projection', 'Tax::projectionAjax');
    $routes->get('tax/comparison', 'Tax::comparison');
    /////////////////////////////////////////////////////////////////////////////
    /// SHAVIAN
    /////////////////////////////////////////////////////////////////////////////
    $routes->get('shavian', 'Shavian::index');
    $routes->get('shavian-ipa', 'Shavian::toIpa');
    $routes->get('shavian-keyboard', 'Shavian::keyboard');
    $routes->post('shavian', 'Shavian::ajaxTranslator');
    // FICTION
//    $routes->get('fiction', 'Fiction::index'); // done
//    $routes->get('fiction/create', 'Fiction::edit/new');
//    $routes->get('fiction/edit/(:num)', 'Fiction::edit/$1');
//    $routes->post('fiction/edit', 'Fiction::save');
//    $routes->get('fiction/view-entries/(:any)', 'Fiction::viewContents/$1'); // all entries by title_id, no AJAX
//    $routes->get('fiction/export-pdf/(:any)', 'Fiction::exportPdf/$1/content');
//    $routes->get('fiction/export-research/(:any)', 'Fiction::exportPdf/$1/research');
//    $routes->get('fiction/new-entry/(:any)', 'Fiction::editContent/new/$1');
//    $routes->get('fiction/edit-entry/(:any)', 'Fiction::editContent/edit/$1'); // edit fiction_entry
//    $routes->post('fiction/edit-entry', 'Fiction::saveContent');
//    $routes->post('fiction/autosave-entry', 'Fiction::autosaveContent');
//    $routes->post('fiction/upload-cover', 'Fiction::uploadCover');
    /////////////////////////////////////////////////////////////////////////////
    // JOURNEY
    // journey_port table
    $routes->get('journey/port', 'Journey::port');
    $routes->post('journey/port', 'Journey::portList');
    $routes->get('journey/port/create', 'Journey::portEdit/new');
    $routes->get('journey/port/edit/(:any)', 'Journey::portEdit/$1');
    $routes->post('journey/port/edit', 'Journey::portSave');
    $routes->get('journey/port/statistics', 'Journey::portStatistics');
    // journey_operator table
    $routes->get('journey/operator', 'Journey::operator');
    $routes->post('journey/operator', 'Journey::operatorList');
    $routes->get('journey/operator/create', 'Journey::operatorEdit/new');
    $routes->get('journey/operator/edit/(:any)', 'Journey::operatorEdit/$1');
    $routes->post('journey/operator/edit', 'Journey::operatorSave');
    $routes->get('journey/operator/statistics', 'Journey::operatorStatistics');
    $routes->get('journey/operator/aircraft/statistics', 'Journey::aircraftStatistics');
    // journey_master table
    $routes->get('journey/trip', 'Journey::trip');
    $routes->post('journey/trip', 'Journey::tripList');
    $routes->get('journey/trip/create', 'Journey::tripEdit/new');
    $routes->get('journey/trip/edit/(:any)', 'Journey::tripEdit/$1');
    $routes->post('journey/trip/edit', 'Journey::tripSave');
    $routes->get('journey/trip/statistics', 'Journey::tripStatistics');
    $routes->get('journey/trip/finance', 'Journey::tripFinance');
    // journey_transport table
    $routes->get('journey/transport', 'Journey::transport');
    $routes->post('journey/transport', 'Journey::transportList');
    $routes->get('journey/transport/create/(:num)', 'Journey::transportEdit/new/$1');
    $routes->get('journey/transport/edit/(:num)', 'Journey::transportEdit/$1');
    $routes->post('journey/transport/edit', 'Journey::transportSave');
    $routes->get('journey/transport/statistics', 'Journey::transportStatistics');
    // journey_accommodation table
    $routes->get('journey/accommodation', 'Journey::accommodation');
    $routes->post('journey/accommodation', 'Journey::accommodationList');
    $routes->get('journey/accommodation/create/(:num)', 'Journey::accommodationEdit/new/$1');
    $routes->get('journey/accommodation/edit/(:num)', 'Journey::accommodationEdit/$1');
    $routes->post('journey/accommodation/edit', 'Journey::accommodationSave');
    $routes->get('journey/accommodation/statistics', 'Journey::accommodationStatistics');
    // journey_attraction table
    $routes->get('journey/attraction', 'Journey::attraction');
    $routes->post('journey/attraction', 'Journey::attractionList');
    $routes->get('journey/attraction/create/(:num)', 'Journey::attractionEdit/new/$1');
    $routes->get('journey/attraction/edit/(:num)', 'Journey::attractionEdit/$1');
    $routes->post('journey/attraction/edit', 'Journey::attractionSave');
    $routes->get('journey/attraction/statistics', 'Journey::attractionStatistics');
    // journey_bucket_list table
    $routes->get('journey/bucket-list', 'Journey::bucketList');
    $routes->post('journey/bucket-list', 'Journey::bucketListRetrieve');
    $routes->get('journey/bucket-list/create', 'Journey::bucketListEdit/new/$1');
    $routes->get('journey/bucket-list/edit/(:num)', 'Journey::bucketListEdit/$1');
    $routes->post('journey/bucket-list/edit', 'Journey::bucketListSave');
    $routes->get('journey/bucket-list/statistics', 'Journey::bucketListStatistics');
    // journey_holiday table
    $routes->get('journey/holiday', 'Journey::holiday');
    $routes->post('journey/holiday', 'Journey::holidayList');
    $routes->get('journey/holiday/create', 'Journey::holidayEdit/new');
    $routes->get('journey/holiday/edit/(:any)', 'Journey::holidayEdit/$1');
    $routes->post('journey/holiday/edit', 'Journey::holidaySave');
    // Summary, export, statistics, etc
    $routes->get('journey/map', 'Journey::map');
    $routes->get('journey/export', 'Journey::export');
    $routes->get('journey/fix', 'Journey::fix');
    $routes->get('journey/fix2', 'Journey::fix2');
    /////////////////////////////////////////////////////////////////////////////
    // PROFILE
    // Profile
    $routes->get('profile/data', 'Profile::index');
//    $routes->get('profile/plan', 'Profile::plan');
//    $routes->get('profile/plan/(:num)', 'Profile::plan/$1');
//    $routes->get('profile/plan-export/(:num)', 'Profile::planExport/$1');
    // Resume
    $routes->get('profile/resume', 'Profile::resume');
    $routes->post('profile/resume/builder', 'Profile::resumeBuilder');
    /////////////////////////////////////////////////////////////////////////////
    // HEALTH
//    $routes->get('health/gym', 'Health::gym');
//    $routes->get('health/gym-finder', 'Health::gymFinder');
//    $routes->post('health/gym-finder', 'Health::gymFinderList');
    $routes->get('health/vaccine', 'Health::vaccine');
    $routes->get('health/affirmation', 'Health::affirmation');
    $routes->post('health/affirmation', 'Health::affirmationList');
    $routes->post('health/affirmation/edit', 'Health::affirmationSave');
    // Activity
    $routes->get('health/activity', 'Health::activity');
    $routes->post('health/activity', 'Health::activityList');
    $routes->get('health/activity/new/(:any)', 'Health::activityEdit/new/$1');
    $routes->post('health/activity/edit', 'Health::activitySave');
    // Measurement
    $routes->get('health/measurement', 'Health::measurement');
    $routes->post('health/measurement', 'Health::measurementList');
    $routes->get('health/measurement/create', 'Health::measurementEdit/new');
    $routes->get('health/measurement/edit/(:any)', 'Health::measurementEdit/$1');
    $routes->post('health/measurement/edit', 'Health::measurementSave');
    // OOCA
    $routes->get('health/ooca', 'Health::ooca');
    $routes->post('health/ooca', 'Health::oocaList');
    $routes->get('health/ooca/create', 'Health::oocaEdit');
    $routes->get('health/ooca/edit/(:any)', 'Health::oocaEdit/$1');
    $routes->post('health/ooca/edit', 'Health::oocaSave');
    $routes->get('health/ooca/view/(:any)', 'Health::oocaView/$1');
    $routes->get('health/ooca/statistics', 'Health::oocaStatistics');
    $routes->get('health/ooca/export/(:num)', 'Health::oocaExport/$1');
    // MBTI
    $routes->get('health/mbti', 'Health::mbti');
    $routes->post('health/mbti', 'Health::mbtiList');
    // PHQ-9
    $routes->get('health/phq9', 'Health::phq9');
    $routes->post('health/phq9', 'Health::phq9List');
});

// for preflight API calls
$routes->options('(:any)', function() {
    return response()->setStatusCode(204);
});