<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyPreEmploymentController;
use App\Http\Controllers\CompanyAppointmentController;
use App\Http\Controllers\CompanyAccountInvitationController;
use App\Http\Controllers\InventoryController;

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RadtechController;
use App\Http\Controllers\PleboController;
use App\Http\Controllers\RadiologistController;
use App\Http\Controllers\PathologistController;
use App\Http\Controllers\EcgtechController;
use App\Http\Controllers\OpdController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// OPD Registration Route
Route::get('/register/opd', function () {
    return redirect()->route('register', ['opd' => 1]);
})->name('register.opd');

// Debug route (temporary)
Route::get('/debug/examinations', function () {
    $examinations = \App\Models\PreEmploymentExamination::with('preEmploymentRecord')->get();
    $records = \App\Models\PreEmploymentRecord::where('status', 'approved')->get();
    
    return response()->json([
        'examinations_count' => $examinations->count(),
        'records_count' => $records->count(),
        'examinations' => $examinations->map(function($exam) {
            return [
                'id' => $exam->id,
                'status' => $exam->status,
                'name' => $exam->name,
                'record_id' => $exam->pre_employment_record_id,
                'record_name' => $exam->preEmploymentRecord ? $exam->preEmploymentRecord->full_name : null,
                'created_at' => $exam->created_at
            ];
        }),
        'records' => $records->map(function($record) {
            return [
                'id' => $record->id,
                'name' => $record->full_name,
                'company' => $record->company_name,
                'status' => $record->status,
                'has_examination' => $record->preEmploymentExamination ? true : false
            ];
        })
    ]);
});

// Role-based Dashboard Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/patients', [AdminController::class, 'patients'])->name('admin.patients');
    Route::get('/admin/appointments', [AdminController::class, 'appointments'])->name('admin.appointments');
    Route::get('/admin/appointments/{id}/details', [AdminController::class, 'appointmentDetails'])->name('admin.appointments.details');
    Route::get('/admin/pre-employment', [AdminController::class, 'preEmployment'])->name('admin.pre-employment');
    Route::get('/admin/pre-employment/{id}/details', [AdminController::class, 'preEmploymentDetails'])->name('admin.pre-employment.details');
    Route::get('/admin/tests', [AdminController::class, 'tests'])->name('admin.tests');
    
    // Medical results view routes
    Route::get('/admin/view-pre-employment-results/{id}', [AdminController::class, 'viewPreEmploymentResults'])->name('admin.view-pre-employment-results');
    Route::get('/admin/view-annual-physical-results/{id}', [AdminController::class, 'viewAnnualPhysicalResults'])->name('admin.view-annual-physical-results');
    Route::post('/admin/examinations/pre-employment/{id}/send', [AdminController::class, 'sendPreEmploymentToCompany'])->name('admin.examinations.pre-employment.send');
    Route::post('/admin/examinations/annual-physical/{id}/send', [AdminController::class, 'sendAnnualPhysicalToCompany'])->name('admin.examinations.annual-physical.send');
    
    // Examination view and send routes
    Route::get('/admin/view-pre-employment-examination/{id}', [AdminController::class, 'viewPreEmploymentExamination'])->name('admin.view-pre-employment-examination');
    Route::get('/admin/view-annual-physical-examination/{id}', [AdminController::class, 'viewAnnualPhysicalExamination'])->name('admin.view-annual-physical-examination');
    Route::post('/admin/send-pre-employment-examination/{id}', [AdminController::class, 'sendPreEmploymentExamination'])->name('admin.send-pre-employment-examination');
    Route::post('/admin/send-annual-physical-examination/{id}', [AdminController::class, 'sendAnnualPhysicalExamination'])->name('admin.send-annual-physical-examination');
    
    // Billing routes for examinations
    Route::get('/admin/examinations/pre-employment/{id}/billing', [AdminController::class, 'getPreEmploymentBilling'])->name('admin.examinations.pre-employment.billing');
    Route::get('/admin/examinations/annual-physical/{id}/billing', [AdminController::class, 'getAnnualPhysicalBilling'])->name('admin.examinations.annual-physical.billing');
    Route::post('/admin/examinations/pre-employment/{id}/send', [AdminController::class, 'sendPreEmploymentExaminationWithBilling'])->name('admin.examinations.pre-employment.send');
    Route::post('/admin/examinations/annual-physical/{id}/send', [AdminController::class, 'sendAnnualPhysicalExaminationWithBilling'])->name('admin.examinations.annual-physical.send');
    
    Route::get('/admin/messages', [AdminController::class, 'messages'])->name('admin.messages');
    Route::get('/admin/messages/fetch', [AdminController::class, 'fetchMessages']);
    Route::post('/admin/messages/send', [AdminController::class, 'sendMessage']);
    Route::post('/admin/messages/mark-read', [AdminController::class, 'markAsRead']);
    Route::get('/admin/messages/count', [AdminController::class, 'getUnreadMessageCount']);
    Route::get('/admin/chat-users', [AdminController::class, 'chatUsers']);
    
    // Admin Notifications Routes
    Route::get('/admin/notifications', [App\Http\Controllers\AdminNotificationController::class, 'index'])->name('admin.notifications');
    Route::get('/admin/notifications/count', [App\Http\Controllers\AdminNotificationController::class, 'getCount']);
    Route::get('/admin/notifications/recent', [App\Http\Controllers\AdminNotificationController::class, 'getRecent']);
    Route::post('/admin/notifications/{id}/mark-read', [App\Http\Controllers\AdminNotificationController::class, 'markAsRead']);
    Route::post('/admin/notifications/mark-all-read', [App\Http\Controllers\AdminNotificationController::class, 'markAllAsRead']);
    Route::delete('/admin/notifications/{id}', [App\Http\Controllers\AdminNotificationController::class, 'destroy']);
    
    Route::get('/admin/report', [AdminController::class, 'report'])->name('admin.report');
    
    
    Route::post('admin/appointments/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveAppointment'])->name('admin.appointments.approve');
    Route::post('admin/appointments/{id}/decline', [App\Http\Controllers\AdminController::class, 'declineAppointment'])->name('admin.appointments.decline');
    Route::post('admin/pre-employment/{id}/approve', [App\Http\Controllers\AdminController::class, 'approvePreEmployment'])->name('admin.pre-employment.approve');
    Route::post('admin/pre-employment/{id}/decline', [App\Http\Controllers\AdminController::class, 'declinePreEmployment'])->name('admin.pre-employment.decline');
    
    // Bulk Operations for Pre-Employment
    Route::post('admin/pre-employment/bulk-approve', [App\Http\Controllers\AdminController::class, 'bulkApprovePreEmployment'])->name('admin.pre-employment.bulk-approve');
    Route::post('admin/pre-employment/bulk-decline', [App\Http\Controllers\AdminController::class, 'bulkDeclinePreEmployment'])->name('admin.pre-employment.bulk-decline');
    Route::post('admin/pre-employment/bulk-send-links', [App\Http\Controllers\AdminController::class, 'bulkSendRegistrationLinks'])->name('admin.pre-employment.bulk-send-links');
    Route::post('admin/pre-employment/bulk-delete', [App\Http\Controllers\AdminController::class, 'bulkDeletePreEmployment'])->name('admin.pre-employment.bulk-delete');
    
    // Test Assignment Routes
    Route::get('/admin/test-assignments', [AdminController::class, 'testAssignments'])->name('admin.test-assignments');
    Route::get('/admin/test-assignments/{id}', [AdminController::class, 'showTestAssignment'])->name('admin.test-assignments.show');
    Route::post('/admin/test-assignments/{id}/update-status', [AdminController::class, 'updateTestAssignmentStatus'])->name('admin.test-assignments.update-status');
