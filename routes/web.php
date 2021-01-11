<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Route Customer

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});
Auth::routes();
//Route Admin
Route::group(['prefix' => 'admin', 'middleware' => 'page.admin'], function () {
    Route::get('/', 'Auth\AdminLoginController@index')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.post');
    Route::post('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::get('/dashboard', 'Admin\DashboardController@index')->name('dashboard.index');
        Route::get('/dashboard/medicalrecord/{id}', 'Admin\DashboardController@medicalrecord')->name('dashboard.medicalrecord');
        Route::get('/dashboard/healthmeter/{id}', 'Admin\DashboardController@healthmeter')->name('dashboard.healthmeter');
        Route::get('/dashboard/chart', 'Admin\DashboardController@chart')->name('dashboard.chart');
        //Route Formula
        Route::get('/formula/read', 'Admin\FormulaController@read')->name('formula.read');
        Route::get('/formula/select', 'Admin\FormulaController@select')->name('formula.select');
        Route::get('/formula/restore/{id}', 'Admin\FormulaController@restore')->name('formula.restore');
        Route::get('/formula/delete/{id}', 'Admin\FormulaController@delete')->name('formula.delete');
        Route::resource('/formula', 'Admin\FormulaController');
        //Route Formula Detail
        Route::get('/formuladetail/read', 'Admin\FormulaDetailController@read')->name('formuladetail.read');
        Route::resource('/formuladetail', 'Admin\FormulaDetailController');
        //Route Category
        Route::get('/category/read', 'Admin\CategoryController@read')->name('category.read');
        Route::get('/category/select', 'Admin\CategoryController@select')->name('category.select');
        Route::resource('/category', 'Admin\CategoryController');
        //Route Sub Category
        Route::get('/subcategory/read', 'Admin\SubCategoryController@read')->name('subcategory.read');
        Route::resource('/subcategory', 'Admin\SubCategoryController');
        //Route Department
        Route::get('/department/read', 'Admin\DepartmentController@read')->name('department.read');
        Route::get('/department/select', 'Admin\DepartmentController@select')->name('department.select');
        Route::get('/department/import', 'Admin\DepartmentController@import')->name('department.import');
        Route::post('/department/preview', 'Admin\DepartmentController@preview')->name('department.preview');
        Route::post('/department/storemass', 'Admin\DepartmentController@storemass')->name('department.storemass');
        Route::resource('/department', 'Admin\DepartmentController');
        //Route Title
        Route::get('/title/read', 'Admin\TitleController@read')->name('title.read');
        Route::get('/title/employee', 'Admin\TitleController@employee')->name('title.employee');
        Route::get('/title/select', 'Admin\TitleController@select')->name('title.select');
        Route::get('/title/import', 'Admin\TitleController@import')->name('title.import');
        Route::post('/title/preview', 'Admin\TitleController@preview')->name('title.preview');
        Route::post('/title/storemass', 'Admin\TitleController@storemass')->name('title.storemass');
        Route::resource('/title', 'Admin\TitleController');
        //Route Grade
        Route::get('/grade/read', 'Admin\GradeController@read')->name('grade.read');
        Route::get('/grade/select', 'Admin\GradeController@select')->name('grade.select');
        Route::resource('/grade', 'Admin\GradeController');
        //Route Employee
        Route::get('/employee/read', 'Admin\EmployeeController@read')->name('employee.read');
        Route::get('/employee/import', 'Admin\EmployeeController@import')->name('employee.import');
        Route::post('/employee/preview', 'Admin\EmployeeController@preview')->name('employee.preview');
        Route::post('/employee/storemass', 'Admin\EmployeeController@storemass')->name('employee.storemass');
        Route::get('/employee/select', 'Admin\EmployeeController@select')->name('employee.select');
        Route::get('/employee/medis', 'Admin\EmployeeController@medis')->name('employee.medis');
        Route::get('/employee/history', 'Admin\EmployeeController@history')->name('employee.history');
        Route::get('/employee/temperature', 'Admin\EmployeeController@temperature')->name('employee.temperature');
        Route::get('/employee/saturasi', 'Admin\EmployeeController@saturasi')->name('employee.saturasi');
        Route::get('/employee/exportmedis', 'Admin\EmployeeController@exportmedis')->name('employee.exportmedis');
        Route::resource('/employee', 'Admin\EmployeeController');
        //Route Employee Detail
        Route::get('/employeefamily/read', 'Admin\EmployeeFamilyController@read')->name('employeefamily.read');
        Route::get('/employeefamily/select', 'Admin\EmployeeFamilyController@select')->name('employeefamily.select');
        Route::resource('/employeefamily', 'Admin\EmployeeFamilyController');
        //Route Health Meter
        Route::get('/healthmeter/read', 'Admin\HealthMeterController@read')->name('healthmeter.read');
        Route::get('/healthmeter/select', 'Admin\HealthMeterController@select')->name('healthmeter.select');
        Route::post('/healthmeter/restore', 'Admin\HealthMeterController@restore')->name('healthmeter.restore');
        Route::post('/healthmeter/delete', 'Admin\HealthMeterController@delete')->name('healthmeter.delete');
        Route::resource('/healthmeter', 'Admin\HealthMeterController');
        //Province
        Route::get('/province/select', 'Admin\ProvinceController@select')->name('province.select');
        //Region
        Route::get('/region/select', 'Admin\RegionController@select')->name('region.select');
        //District
        Route::get('/district/select', 'Admin\DistrictController@select')->name('district.select');
        //Route Config
        Route::get('/config', 'Admin\ConfigController@index')->name('config.index');
        Route::put('/config', 'Admin\ConfigController@update')->name('config.update');
        //Route Site
        Route::get('/site/read', 'Admin\SiteController@read')->name('site.read');
        Route::get('/site/select', 'Admin\SiteController@select')->name('site.select');
        Route::resource('/site', 'Admin\SiteController')->except(['show']);
        //Route User Site
        Route::get('/siteuser/read', 'Admin\SiteUserController@read')->name('siteuser.read');
        Route::get('/siteuser/select', 'Admin\SiteUserController@select')->name('siteuser.select');
        Route::post('/siteuser/store', 'Admin\SiteUserController@store')->name('siteuser.store');
        Route::delete('/siteuser/{id}', 'Admin\SiteUserController@destroy')->name('siteuser.destroy');
        //Route Menu
        Route::post('/menu/order', 'Admin\MenuController@order')->name('menu.order');
        Route::resource('/menu', 'Admin\MenuController')->only(['index', 'store', 'edit', 'update', 'destroy']);;
        //Route Role
        Route::get('/role/set/{id}', 'Admin\RoleController@set')->name('role.set');
        Route::get('/role/read', 'Admin\RoleController@read')->name('role.read');
        Route::get('/role/select', 'Admin\RoleController@select')->name('role.select');
        Route::resource('/role', 'Admin\RoleController');
        //Route Role Menu
        Route::post('/rolemenu/update', 'Admin\RoleMenuController@update');
        //Route Role Menu
        Route::post('/roledashboard/update', 'Admin\RoleDashboardController@update');
        //Route User
        Route::get('/user/log', 'Admin\UserController@log')->name('user.log');
        Route::get('/user/read', 'Admin\UserController@read')->name('user.read');
        Route::get('/user/readrole', 'Admin\UserController@readrole')->name('user.readrole');
        Route::post('/user/reset', 'Admin\UserController@reset')->name('user.reset');
        Route::post('/user/assignrole', 'Admin\UserController@assignrole')->name('user.assignrole');
        Route::delete('/user/deleterole', 'Admin\UserController@deleterole')->name('user.deleterole');
        Route::resource('/user', 'Admin\UserController');

        //Route Personeel
        Route::get('/personnel/read', 'Admin\PersonnelController@read')->name('personnel.read');
        Route::get('/personnel/info', 'Admin\PersonnelController@info')->name('personnel.info');
        Route::get('/personnel/medis', 'Admin\PersonnelController@medis')->name('personnel.medis');
        Route::get('/personnel/history', 'Admin\PersonnelController@history')->name('personnel.history');
        Route::get('/personnel/temperature', 'Admin\PersonnelController@temperature')->name('personnel.temperature');
        Route::get('/personnel/saturasi', 'Admin\PersonnelController@saturasi')->name('personnel.saturasi');
        Route::get('/personnel/exportmedis', 'Admin\PersonnelController@exportmedis')->name('personnel.exportmedis');
        Route::resource('/personnel', 'Admin\PersonnelController');
        Route::get('/supervisor/read', 'Admin\SupervisorController@read')->name('supervisor.read');
        Route::resource('/supervisor', 'Admin\SupervisorController');

        //Route partner
        Route::get('/partner/read', 'Admin\PartnerController@read')->name('partner.read');
        Route::get('/partner/select', 'Admin\PartnerController@select')->name('partner.select');
        Route::resource('/partner', 'Admin\PartnerController');

        //Route Profile
        Route::get('account/info', 'Admin\AccountController@info')->name('account.info');
        Route::put('account/update/{account}', 'Admin\AccountController@update')->name('account.update');
        //Route Diagnoses Report
        Route::get('/reportdiagnoses', 'Admin\MedicalRecordController@report')->name('reportdiagnoses');
        Route::get('/reportdiagnoses/chart', 'Admin\MedicalRecordController@chart')->name('reportdiagnoses.chart');
        Route::get('/reportdiagnoses/total', 'Admin\MedicalRecordController@total')->name('reportdiagnoses.total');
        Route::get('/reportdiagnoses/lastweek', 'Admin\MedicalRecordController@lastweek')->name('reportdiagnoses.lastweek');
        Route::get('/reportdiagnoses/today', 'Admin\MedicalRecordController@today')->name('reportdiagnoses.today');
        Route::get('/reportdiagnoses/read', 'Admin\MedicalRecordController@reportread')->name('reportdiagnoses.read');
        Route::post('/reportdiagnoses/export', 'Admin\MedicalRecordController@export')->name('reportdiagnoses.export');

        //Route Report Kesehatan
        Route::get('/reporthealth', 'Admin\ReportHealthController@index')->name('reporthealth');
        Route::get('/reporthealth/read', 'Admin\ReportHealthController@read')->name('reporthealth.read');
        Route::post('/reporthealth/export', 'Admin\ReportHealthController@export')->name('reporthealth.export');

        //Route Report Kesehatan Karyawan
        Route::get('/reporthealthemployee', 'Admin\ReportHealthEmployeeController@index')->name('reporthealthemployee');
        Route::get('/reporthealthemployee/read', 'Admin\ReportHealthEmployeeController@read')->name('reporthealthemployee.read');
        Route::post('/reporthealthemployee/export', 'Admin\ReportHealthEmployeeController@export')->name('reporthealthemployee.export');

        //Route Medical Report
        Route::get('/medicalrecord/read', 'Admin\MedicalRecordController@read')->name('medicalrecord.read');
        Route::resource('/medicalrecord', 'Admin\MedicalRecordController');
        //Route Medical Report Personnel
        Route::get('/medicalpersonnel/read', 'Admin\MedicalPersonnelController@read')->name('medicalpersonnel.read');
        Route::resource('/medicalpersonnel', 'Admin\MedicalPersonnelController');
        //Route Cover Letter
        Route::get('/coverletter/chart', 'Admin\CoverLetterController@chart')->name('coverletter.chart');
        Route::get('/coverletter/total', 'Admin\CoverLetterController@total')->name('coverletter.total');
        Route::get('/coverletter/closed', 'Admin\CoverLetterController@closed')->name('coverletter.closed');
        Route::get('/coverletter/request', 'Admin\CoverLetterController@request')->name('coverletter.request');
        Route::get('/coverletter/read', 'Admin\CoverLetterController@read')->name('coverletter.read');
        Route::post('/coverletter/export', 'Admin\CoverLetterController@export')->name('coverletter.export');
        Route::get('/coverletter/{id}/print', 'Admin\CoverLetterController@print')->name('coverletter.print');
        Route::resource('/coverletter', 'Admin\CoverLetterController');

        //Route Diagnosis
        Route::get('/diagnosis/read', 'Admin\DiagnosisController@read')->name('diagnosis.read');
        Route::get('/diagnosis/select', 'Admin\DiagnosisController@select')->name('diagnosis.select');
        Route::get('/diagnosis/import', 'Admin\DiagnosisController@import')->name('diagnosis.import');
        Route::get('/diagnosis/restore/{id}', 'Admin\DiagnosisController@restore')->name('diagnosis.restore');
        Route::post('/diagnosis/preview', 'Admin\DiagnosisController@preview')->name('diagnosis.preview');
        Route::post('/diagnosis/storemass', 'Admin\DiagnosisController@storemass')->name('diagnosis.storemass');
        Route::resource('/diagnosis', 'Admin\DiagnosisController');

        //Route Medical
        Route::get('/medical/read', 'Admin\MedicalController@read')->name('medical.read');
        Route::get('/medical/read', 'Admin\MedicalController@read')->name('medical.read');
        Route::resource('/medical', 'Admin\MedicalController');
        //Route Medical Detail
        Route::get('/medicaldetail/read', 'Admin\MedicalDetailController@read')->name('medicaldetail.read');
        Route::resource('/medicaldetail', 'Admin\MedicalDetailController');
        //Route Inpatient
        Route::get('/inpatient/restore/{id}', 'Admin\InpatientController@restore')->name('inpatient.restore');
        Route::get('/inpatient/read', 'Admin\InpatientController@read')->name('inpatient.read');
        Route::get('/inpatient/select', 'Admin\InpatientController@select')->name('inpatient.select');
        Route::resource('/inpatient', 'Admin\InpatientController');

        //Route Medical Checkup
        Route::get('/checkup/read', 'Admin\CheckupController@read')->name('checkup.read');
        Route::get('/checkup/export', 'Admin\CheckupController@export')->name('checkup.export');
        Route::get('/checkup/import', 'Admin\CheckupController@import')->name('checkup.import');
        Route::get('/checkup/export', 'Admin\CheckupController@export')->name('checkup.export');
        Route::post('/checkup/preview', 'Admin\CheckupController@preview')->name('checkup.preview');
        Route::post('/checkup/storemass', 'Admin\CheckupController@storemass')->name('checkup.storemass');
        Route::resource('/checkup', 'Admin\CheckupController');

        //Medical Action
        Route::get('/medicalaction/read', 'Admin\MedicalActionController@read')->name('medicalaction.read');
        Route::resource('/medicalaction', 'Admin\MedicalActionController');

        //Invoice
        Route::get('/invoice/chart', 'Admin\InvoiceController@chart')->name('invoice.chart');
        Route::get('/invoice/total', 'Admin\InvoiceController@total')->name('invoice.total');
        Route::get('/invoice/closed', 'Admin\InvoiceController@closed')->name('invoice.closed');
        Route::get('/invoice/request', 'Admin\InvoiceController@request')->name('invoice.request');
        Route::get('/invoice/read', 'Admin\InvoiceController@read')->name('invoice.read');
        Route::get('/invoice/coverread', 'Admin\InvoiceController@coverread')->name('invoice.coverread');
        Route::post('/invoice/coverstore', 'Admin\InvoiceController@coverstore')->name('invoice.coverstore');
        Route::delete('/invoice/coverdestroy/{id}', 'Admin\InvoiceController@coverdestroy')->name('invoice.coverdestroy');
        Route::get('/invoice/import/{id}', 'Admin\InvoiceController@import')->name('invoice.import');
        Route::post('/invoice/preview', 'Admin\InvoiceController@preview')->name('invoice.preview');
        Route::post('/invoice/storemass', 'Admin\InvoiceController@storemass')->name('invoice.storemass');
        Route::get('/invoice/itemread', 'Admin\InvoiceController@itemread')->name('invoice.itemread');
        Route::post('/invoice/export', 'Admin\InvoiceController@export')->name('invoice.export');
        Route::resource('/invoice', 'Admin\InvoiceController');

        //Route Status Report
        Route::get('/reportstatus', 'Admin\ReportStatusController@index')->name('reportstatus');
        Route::get('/reportstatus/chart', 'Admin\ReportStatusController@chart')->name('reportstatus.chart');
        Route::get('/reportstatus/total', 'Admin\ReportStatusController@total')->name('reportstatus.total');
        Route::get('/reportstatus/lastweek', 'Admin\ReportStatusController@lastweek')->name('reportstatus.lastweek');
        Route::get('/reportstatus/today', 'Admin\ReportStatusController@today')->name('reportstatus.today');
        Route::get('/reportstatus/read', 'Admin\ReportStatusController@read')->name('reportstatus.read');
        Route::post('/reportstatus/export', 'Admin\ReportStatusController@export')->name('reportstatus.export');

        //Template
        Route::get('/template/read', 'Admin\TemplateController@read')->name('template.read');
        Route::resource('/template', 'Admin\TemplateController');

        //Document
        Route::get('/document/read', 'Admin\DocumentController@read')->name('document.read');
        Route::resource('/document', 'Admin\DocumentController');

        // Medicine
        Route::get('/medicine/read', 'Admin\MedicineController@read')->name('medicine.read');
        Route::get('/medicine/select', 'Admin\MedicineController@select')->name('medicine.select');
        Route::get('/medicine/import', 'Admin\MedicineController@import')->name('medicine.import');
        Route::post('/medicine/restore', 'Admin\MedicineController@restore')->name('medicine.restore');
        Route::post('/medicine/delete', 'Admin\MedicineController@delete')->name('medicine.delete');
        Route::post('/medicine/preview', 'Admin\MedicineController@preview')->name('medicine.preview');
        Route::post('/medicine/storemass', 'Admin\MedicineController@storemass')->name('medicine.storemass');
        Route::resource('/medicine', 'Admin\MedicineController');

        //Laporan Pengisian
        Route::get('/reportdaily', 'Admin\ReportDailyController@index')->name('reportdaily');
        Route::get('/reportdaily/personnel', 'Admin\ReportDailyController@personnel')->name('reportdaily.personnel');
        Route::get('/reportdaily/supervisor', 'Admin\ReportDailyController@supervisor')->name('reportdaily.supervisor');
        Route::get('/reportdaily/chartpersonnel', 'Admin\ReportDailyController@chartpersonnel')->name('reportdaily.chartpersonnel');
        Route::get('/reportdaily/totalpersonnel', 'Admin\ReportDailyController@totalpersonnel')->name('reportdaily.totalpersonnel');
        Route::get('/reportdaily/lastweekpersonnel', 'Admin\ReportDailyController@lastweekpersonnel')->name('reportdaily.lastweekpersonnel');
        Route::get('/reportdaily/todaypersonnel', 'Admin\ReportDailyController@todaypersonnel')->name('reportdaily.todaypersonnel');
        Route::get('/reportdaily/chartsupervisor', 'Admin\ReportDailyController@chartsupervisor')->name('reportdaily.chartsupervisor');
        Route::get('/reportdaily/totalsupervisor', 'Admin\ReportDailyController@totalsupervisor')->name('reportdaily.totalsupervisor');
        Route::get('/reportdaily/lastweeksupervisor', 'Admin\ReportDailyController@lastweeksupervisor')->name('reportdaily.lastweeksupervisor');
        Route::get('/reportdaily/todaysupervisor', 'Admin\ReportDailyController@todaysupervisor')->name('reportdaily.todaysupervisor');
        Route::post('/reportdaily/export', 'Admin\ReportDailyController@export')->name('reportdaily.export');
        Route::post('/reportdaily/exportsuper', 'Admin\ReportDailyController@exportsuper')->name('reportdailysuper.export');

        //Guide
        Route::get('/guide/read', 'Admin\GuideController@read')->name('guide.read');
        Route::get('/guide/list', 'Admin\GuideController@list')->name('guide.list');
        Route::resource('/guide', 'Admin\GuideController');

        // Doctor
        Route::get('/doctor/read', 'Admin\DoctorController@read')->name('doctor.read');
        Route::get('/doctor/select', 'Admin\DoctorController@select')->name('doctor.select');
        Route::get('/doctor/import', 'Admin\DoctorController@import')->name('doctor.import');
        Route::post('/doctor/preview', 'Admin\DoctorController@preview')->name('doctor.preview');
        Route::post('/doctor/storemass', 'Admin\DoctorController@storemass')->name('doctor.storemass');
        Route::resource('/doctor', 'Admin\DoctorController');

        // Medicine Category
        Route::get('/medicinecategory/read', 'Admin\MedicineCategoryController@read')->name('medicinecategory.read');
        Route::get('/medicinecategory/select', 'Admin\MedicineCategoryController@select')->name('medicinecategory.select');
        Route::post('/medicinecategory/restore', 'Admin\MedicineCategoryController@restore')->name('medicinecategory.restore');
        Route::post('/medicinecategory/delete', 'Admin\MedicineCategoryController@delete')->name('medicinecategory.delete');
        Route::resource('/medicinecategory', 'Admin\MedicineCategoryController');

        // Medicine Group
        Route::get('/medicinegroup/read', 'Admin\MedicineGroupController@read')->name('medicinegroup.read');
        Route::get('/medicinegroup/select', 'Admin\MedicineGroupController@select')->name('medicinegroup.select');
        Route::get('/medicinegroup/restore/{id}', 'Admin\MedicineGroupController@restore')->name('medicinegroup.restore');
        Route::get('/medicinegroup/delete/{id}', 'Admin\MedicineGroupController@delete')->name('medicinegroup.delete');
        Route::resource('/medicinegroup', 'Admin\MedicineGroupController');

        // Medicine Unit
        Route::get('/medicineunit/read', 'Admin\MedicineUnitController@read')->name('medicineunit.read');
        Route::get('/medicineunit/select', 'Admin\MedicineUnitController@select')->name('medicineunit.select');
        Route::get('/medicineunit/restore/{id}', 'Admin\MedicineUnitController@restore')->name('medicineunit.restore');
        Route::get('/medicineunit/delete/{id}', 'Admin\MedicineUnitController@delete')->name('medicineunit.delete');
        Route::resource('/medicineunit', 'Admin\MedicineUnitController');
        
        // Medicine Type
        Route::get('/medicinetype/read', 'Admin\MedicineTypeController@read')->name('medicinetype.read');
        Route::get('/medicinetype/select', 'Admin\MedicineTypeController@select')->name('medicinetype.select');
        Route::get('/medicinetype/restore/{id}', 'Admin\MedicineTypeController@restore')->name('medicinetype.restore');
        Route::get('/medicinetype/delete/{id}', 'Admin\MedicineTypeController@delete')->name('medicinetype.delete');
        Route::resource('/medicinetype', 'Admin\MedicineTypeController');

        // Using Rule
        Route::get('/usingrule/read', 'Admin\UsingRuleController@read')->name('usingrule.read');
        Route::get('/usingrule/select', 'Admin\UsingRuleController@select')->name('usingrule.select');
        Route::get('/usingrule/restore/{id}', 'Admin\UsingRuleController@restore')->name('usingrule.restore');
        Route::get('/usingrule/delete/{id}', 'Admin\UsingRuleController@delete')->name('usingrule.delete');
        Route::resource('/usingrule', 'Admin\UsingRuleController');
        
        // Partner Category
        Route::get('/partnercategory/read', 'Admin\PartnerCategoryController@read')->name('partnercategory.read');
        Route::get('/partnercategory/select', 'Admin\PartnerCategoryController@select')->name('partnercategory.select');
        Route::get('/partnercategory/restore/{id}', 'Admin\PartnerCategoryController@restore')->name('partnercategory.restore');
        Route::get('/partnercategory/delete/{id}', 'Admin\PartnerCategoryController@delete')->name('partnercategory.delete');
        Route::resource('/partnercategory', 'Admin\PartnerCategoryController');
        
        // Speciality
        Route::get('/speciality/read', 'Admin\SpecialityController@read')->name('speciality.read');
        Route::get('/speciality/select', 'Admin\SpecialityController@select')->name('speciality.select');
        Route::get('/speciality/restore/{id}', 'Admin\SpecialityController@restore')->name('speciality.restore');
        Route::get('/speciality/delete/{id}', 'Admin\SpecialityController@delete')->name('speciality.delete');
        Route::resource('/speciality', 'Admin\SpecialityController');

        // Examination
        Route::get('/examination/read', 'Admin\ExaminationController@read')->name('examination.read');
        Route::get('/examination/select', 'Admin\ExaminationController@select')->name('examination.select');
        Route::get('/examination/restore/{id}', 'Admin\ExaminationController@restore')->name('examination.restore');
        Route::get('/examination/delete/{id}', 'Admin\ExaminationController@delete')->name('examination.delete');
        Route::resource('/examination', 'Admin\ExaminationController');

        // Examination Type
        Route::get('/examinationtype/read', 'Admin\ExaminationTypeController@read')->name('examinationtype.read');
        Route::get('/examinationtype/select', 'Admin\ExaminationTypeController@select')->name('examinationtype.select');
        Route::get('/examinationtype/restore/{id}', 'Admin\ExaminationTypeController@restore')->name('examinationtype.restore');
        Route::get('/examinationtype/delete/{id}', 'Admin\ExaminationTypeController@delete')->name('examinationtype.delete');
        Route::resource('/examinationtype', 'Admin\ExaminationTypeController');

        // Examination Evaluation
        Route::get('/examinationevaluation/read', 'Admin\ExaminationEvaluationController@read')->name('examinationevaluation.read');
        Route::get('/examinationevaluation/select', 'Admin\ExaminationEvaluationController@select')->name('examinationevaluation.select');
        Route::get('/examinationevaluation/restore/{id}', 'Admin\ExaminationEvaluationController@restore')->name('examinationevaluation.restore');
        Route::get('/examinationevaluation/delete/{id}', 'Admin\ExaminationEvaluationController@delete')->name('examinationevaluation.delete');
        Route::resource('/examinationevaluation', 'Admin\ExaminationEvaluationController');
        
        // Examination Evaluation Level
        Route::get('/evaluationlevel/read', 'Admin\ExaminationEvaluationLevelController@read')->name('evaluationlevel.read');
        Route::get('/evaluationlevel/select', 'Admin\ExaminationEvaluationLevelController@select')->name('evaluationlevel.select');
        Route::get('/evaluationlevel/restore/{id}', 'Admin\ExaminationEvaluationLevelController@restore')->name('evaluationlevel.restore');
        Route::get('/evaluationlevel/delete/{id}', 'Admin\ExaminationEvaluationLevelController@delete')->name('evaluationlevel.delete');
        Route::resource('/evaluationlevel', 'Admin\ExaminationEvaluationLevelController');

        // Diagnoses Category
        Route::get('/diagnosescategory/read', 'Admin\DiagnosesCategoryController@read')->name('diagnosescategory.read');
        Route::get('/diagnosescategory/select', 'Admin\DiagnosesCategoryController@select')->name('diagnosescategory.select');
        Route::get('/diagnosescategory/restore/{id}', 'Admin\DiagnosesCategoryController@restore')->name('diagnosescategory.restore');
        Route::get('/diagnosescategory/delete/{id}', 'Admin\DiagnosesCategoryController@delete')->name('diagnosescategory.delete');
        Route::resource('/diagnosescategory', 'Admin\DiagnosesCategoryController');

        // Workforce Group
        Route::get('/workforcegroup/read', 'Admin\WorkforceGroupController@read')->name('workforcegroup.read');
        Route::get('/workforcegroup/select', 'Admin\WorkforceGroupController@select')->name('workforcegroup.select');
        Route::get('/workforcegroup/restore/{id}', 'Admin\WorkforceGroupController@restore')->name('workforcegroup.restore');
        Route::get('/workforcegroup/delete/{id}', 'Admin\WorkforceGroupController@delete')->name('workforcegroup.delete');
        Route::resource('/workforcegroup', 'Admin\WorkforceGroupController');

        // Assessment Question
        Route::get('/assessmentquestion/read', 'Admin\AssessmentQuestionController@read')->name('assessmentquestion.read');
        Route::get('/assessmentquestion/select', 'Admin\AssessmentQuestionController@select')->name('assessmentquestion.select');
        Route::post('/assessmentquestion/restore', 'Admin\AssessmentQuestionController@restore')->name('assessmentquestion.restore');
        Route::post('/assessmentquestion/delete', 'Admin\AssessmentQuestionController@delete')->name('assessmentquestion.delete');
        Route::resource('/assessmentquestion', 'Admin\AssessmentQuestionController');

        // Assessment Answer
        Route::get('/assessmentanswer/read', 'Admin\AssessmentAnswerController@read')->name('assessmentanswer.read');
        Route::get('/assessmentanswer/select', 'Admin\AssessmentAnswerController@select')->name('assessmentanswer.select');
        Route::post('/assessmentanswer/restore', 'Admin\AssessmentAnswerController@restore')->name('assessmentanswer.restore');
        Route::post('/assessmentanswer/delete', 'Admin\AssessmentAnswerController@delete')->name('assessmentanswer.delete');
        Route::resource('/assessmentanswer', 'Admin\AssessmentAnswerController');

        // Attendance Description
        Route::get('/attendance/read', 'Admin\AttendanceDescriptionController@read')->name('attendance.read');
        Route::get('/attendance/select', 'Admin\AttendanceDescriptionController@select')->name('attendance.select');
        Route::post('/attendance/restore', 'Admin\AttendanceDescriptionController@restore')->name('attendance.restore');
        Route::post('/attendance/delete', 'Admin\AttendanceDescriptionController@delete')->name('attendance.delete');
        Route::resource('/attendance', 'Admin\AttendanceDescriptionController');

        // Assessment
        Route::get('/assessment/read', 'Admin\AssessmentController@read')->name('assessment.read');
        Route::get('/assessment/select', 'Admin\AssessmentController@select')->name('assessment.select');
        Route::post('/assessment/restore', 'Admin\AssessmentController@restore')->name('assessment.restore');
        Route::post('/assessment/delete', 'Admin\AssessmentController@delete')->name('assessment.delete');
        Route::resource('/assessment', 'Admin\AssessmentController');
    });
});
Route::group(['prefix' => '{site}', 'middleware' => 'page.site'], function () {
    Route::get('/', 'Auth\SiteLoginController@index')->name('site.login');
    Route::post('/login', 'Auth\SiteLoginController@login')->name('site.login.post');
    Route::post('/logout', 'Auth\SiteLoginController@logout')->name('site.logout');
    Route::group(['middleware' => 'auth:site'], function () {
        Route::get('/dashboard', 'Site\DashboardController@index')->name('site.dashboard.index');
        Route::get('/role/set/{id}', 'Site\RoleController@set')->name('site.role.set');
    });
});