Route::post('admin/pre-employment/{id}/send-email', [App\Http\Controllers\AdminController::class, 'sendRegistrationEmail'])->name('admin.pre-employment.send-email');
    Route::post('admin/pre-employment/send-all-emails', [AdminController::class, 'sendAllRegistrationEmails'])->name('admin.pre-employment.send-all-emails');
    Route::get('/admin/accounts-and-patients', [AdminController::class, 'companyAccountsAndPatients'])->name('admin.accounts-and-patients');
    Route::delete('/admin/company/{id}', [AdminController::class, 'deleteCompany'])->name('admin.delete-company');
    
    // Admin - Medical Staff Management
    Route::get('/admin/medical-staff', [AdminController::class, 'medicalStaff'])->name('admin.medical-staff');
    Route::post('/admin/medical-staff', [AdminController::class, 'storeMedicalStaff'])->name('admin.medical-staff.store');
    Route::put('/admin/medical-staff/{id}', [AdminController::class, 'updateMedicalStaff'])->name('admin.medical-staff.update');
    Route::delete('/admin/medical-staff/{id}', [AdminController::class, 'destroyMedicalStaff'])->name('admin.medical-staff.destroy');
    
    // Medical Test Categories Routes
    Route::prefix('admin/medical-test-categories')->name('admin.medical-test-categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MedicalTestCategoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\MedicalTestCategoryController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\MedicalTestCategoryController::class, 'store'])->name('store');
        Route::get('/{medical_test_category}', [\App\Http\Controllers\Admin\MedicalTestCategoryController::class, 'show'])->name('show');
        Route::get('/{medical_test_category}/edit', [\App\Http\Controllers\Admin\MedicalTestCategoryController::class, 'edit'])->name('edit');
        Route::put('/{medical_test_category}', [\App\Http\Controllers\Admin\MedicalTestCategoryController::class, 'update'])->name('update');
        Route::delete('/{medical_test_category}', [\App\Http\Controllers\Admin\MedicalTestCategoryController::class, 'destroy'])->name('destroy');
    });
    Route::resource('admin/medical-tests', App\Http\Controllers\Admin\MedicalTestController::class)->except(['index']);
    
    // Inventory Management Routes
    Route::resource('admin/inventory', InventoryController::class, ['as' => 'admin']);

    // Page Content Management Routes
    Route::get('/admin/page-contents', [App\Http\Controllers\Admin\PageContentController::class, 'index'])->name('admin.page-contents.index');
    Route::get('/admin/page-contents/create', [App\Http\Controllers\Admin\PageContentController::class, 'create'])->name('admin.page-contents.create');
    Route::post('/admin/page-contents/actions/bulk-update', [App\Http\Controllers\Admin\PageContentController::class, 'bulkUpdate'])->name('admin.page-contents.bulk-update');
    Route::post('/admin/page-contents/actions/initialize-defaults', [App\Http\Controllers\Admin\PageContentController::class, 'initializeDefaults'])->name('admin.page-contents.initialize-defaults');
    Route::post('/admin/page-contents/actions/add-service-card', [App\Http\Controllers\Admin\PageContentController::class, 'addServiceCard'])->name('admin.page-contents.add-service-card');
    Route::post('/admin/page-contents', [App\Http\Controllers\Admin\PageContentController::class, 'store'])->name('admin.page-contents.store');
    Route::get('/admin/page-contents/{pageContent}/edit', [App\Http\Controllers\Admin\PageContentController::class, 'edit'])->name('admin.page-contents.edit');
    Route::put('/admin/page-contents/{pageContent}', [App\Http\Controllers\Admin\PageContentController::class, 'update'])->name('admin.page-contents.update');
    Route::delete('/admin/page-contents/{pageContent}', [App\Http\Controllers\Admin\PageContentController::class, 'destroy'])->name('admin.page-contents.destroy');

    // Admin OPD entries
    Route::get('/admin/opd', [AdminController::class, 'opd'])->name('admin.opd');
    Route::post('/admin/opd/{id}/approve', [AdminController::class, 'approveOpd'])->name('admin.opd.approve');
    Route::post('/admin/opd/{id}/decline', [AdminController::class, 'declineOpd'])->name('admin.opd.decline');
    Route::post('/admin/opd/{id}/done', [AdminController::class, 'markOpdDone'])->name('admin.opd.mark-done');
    Route::post('/admin/opd/{id}/send-results', [AdminController::class, 'sendOpdResults'])->name('admin.opd.send-results');
    
    // Company Account Management Routes
    Route::post('/admin/company-accounts/{id}/approve', [AdminController::class, 'approveCompanyAccount'])->name('admin.company-accounts.approve');
    Route::post('/admin/company-accounts/{id}/reject', [AdminController::class, 'rejectCompanyAccount'])->name('admin.company-accounts.reject');
    Route::put('/admin/company-accounts/{id}/update', [AdminController::class, 'updateCompanyAccount'])->name('admin.company-accounts.update');
    Route::delete('/admin/company-accounts/{id}', [AdminController::class, 'deleteCompany'])->name('admin.company-accounts.delete');
});

Route::middleware(['auth', 'role:company'])->group(function () {
    Route::get('/company/dashboard', [CompanyController::class, 'dashboard'])->name('company.dashboard');
    
    // Settings Routes
    Route::get('/company/settings', [CompanyController::class, 'settings'])->name('company.settings');
    Route::put('/company/settings', [CompanyController::class, 'updateSettings'])->name('company.settings.update');
    
    // Medical Results Routes
    Route::get('/company/medical-results', [CompanyController::class, 'medicalResults'])->name('company.medical-results');
    
    // Sent Results View Routes
    Route::get('/company/view-sent-pre-employment/{id}', [CompanyController::class, 'viewSentPreEmployment'])->name('company.view-sent-pre-employment');
    Route::get('/company/view-sent-annual-physical/{id}', [CompanyController::class, 'viewSentAnnualPhysical'])->name('company.view-sent-annual-physical');
    Route::get('/company/download-sent-pre-employment/{id}', [CompanyController::class, 'downloadSentPreEmployment'])->name('company.download-sent-pre-employment');
    Route::get('/company/download-sent-annual-physical/{id}', [CompanyController::class, 'downloadSentAnnualPhysical'])->name('company.download-sent-annual-physical');
    
    // Pre-Employment Routes
    Route::get('/company/pre-employment', [CompanyPreEmploymentController::class, 'index'])->name('company.pre-employment.index');
    Route::get('/company/pre-employment/create', [CompanyPreEmploymentController::class, 'create'])->name('company.pre-employment.create');
    Route::post('/company/pre-employment', [CompanyPreEmploymentController::class, 'store'])->name('company.pre-employment.store');
    Route::get('/company/pre-employment/{id}', [CompanyPreEmploymentController::class, 'show'])->name('company.pre-employment.show');
    
    // Appointment Routes
    Route::get('/company/appointments', [CompanyAppointmentController::class, 'index'])->name('company.appointments.index');
    Route::get('/company/appointments/create', [CompanyAppointmentController::class, 'create'])->name('company.appointments.create');
    Route::post('/company/appointments', [CompanyAppointmentController::class, 'store'])->name('company.appointments.store');
    Route::get('/company/appointments/{appointment}', [CompanyAppointmentController::class, 'show'])->name('company.appointments.show');
    Route::get('/company/appointments/{appointment}/edit', [CompanyAppointmentController::class, 'edit'])->name('company.appointments.edit');
    Route::put('/company/appointments/{appointment}', [CompanyAppointmentController::class, 'update'])->name('company.appointments.update');
    Route::delete('/company/appointments/{appointment}', [CompanyAppointmentController::class, 'destroy'])->name('company.appointments.destroy');
    Route::get('/company/appointments/events', [CompanyAppointmentController::class, 'events'])->name('company.appointments.events');
    
    // Account Invitation Routes
    Route::get('/company/account-invitations', [CompanyAccountInvitationController::class, 'index'])->name('company.account-invitations.index');
    Route::get('/company/account-invitations/create', [CompanyAccountInvitationController::class, 'create'])->name('company.account-invitations.create');
    Route::post('/company/account-invitations', [CompanyAccountInvitationController::class, 'store'])->name('company.account-invitations.store');
    Route::delete('/company/account-invitations/{invitation}', [CompanyAccountInvitationController::class, 'destroy'])->name('company.account-invitations.destroy');
    
    // Company Chat Routes
    Route::get('/company/messages', [CompanyController::class, 'messages'])->name('company.messages');
    Route::get('/company/messages/fetch', [CompanyController::class, 'fetchMessages']);
    Route::post('/company/messages/send', [CompanyController::class, 'sendMessage']);
    Route::post('/company/messages/mark-read', [CompanyController::class, 'markAsRead']);
    Route::get('/company/messages/count', [CompanyController::class, 'getUnreadMessageCount']);
    Route::get('/company/chat-users', [CompanyController::class, 'chatUsers']);
});





// Debug route to test authentication
Route::get('/debug-auth', function() {
    if (Auth::check()) {
        $user = Auth::user();
        return response()->json([
            'authenticated' => true,
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'has_company_role' => $user->hasRole('company')
        ]);
    } else {
        return response()->json([
            'authenticated' => false
        ]);
    }
});

Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::get('/doctor/pre-employment', [DoctorController::class, 'preEmployment'])->name('doctor.pre-employment');
    Route::get('/doctor/pre-employment/{id}/edit', [DoctorController::class, 'editPreEmployment'])->name('doctor.pre-employment.edit');
    Route::patch('/doctor/pre-employment/{id}', [DoctorController::class, 'updatePreEmployment'])->name('doctor.pre-employment.update');
    Route::get('/doctor/annual-physical', [DoctorController::class, 'annualPhysical'])->name('doctor.annual-physical');
    Route::get('/doctor/annual-physical/{id}/edit', [DoctorController::class, 'editAnnualPhysical'])->name('doctor.annual-physical.edit');
    Route::patch('/doctor/annual-physical/{id}', [DoctorController::class, 'updateAnnualPhysical'])->name('doctor.annual-physical.update');
    Route::get('/doctor/pre-employment/{id}/examination', [DoctorController::class, 'showExamination'])->name('doctor.pre-employment.examination.show');
    Route::post('/doctor/pre-employment/{record}/submit', [DoctorController::class, 'submitPreEmploymentByRecordId'])->name('doctor.pre-employment.by-record.submit');
    Route::get('/doctor/annual-physical/{id}/examination', [DoctorController::class, 'showAnnualPhysicalExamination'])->name('doctor.annual-physical.examination.show');
    Route::get('/doctor/annual-physical/patient/{patientId}/edit', [DoctorController::class, 'editAnnualPhysicalByPatientId'])->name('doctor.annual-physical.by-patient.edit');
    Route::post('/doctor/annual-physical/patient/{patientId}/submit', [DoctorController::class, 'submitAnnualPhysicalByPatientId'])->name('doctor.annual-physical.by-patient.submit');
    
    // Medical Checklist Routes
    Route::get('/doctor/medical-checklist/pre-employment/{recordId}', [DoctorController::class, 'showMedicalChecklistPreEmployment'])->name('doctor.medical-checklist.pre-employment');
    Route::get('/doctor/medical-checklist/annual-physical/{patientId}', [DoctorController::class, 'showMedicalChecklistAnnualPhysical'])->name('doctor.medical-checklist.annual-physical');
    Route::post('/doctor/medical-checklist', [DoctorController::class, 'storeMedicalChecklist'])->name('doctor.medical-checklist.store');
    Route::patch('/doctor/medical-checklist/{id}', [DoctorController::class, 'updateMedicalChecklist'])->name('doctor.medical-checklist.update');
    
    // Medical Test Management Routes
    Route::get('/doctor/medical-test-categories', [DoctorController::class, 'medicalTestCategories'])->name('medical-test-categories.index');
    Route::get('/doctor/medical-test-categories/{id}', [DoctorController::class, 'showMedicalTestCategory'])->name('medical-test-categories.show');
    Route::get('/doctor/medical-tests', [DoctorController::class, 'medicalTests'])->name('medical-tests.index');
    Route::get('/doctor/medical-tests/{id}/edit', [DoctorController::class, 'editMedicalTest'])->name('medical-tests.edit');
    Route::patch('/doctor/medical-tests/{id}', [DoctorController::class, 'updateMedicalTest'])->name('medical-tests.update');
    
    // Doctor Chat Routes
    Route::get('/doctor/messages', [DoctorController::class, 'messages'])->name('doctor.messages');
    Route::get('/doctor/messages/fetch', [DoctorController::class, 'fetchMessages']);
    Route::post('/doctor/messages/send', [DoctorController::class, 'sendMessage']);
    Route::post('/doctor/messages/mark-read', [DoctorController::class, 'markAsRead']);
    Route::get('/doctor/messages/count', [DoctorController::class, 'getUnreadMessageCount']);
    Route::get('/doctor/chat-users', [DoctorController::class, 'chatUsers']);
});

Route::middleware(['auth', 'role:opd'])->group(function () {
    Route::get('/opd/dashboard', [OpdController::class, 'dashboard'])->name('opd.dashboard');
    Route::get('/opd/medical-test-categories', [OpdController::class, 'medicalTestCategories'])->name('opd.medical-test-categories');
    Route::get('/opd/medical-tests', [OpdController::class, 'medicalTests'])->name('opd.medical-tests');
    Route::get('/opd/incoming-tests', [OpdController::class, 'incomingTests'])->name('opd.incoming-tests');
    Route::post('/opd/incoming-tests/add/{testId}', [OpdController::class, 'addIncomingTest'])->name('opd.incoming-tests.add');
    Route::post('/opd/incoming-tests/remove/{testId}', [OpdController::class, 'removeIncomingTest'])->name('opd.incoming-tests.remove');
    // OPD Result preview (UI only)
    Route::get('/opd/result', [OpdController::class, 'result'])->name('opd.result');
    
    // OPD Account Creation Routes
    Route::get('/opd/create-account', [OpdController::class, 'showCreateAccount'])->name('opd.create-account');
    Route::post('/opd/create-account', [OpdController::class, 'createAccount'])->name('opd.create-account.store');
});

// Debug route to check users (temporary)
Route::get('/debug-users', function() {
    $users = \App\Models\User::select('id', 'fname', 'lname', 'role', 'company')->get();
    return response()->json($users);
});

// Debug route to check patients for an appointment
Route::get('/debug-patients/{appointmentId}', function($appointmentId) {
    $appointment = App\Models\Appointment::with('patients')->find($appointmentId);
    if (!$appointment) {
        return 'Appointment not found';
    }
    
    return response()->json([
        'appointment_id' => $appointment->id,
        'patients_count' => $appointment->patients->count(),
        'patients' => $appointment->patients->map(function($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->first_name . ' ' . $patient->last_name,
                'email' => $patient->email,
                'appointment_id' => $patient->appointment_id
            ];
        }),
        'patients_data' => $appointment->patients_data
    ]);
});

Route::middleware(['auth', 'role:nurse'])->group(function () {
    Route::get('/nurse/dashboard', [NurseController::class, 'dashboard'])->name('nurse.dashboard');
    Route::get('/nurse/appointments', [NurseController::class, 'appointments'])->name('nurse.appointments');
    Route::get('/nurse/pre-employment', [NurseController::class, 'preEmployment'])->name('nurse.pre-employment');
    Route::get('/nurse/pre-employment/create', [NurseController::class, 'createPreEmployment'])->name('nurse.pre-employment.create');
    Route::post('/nurse/pre-employment', [NurseController::class, 'storePreEmployment'])->name('nurse.pre-employment.store');
    Route::get('/nurse/annual-physical', [NurseController::class, 'annualPhysical'])->name('nurse.annual-physical');
    Route::get('/nurse/annual-physical/create', [NurseController::class, 'createAnnualPhysical'])->name('nurse.annual-physical.create');
    Route::post('/nurse/annual-physical', [NurseController::class, 'storeAnnualPhysical'])->name('nurse.annual-physical.store');
    
    // Nurse Pre-Employment Edit Routes
    Route::get('/nurse/pre-employment/{id}/edit', [NurseController::class, 'editPreEmployment'])->name('nurse.pre-employment.edit');
    Route::patch('/nurse/pre-employment/{id}', [NurseController::class, 'updatePreEmployment'])->name('nurse.pre-employment.update');
    
    // Nurse Annual Physical Edit Routes
    Route::get('/nurse/annual-physical/{id}/edit', [NurseController::class, 'editAnnualPhysical'])->name('nurse.annual-physical.edit');
    Route::patch('/nurse/annual-physical/{id}', [NurseController::class, 'updateAnnualPhysical'])->name('nurse.annual-physical.update');
    
    // Nurse OPD Routes
    Route::get('/nurse/opd', [NurseController::class, 'opd'])->name('nurse.opd');
    Route::get('/nurse/opd/create', [NurseController::class, 'createOpdExamination'])->name('nurse.opd.create');
    Route::post('/nurse/opd', [NurseController::class, 'storeOpdExamination'])->name('nurse.opd.store');
    Route::get('/nurse/opd/{id}/edit', [NurseController::class, 'editOpdExamination'])->name('nurse.opd.edit');
    Route::patch('/nurse/opd/{id}', [NurseController::class, 'updateOpdExamination'])->name('nurse.opd.update');

    // Nurse Medical Checklist Routes
    Route::get('/nurse/medical-checklist/pre-employment/{recordId}', [NurseController::class, 'showMedicalChecklistPreEmployment'])->name('nurse.medical-checklist.pre-employment');
    Route::get('/nurse/medical-checklist/annual-physical/{patientId}', [NurseController::class, 'showMedicalChecklistAnnualPhysical'])->name('nurse.medical-checklist.annual-physical');
    Route::get('/nurse/medical-checklist/opd/{userId}', [NurseController::class, 'showMedicalChecklistOpd'])->name('nurse.medical-checklist.opd');
    Route::post('/nurse/medical-checklist', [NurseController::class, 'storeMedicalChecklist'])->name('nurse.medical-checklist.store');
    Route::patch('/nurse/medical-checklist/{id}', [NurseController::class, 'updateMedicalChecklist'])->name('nurse.medical-checklist.update');

    // Nurse Messaging Routes
    Route::get('/nurse/messages', [NurseController::class, 'messages'])->name('nurse.messages');
    Route::get('/nurse/messages/fetch', [NurseController::class, 'fetchMessages']);
    Route::post('/nurse/messages/send', [NurseController::class, 'sendMessage']);
    Route::post('/nurse/messages/mark-read', [NurseController::class, 'markAsRead']);
    Route::get('/nurse/messages/count', [NurseController::class, 'getUnreadMessageCount']);
    Route::get('/nurse/chat-users', [NurseController::class, 'chatUsers']);
});

Route::middleware(['auth', 'role:radtech'])->group(function () {
    Route::get('/radtech/dashboard', [RadtechController::class, 'dashboard'])->name('radtech.dashboard');
    
    // Radtech X-Ray Service Routes
    Route::get('/radtech/pre-employment-xray', [RadtechController::class, 'preEmploymentXray'])->name('radtech.pre-employment-xray');
    Route::get('/radtech/annual-physical-xray', [RadtechController::class, 'annualPhysicalXray'])->name('radtech.annual-physical-xray');
    
    // Radtech Medical Checklist Routes
    Route::get('/radtech/medical-checklist/pre-employment/{recordId}', [RadtechController::class, 'showMedicalChecklistPreEmployment'])->name('radtech.medical-checklist.pre-employment');
    Route::get('/radtech/medical-checklist/annual-physical/{patientId}', [RadtechController::class, 'showMedicalChecklistAnnualPhysical'])->name('radtech.medical-checklist.annual-physical');
    Route::post('/radtech/medical-checklist', [RadtechController::class, 'storeMedicalChecklist'])->name('radtech.medical-checklist.store');
    Route::patch('/radtech/medical-checklist/{id}', [RadtechController::class, 'updateMedicalChecklist'])->name('radtech.medical-checklist.update');

    // Radtech Messaging Routes
    Route::get('/radtech/messages', [RadtechController::class, 'messages'])->name('radtech.messages');
    Route::get('/radtech/messages/fetch', [RadtechController::class, 'fetchMessages']);
    Route::post('/radtech/messages/send', [RadtechController::class, 'sendMessage']);
    Route::post('/radtech/messages/mark-read', [RadtechController::class, 'markAsRead']);
    Route::get('/radtech/messages/count', [RadtechController::class, 'getUnreadMessageCount']);
    Route::get('/radtech/chat-users', [RadtechController::class, 'chatUsers']);
});

Route::middleware(['auth', 'role:plebo'])->group(function () {
    Route::get('/plebo/dashboard', [PleboController::class, 'dashboard'])->name('plebo.dashboard');
    Route::get('/plebo/pre-employment', [PleboController::class, 'preEmployment'])->name('plebo.pre-employment');
    Route::get('/plebo/annual-physical', [PleboController::class, 'annualPhysical'])->name('plebo.annual-physical');
    Route::get('/plebo/opd', [PleboController::class, 'opd'])->name('plebo.opd');
    Route::get('/plebo/medical-checklist/pre-employment/{recordId}', [PleboController::class, 'showMedicalChecklistPreEmployment'])->name('plebo.medical-checklist.pre-employment');
    Route::get('/plebo/medical-checklist/annual-physical/{patientId}', [PleboController::class, 'showMedicalChecklistAnnualPhysical'])->name('plebo.medical-checklist.annual-physical');
    Route::get('/plebo/medical-checklist/opd/{userId}', [PleboController::class, 'showMedicalChecklistOpd'])->name('plebo.medical-checklist.opd');
    Route::post('/plebo/medical-checklist', [PleboController::class, 'storeMedicalChecklist'])->name('plebo.medical-checklist.store');
    Route::patch('/plebo/medical-checklist/{id}', [PleboController::class, 'updateMedicalChecklist'])->name('plebo.medical-checklist.update');
    Route::get('/plebo/test-assignments', [PleboController::class, 'testAssignments'])->name('plebo.test-assignments');
    Route::post('/plebo/test-assignments/{id}/update-status', [PleboController::class, 'updateTestAssignmentStatus'])->name('plebo.test-assignments.update-status');

    // Plebo Messaging Routes
    Route::get('/plebo/messages', [PleboController::class, 'messages'])->name('plebo.messages');
    Route::get('/plebo/messages/fetch', [PleboController::class, 'fetchMessages']);
    Route::post('/plebo/messages/send', [PleboController::class, 'sendMessage']);
    Route::post('/plebo/messages/mark-read', [PleboController::class, 'markAsRead']);
    Route::get('/plebo/messages/count', [PleboController::class, 'getUnreadMessageCount']);
    Route::get('/plebo/chat-users', [PleboController::class, 'chatUsers']);
});

Route::middleware(['auth', 'role:radiologist'])->group(function () {
    Route::get('/radiologist/dashboard', [RadiologistController::class, 'dashboard'])->name('radiologist.dashboard');
    
    // X-Ray List Routes
    Route::get('/radiologist/pre-employment-xray', [RadiologistController::class, 'preEmploymentXray'])->name('radiologist.pre-employment-xray');
    Route::get('/radiologist/annual-physical-xray', [RadiologistController::class, 'annualPhysicalXray'])->name('radiologist.annual-physical-xray');
    Route::get('/radiologist/xray-gallery', [RadiologistController::class, 'xrayGallery'])->name('radiologist.xray-gallery');
    
    // Individual X-Ray Review Routes
    Route::get('/radiologist/pre-employment/{id}', [RadiologistController::class, 'showPreEmployment'])->name('radiologist.pre-employment.show');
    Route::patch('/radiologist/pre-employment/{id}', [RadiologistController::class, 'updatePreEmployment'])->name('radiologist.pre-employment.update');
    Route::get('/radiologist/annual-physical/{id}', [RadiologistController::class, 'showAnnualPhysical'])->name('radiologist.annual-physical.show');
    Route::patch('/radiologist/annual-physical/{id}', [RadiologistController::class, 'updateAnnualPhysical'])->name('radiologist.annual-physical.update');

    // Radiologist Messaging Routes
    Route::get('/radiologist/messages', [RadiologistController::class, 'messages'])->name('radiologist.messages');
    Route::get('/radiologist/messages/fetch', [RadiologistController::class, 'fetchMessages']);
    Route::post('/radiologist/messages/send', [RadiologistController::class, 'sendMessage']);
    Route::post('/radiologist/messages/mark-read', [RadiologistController::class, 'markAsRead']);
    Route::get('/radiologist/messages/count', [RadiologistController::class, 'getUnreadMessageCount']);
    Route::get('/radiologist/chat-users', [RadiologistController::class, 'chatUsers']);
});

Route::middleware(['auth', 'role:ecgtech'])->group(function () {
    // Dashboard
    Route::get('/ecgtech/dashboard', [EcgtechController::class, 'dashboard'])->name('ecgtech.dashboard');
    
    // Pre-Employment
    Route::get('/ecgtech/pre-employment', [EcgtechController::class, 'preEmployment'])->name('ecgtech.pre-employment');
    Route::get('/ecgtech/pre-employment/{id}/edit', [EcgtechController::class, 'editPreEmployment'])->name('ecgtech.pre-employment.edit');
    Route::patch('/ecgtech/pre-employment/{id}', [EcgtechController::class, 'updatePreEmployment'])->name('ecgtech.pre-employment.update');
    
    // Annual Physical
    Route::get('/ecgtech/annual-physical', [EcgtechController::class, 'annualPhysical'])->name('ecgtech.annual-physical');
    Route::get('/ecgtech/annual-physical/{id}/edit', [EcgtechController::class, 'editAnnualPhysical'])->name('ecgtech.annual-physical.edit');
    Route::patch('/ecgtech/annual-physical/{id}', [EcgtechController::class, 'updateAnnualPhysical'])->name('ecgtech.annual-physical.update');
    
    // OPD
    Route::get('/ecgtech/opd', [EcgtechController::class, 'opd'])->name('ecgtech.opd');
    Route::get('/ecgtech/opd/{id}/edit', [EcgtechController::class, 'editOpd'])->name('ecgtech.opd.edit');
    Route::patch('/ecgtech/opd/{id}', [EcgtechController::class, 'updateOpd'])->name('ecgtech.opd.update');
    Route::get('/ecgtech/opd/create', [EcgtechController::class, 'createOpd'])->name('ecgtech.opd.create');
    
    // Medical Checklist
    Route::get('/ecgtech/medical-checklist/pre-employment/{recordId}', [EcgtechController::class, 'showMedicalChecklistPreEmployment'])->name('ecgtech.medical-checklist.pre-employment');
    Route::get('/ecgtech/medical-checklist/annual-physical/{patientId}', [EcgtechController::class, 'showMedicalChecklistAnnualPhysical'])->name('ecgtech.medical-checklist.annual-physical');
    Route::get('/ecgtech/medical-checklist/opd/{userId}', [EcgtechController::class, 'showMedicalChecklistOpd'])->name('ecgtech.medical-checklist.opd');
    Route::get('/ecgtech/medical-checklist-page/pre-employment/{recordId}', [EcgtechController::class, 'showMedicalChecklistPagePreEmployment'])->name('ecgtech.medical-checklist-page.pre-employment');
    Route::get('/ecgtech/medical-checklist-page/annual-physical/{patientId}', [EcgtechController::class, 'showMedicalChecklistPageAnnualPhysical'])->name('ecgtech.medical-checklist-page.annual-physical');
    Route::post('/ecgtech/medical-checklist', [EcgtechController::class, 'storeMedicalChecklist'])->name('ecgtech.medical-checklist.store');
    Route::patch('/ecgtech/medical-checklist/{id}', [EcgtechController::class, 'updateMedicalChecklist'])->name('ecgtech.medical-checklist.update');
    
    // Ecgtech Messaging Routes
    Route::get('/ecgtech/messages', [EcgtechController::class, 'messages'])->name('ecgtech.messages');
    Route::get('/ecgtech/messages/fetch', [EcgtechController::class, 'fetchMessages']);
    Route::post('/ecgtech/messages/send', [EcgtechController::class, 'sendMessage']);
    Route::post('/ecgtech/messages/mark-read', [EcgtechController::class, 'markAsRead']);
    Route::get('/ecgtech/messages/count', [EcgtechController::class, 'getUnreadMessageCount']);
    Route::get('/ecgtech/chat-users', [EcgtechController::class, 'chatUsers']);
    
    // Test route
    Route::get('/ecgtech/test-contacts', [EcgtechController::class, 'testContacts']);
});

Route::middleware(['auth', 'role:pathologist'])->group(function () {
    // Dashboard
    Route::get('/pathologist/dashboard', [PathologistController::class, 'dashboard'])->name('pathologist.dashboard');
    
    // Medical Checklist
    Route::get('/pathologist/medical-checklist', [PathologistController::class, 'medicalChecklist'])->name('pathologist.medical-checklist');
    Route::post('/pathologist/medical-checklist', [PathologistController::class, 'storeMedicalChecklist'])->name('pathologist.medical-checklist.store');
    Route::patch('/pathologist/medical-checklist/{id}', [PathologistController::class, 'updateMedicalChecklist'])->name('pathologist.medical-checklist.update');
    
    // Annual Physical
    Route::get('/pathologist/annual-physical', [PathologistController::class, 'annualPhysical'])->name('pathologist.annual-physical');
    Route::get('/pathologist/annual-physical/{id}/edit', [PathologistController::class, 'editAnnualPhysical'])->name('pathologist.annual-physical.edit');
    Route::put('/pathologist/annual-physical/{id}', [PathologistController::class, 'updateAnnualPhysical'])->name('pathologist.annual-physical.update');
    
    // Pre-Employment
    Route::get('/pathologist/pre-employment', [PathologistController::class, 'preEmployment'])->name('pathologist.pre-employment');
    Route::get('/pathologist/pre-employment/{id}', [PathologistController::class, 'showPreEmployment'])->name('pathologist.pre-employment.show');
    Route::get('/pathologist/pre-employment/{id}/edit', [PathologistController::class, 'editPreEmployment'])->name('pathologist.pre-employment.edit');
    Route::put('/pathologist/pre-employment/{id}', [PathologistController::class, 'updatePreEmployment'])->name('pathologist.pre-employment.update');
    
    // OPD
    Route::get('/pathologist/opd', [PathologistController::class, 'opd'])->name('pathologist.opd');
    Route::get('/pathologist/opd/{id}/edit', [PathologistController::class, 'editOpd'])->name('pathologist.opd.edit');
    Route::put('/pathologist/opd/{id}', [PathologistController::class, 'updateOpd'])->name('pathologist.opd.update');
    Route::get('/pathologist/medical-checklist/opd/{userId}', [PathologistController::class, 'showMedicalChecklistOpd'])->name('pathologist.medical-checklist.opd');
    
    // Messages
    Route::get('/pathologist/messages', [PathologistController::class, 'messages'])->name('pathologist.messages');
    Route::get('/pathologist/chat-users', [PathologistController::class, 'chatUsers'])->name('pathologist.chat-users');
    Route::get('/pathologist/fetch-messages', [PathologistController::class, 'fetchMessages'])->name('pathologist.fetch-messages');
    Route::post('/pathologist/send-message', [PathologistController::class, 'sendMessage'])->name('pathologist.send-message');
    Route::post('/pathologist/mark-as-read', [PathologistController::class, 'markAsRead'])->name('pathologist.mark-as-read');
});

Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    Route::get('/patient/profile', [PatientController::class, 'profile'])->name('patient.profile');
    Route::put('/patient/profile', [PatientController::class, 'updateProfile'])->name('patient.profile.update');
});


// Generic dashboard redirector for authenticated users
Route::middleware(['auth'])->get('/dashboard', function() {
    $user = Auth::user();
    if ($user->isAdmin()) return redirect()->route('admin.dashboard');
    if ($user->isCompany()) return redirect()->route('company.dashboard');
    if ($user->isDoctor()) return redirect()->route('doctor.dashboard');
    if ($user->isNurse()) return redirect()->route('nurse.dashboard');
    if ($user->isRadTech()) return redirect()->route('radtech.dashboard');
    if ($user->isRadiologist()) return redirect()->route('radiologist.dashboard');
    if ($user->isPathologist()) return redirect()->route('pathologist.dashboard');
    if ($user->isPlebo()) return redirect()->route('plebo.dashboard');
    if ($user->isOpd()) return redirect()->route('opd.dashboard');
    return redirect()->route('patient.dashboard');
});



// Public invitation routes
Route::get('/invitation/{token}', [App\Http\Controllers\CompanyAccountInvitationController::class, 'accept'])->name('invitation.accept');
Route::post('/invitation/{token}', [App\Http\Controllers\CompanyAccountInvitationController::class, 'processInvitation'])->name('invitation.process');

// Public pages
Route::get('/about', function () {
    $aboutContent = \App\Models\PageContent::getPageContent('about');
    return view('about', compact('aboutContent'));
})->name('about');

Route::get('/services', function () {
    $servicesContent = \App\Models\PageContent::getPageContent('services');
    return view('services', compact('servicesContent'));
})->name('services');

Route::get('/service', function () {
    return redirect('/services');
});

Route::get('/location', function () {
    $locationContent = \App\Models\PageContent::getPageContent('location');
    return view('location', compact('locationContent'));
})->name('location');

// Default route - redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});