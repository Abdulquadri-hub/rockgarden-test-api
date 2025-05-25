<?php

use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ExchangeRateController;
use App\Http\Controllers\Api\ServiceApplicationController;
use App\Http\Controllers\Api\StaffAssignmentController;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Http\Controllers\Api\CronJobController;
use App\Http\Controllers\Api\StaffReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use App\Mail\InvoiceEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AnnouncementController;
//use App\Http\Controllers\ProductController;

Route::get('/send-email', [TwilioSMSController::class, 'mail']);
Route::get('/send-sms', [TwilioSMSController::class, 'sms']);

Route::get('/test', function () {
    return response()->json(['success' => true, 'message' => 'api works fine']);
});


Route::group(['middleware' => ['auth:api']], function() {
    /*Route::post('/logout', [ApiController::class, 'logout']);
    Route::get('/get-user', [ApiController::class, 'get_user']);
    Route::post('/profile/update', [ApiController::class, 'profile_update']);*/


//    Route::get('/products', [ProductController::class, 'index']);
//    Route::get('/products/{id}', [ProductController::class, 'show']);
//    Route::post('/create', [ProductController::class, 'store']);
//    Route::put('update/{product}',  [ProductController::class, 'update']);
//    Route::delete('delete/{product}',  [ProductController::class, 'destroy']);
});

Route::group([
    'namespace' => 'App\Http\Controllers\Api',
], function (){

    Route::post('/login', 'ApiController@authenticate')->name('login-simple');
    Route::post('/admin/login', 'ApiController@authenticateAdmin')->name('login-admin');
    Route::post('/register','ApiController@register')->name('register');
    Route::post('/register/verify','ApiController@verifyOtp');
    Route::post('/forgot-password','ApiController@sendResetLinkResponse');
    Route::post('/forgot-password/check','ApiController@sendResetLinkResponseCheck');
    Route::post('/reset-password','ApiController@sendResetResponse');
    Route::post('/resend-otp','ApiController@resend_otp');

    Route::post('/charge-callback', [InvoiceController::class, 'charge_callback']);
    Route::post('/charge-callback-rave', [InvoiceController::class, 'charge_callback_rave']);
    Route::get('/requery-transaction','InvoiceController@verify_payment');
    Route::get('/requery-transaction-mutiple','InvoiceController@verify_payment_mutiple');
    Route::get('/admin/requery-transaction','InvoiceController@verify_payment');



    //Invoice init
     Route::post('/init-transaction', [InvoiceController::class, 'init_transaction']);
     Route::post('/init-transaction-multiple', [InvoiceController::class, 'init_transaction_multiple']);


    //Get PDF link of Unpaid invoices,
       Route::get('/get-invoice-attachment', [InvoiceController::class, 'getinvoiceattachment']);
       //Get all Invoice Record unpaid and paid by client_id
 Route::get('/get-invoices-unpaid-client', [InvoiceController::class, 'getunpaidinvoicesclient']);
 Route::get('/get-invoices-unpaid-client-new', [InvoiceController::class, 'getunpaidinvoicesclientnew']);
    Route::get('/get-faqs','FAQController@index');
    Route::get('/get-faqs-paged','FAQController@indexPaged');
    Route::get('/get-faq','FAQController@show');

    Route::group([
      'middleware' => ['auth:api']
    ], function() {

        Route::get('/sendSMS', [TwilioSMSController::class, 'index']);

        //messages
        Route::get('/messages/outbox', 'MessageController@outbox');
        Route::get('/messages/inbox', 'MessageController@inbox');
        Route::get('/messages/{message}', 'MessageController@show');
        Route::post('/messages/send', 'MessageController@store');
        Route::post('/messages/reply/{message}', 'MessageController@reply');
        Route::post('/messages/forward/{message}', 'MessageController@forward');
        Route::post('/messages/update-read-status', 'MessageController@updateReadStatus');
        Route::post('/messages/search', 'MessageController@search');
        Route::delete('/messages/delete/{message}', 'MessageController@destroy');
        Route::post('/messages/restore/{message}', 'MessageController@restore');

        Route::get('/messages/user/{user}', [MessageController::class, 'getUserMessages'])->middleware('admin');
        Route::post('/messages/bulk', [MessageController::class, 'bulk'])->middleware('admin');

        //announcement
        Route::get('/announcements', 'AnnouncementController@index');
        Route::get('announcements/unread-count', 'AnnouncementController@unreadCount');
        Route::get('announcements/unread', 'AnnouncementController@getUnread');
        Route::post('announcements/{announcement}/read', 'AnnouncementController@markAsRead');

        Route::post('announcement/send', 'AnnouncementController@store')->middleware('admin');
        Route::post('announcement/edit/{announcement}', 'AnnouncementController@update')->middleware('admin');
        Route::post('announcement/delete/{announcement}', 'AnnouncementController@destroy')->middleware('admin');


        Route::get('/get-familyfriend-assignment','FamilyFriendsAssignmentController@show');
        Route::get('/get-familyfriend-assignments','FamilyFriendsAssignmentController@index');
        Route::get('/get-familyfriend-assignments-paged','FamilyFriendsAssignmentController@indexPaged');

        Route::get('/get-service', [ServiceController::class, 'get_service']);
        Route::get('/get-application-histories', [ServiceApplicationController::class, 'indexPaged']);
        Route::get('/get-application-history', [ServiceApplicationController::class, 'show']);

        Route::get('/get-staff-assignments', [StaffAssignmentController::class, 'index']);
        Route::get('/', [ServiceController::class, 'get_application_history']);

        Route::post('/apply-for-service', [ServiceController::class, 'apply_for_service']);
        Route::post('/upload-file', [ProfileController::class, 'upload_file']);
        Route::post('/update-photo', [ProfileCOntroller::class, 'update_photo']);

        Route::post('/logout', 'ApiController@logout');
        Route::get('/get-user', 'ApiController@get_user');
        Route::post('/profile/update', 'ApiController@profile_update');

        Route::get('/payroll-dashboard', 'ApiController@payrollDashboard');
        Route::get('/care-dashboard', 'ApiController@chartDashboard');

        Route::get('/get-profile', 'ApiController@getProfile');

        // Dashboard
        Route::get('/inventory-dashboard', 'ApiController@inventoryDashboard');
        Route::get('/billing-dashboard', 'ApiController@billingDashboard');
        Route::get('/app-dashboard', 'ApiController@appDashboard');
        Route::post('/update-password', 'ApiController@updatePassword');
        Route::post('/update-avatar', 'ApiController@updateAvatar');

        // Invoice
        Route::get('/get-invoices', [InvoiceController::class, 'index']);

        // Route::get('/send-invoice-email', [InvoiceController::class, 'send_invoice_email']);


        // review update
        Route::post('/review-assigned-staff', [ReviewController::class, 'store']);



        // Transactions
        Route::get('/get-transaction','TransactionController@show');
        Route::get('/get-transactions','TransactionController@index');

        // Tax
        Route::get('/get-tax','TaxController@show');
        Route::get('/get-taxes','TaxController@index');
        Route::get('/get-taxes-paged','TaxController@indexPaged');

        // Allowance
        Route::get('/get-allowance','AllowanceController@show');
        Route::get('/get-allowances','AllowanceController@index');
        Route::get('/get-allowances-paged','AllowanceController@indexPaged');

        // Bonus
        Route::get('/get-bonus','BonusController@show');
        Route::get('/get-bonuses','BonusController@index');
        Route::get('/get-bonuses-paged','BonusController@indexPaged');

        // Loan
        Route::get('/get-loan','LoanController@show');
        Route::get('/get-loans','LoanController@index');
        Route::get('/get-loans-paged','LoanController@indexPaged');

        // Employee Salary
        Route::get('/get-employee-salary','EmployeeSalaryController@show');
        Route::get('/get-employee-salaries','EmployeeSalaryController@index');
        Route::get('/get-employee-salaries-paged','EmployeeSalaryController@indexPaged');

        // Deduction
        Route::get('/get-deduction','DeductionController@show');
        Route::get('/get-deductions','DeductionController@index');
        Route::get('/get-deductions-paged','DeductionController@indexPaged');

        // Employee Payrun
        Route::get('/get-employee-payrun','PayRunController@show');
        Route::get('/get-employee-payruns','PayRunController@index');
//        Route::post('/create-employee-payrun','PayRunController@store');
//        Route::post('/update-employee-payrun','PayRunController@update');
//        Route::post('/delete-employee-payrun','PayRunController@destroy');


        // Employee Loan Repayment
        Route::get('/get-employee-loan-repayment','EmployeeLoanRepaymentController@show');
        Route::get('/get-employee-loan-repayments','EmployeeLoanRepaymentController@indexPaged');
        Route::get('/get-employee-loan-repayments-paged','EmployeeLoanRepaymentController@indexPaged');

        // Employee Payrun
        Route::get('/get-employee-payrun','EmployeePayRunController@show');
        Route::get('/get-employee-payruns','EmployeePayRunController@indexPaged');
        Route::get('/get-employee-payruns-paged','EmployeePayRunController@indexPaged');

        // Rota
        Route::get('/get-rota','RotaController@indexDateBetween');
        /*Route::get('/get-service', [ServiceController::class, 'get_service']);
        Route::get('/get-application-histories', [ServiceController::class, 'get_application_histories']);
        Route::get('/get-application-history', [ServiceController::class, 'get_application_history']);
        Route::get('/get-staff-assignments', [StaffController::class, 'get_staff_assignments']);

        Route::get('/get-invoices', [InvoiceController::class, 'get_invoices']);
        Route::post('/init-transaction', [InvoiceController::class, 'init_transaction']);
        Route::post('/charge-callback', [InvoiceController::class, 'charge_callback']);
        Route::post('/charge-callback-rave', [InvoiceController::class, 'charge_callback_rave']);

        Route::get('/', [ServiceController::class, 'get_application_history']);

        Route::post('/apply-for-service', [ServiceController::class, 'apply_for_service']);
        Route::post('/upload-file', [ProfileController::class, 'upload_file']);
        Route::post('/update-photo', [ProfileCOntroller::class, 'update_photo']);

        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::post('/create', [ProductController::class, 'store']);
        Route::put('update/{product}',  [ProductController::class, 'update']);
        Route::delete('delete/{product}',  [ProductController::class, 'destroy']);*/

        // designations
        Route::get('/get-designations','DesignationController@index'); //{{base_url}}admin/get-designation
        Route::get('/get-designations-paged','DesignationController@indexPaged'); //{{base_url}}admin/get-designation
        Route::get('/get-designation','DesignationController@show');

        // departments
        Route::get('/get-departments','DepartmentController@index');
        Route::get('/get-departments-paged','DepartmentController@indexPaged');
        Route::get('/get-department','DepartmentController@show');

        // Attendances
        Route::get('/get-attendances','AttendanceController@index');
        Route::get('/get-attendance','AttendanceController@indexDateBetween');
        Route::get('/get-one-attendance','AttendanceController@show');
        Route::post('/attendance/check-in','AttendanceController@checkin');
        Route::post('/attendance/check-out','AttendanceController@checkout');
        Route::post('/delete-attendance','AttendanceController@destroy');
        Route::post('/update-attendance-record','AttendanceController@updateAttendanceRecords');

        // Attendances
        Route::get('/get-application-histories','ServiceApplicationController@indexPaged');
        Route::get('/get-application-histories-by-status','ServiceApplicationController@indexByStatusPaged');
        Route::get('/get-application-histories-by-plan','ServiceApplicationController@indexByPlanPaged');
        Route::get('/get-application-histories-by-plan-and-status','ServiceApplicationController@indexByPlanAndStatusPaged');
        Route::get('/get-application','ServiceApplicationController@show');
        Route::post('/apply-for-service','ServiceApplicationController@store');
        Route::post('/update-service-application','ServiceApplicationController@update');
        Route::post('/delete-attendance','ServiceApplicationController@destroy');

        // Staff Chart
        Route::get('/get-staff-charts-by-type','StaffChartController@findAllByType');
        Route::get('/get-staff-charts','StaffChartController@index');
        Route::get('/get-one-staff-chart','StaffChartController@show');
        Route::post('/create-staff-chart','StaffChartController@store');
        Route::post('/update-staff-chart','StaffChartController@update');
        Route::post('/delete-staff-chart','StaffChartController@destroy');
        Route::get('/get-staff-chart-notes', 'StaffChartController@indexNotes');

        // Employee Rating
        Route::get('/get-employee-ratings','EmployeeRatingController@index');
        Route::get('/get-employee-rating','EmployeeRatingController@show');
        Route::post('/submit-employee-rating','EmployeeRatingController@store');
        Route::post('/delete-employee-rating','EmployeeRatingController@destroy');

        // Health Issue
        Route::get('/get-health-issues','HealthIssueController@index')->name('public-health-issues');
        Route::get('/get-health-issue','HealthIssueController@show')->name('public-health-issue');
        Route::post('/create-health-issue','HealthIssueController@store');
        Route::post('/update-health-issue','HealthIssueController@update');
        Route::post('/delete-health-issue','HealthIssueController@destroy');

        // Incident
        Route::get('/get-incidents','IncidentController@index');
        Route::get('/get-incident','IncidentController@show');
        Route::post('/create-incident','IncidentController@store');
        Route::post('/update-incident','IncidentController@update');
        Route::post('/delete-incident','IncidentController@destroy');

        // Incident
        Route::get('/get-incidents','IncidentController@index');
        Route::get('/get-incident','IncidentController@show');
        Route::post('/create-incident','IncidentController@store');
        Route::post('/update-incident','IncidentController@update');
        Route::post('/delete-incident','IncidentController@destroy');

        // Employee
        Route::get('/get-employee','EmployeeController@show');

        // Contact(CRUD)✅
        Route::get('/get-contact','ContactController@show')->name('public-contact');
        Route::get('/get-contacts','ContactController@index')->name('public-contact-list');
        Route::get('/get-contacts-paged','ContactController@indexPaged')->name('public-contacts-paged');
        Route::post('/create-contact','ContactController@store')->name('public-contact-add');
        Route::post('/update-contact','ContactController@update');
        Route::post('/delete-contact','ContactController@destroy');

        // Manufacturer(CRUD)✅
        Route::get('/get-manufacturer','ManufacturersController@show');
        Route::get('/get-manufacturers','ManufacturersController@index');
        Route::get('/get-manufacturers-paged','ManufacturersController@indexPaged');
        Route::post('/create-manufacturer','ManufacturersController@store');
        Route::post('/update-manufacturer','ManufacturersController@update');
        Route::post('/delete-manufacturer','ManufacturersController@destroy');

        // Product Type(CRUD)✅
        Route::get('/get-product-type','ProductTypeController@show');
        Route::get('/get-product-types','ProductTypeController@index');
        Route::get('/get-product-types-paged','ProductTypeController@indexPaged');

        // Item (CRUD)✅
        Route::get('/get-item','ItemController@show');
        Route::get('/get-items','ItemController@indexPaged');
        Route::get('/get-items-paged','ItemController@indexPaged');

        // Item Group (CRUD)✅
        Route::get('/get-item-group','ItemGroupController@show');
        Route::get('/get-item-groups','ItemGroupController@indexPaged');
        Route::get('/get-item-groups-paged','ItemGroupController@indexPaged');

        // Stock (CRUD)✅
        Route::get('/get-stock','StockController@show');
        Route::get('/get-stocks','StockController@indexPaged');
        Route::get('/get-stocks-paged','StockController@indexPaged');
        Route::post('/create-stock','StockController@store');
        Route::post('/update-stock','StockController@update');
        Route::post('/delete-stock','StockController@destroy');

        // Vendor (CRUD)✅
        Route::get('/get-vendor','VendorController@show');
        Route::get('/get-vendors','VendorController@index');
        Route::get('/get-vendors-paged','VendorController@indexPaged');

        // Sales Order (CRUD)✅
        Route::get('/get-sale-order','SaleOrderController@show');
        Route::get('/get-sale-orders','SaleOrderController@index');
        Route::get('/get-sale-orders-paged','SaleOrderController@indexPaged');
        Route::post('/create-sale-order','SaleOrderController@store');
        Route::post('/update-sale-order','SaleOrderController@update');
        Route::post('/delete-sale-order','SaleOrderController@destroy');

        Route::get('/item-sales', 'SaleOrderController@getItemSales');

        // CLient Medication
        Route::get('/get-client-medication','ClientMedicationController@show');
        Route::get('/get-client-medications','ClientMedicationController@index');
        Route::post('/create-client-medication','ClientMedicationController@store');
        Route::post('/update-client-medication','ClientMedicationController@update');
        Route::post('/delete-client-medication','ClientMedicationController@destroy');

        // Medication Type
        Route::get('/get-medication-type','MedicationTypeController@show');
        Route::get('/get-medication-types','MedicationTypeController@index');
        Route::get('/get-medication-types-paged','MedicationTypeController@indexPaged');

        // Medication intake
        Route::get('/get-medication-intake','MedicationInTakeController@show');
        Route::get('/get-medication-intakes','MedicationInTakeController@index');
        Route::get('/get-medication-intakes-paged','MedicationInTakeController@indexPaged');
        Route::post('/create-medication-intake','MedicationInTakeController@store');
        Route::post('/update-medication-intake','MedicationInTakeController@update');
        Route::post('/delete-medication-intake','MedicationInTakeController@destroy');

        // Medical History
        Route::get('/get-medical-history','MedicalHistoryController@show')->name('medical-history-details');
        Route::get('/get-medical-histories','MedicalHistoryController@index')->name('medical-history-list');
        Route::get('/get-medical-histories-paged','MedicalHistoryController@indexPaged')->name('medical-history-list-paged');
        Route::post('/create-medical-history','MedicalHistoryController@store');
        Route::post('/update-medical-history','MedicalHistoryController@update');

        // Key Contact
        Route::get('/get-key-contact','KeyContactController@show');
        Route::get('/get-key-contacts','KeyContactController@index');
        Route::get('/get-key-contacts-paged','KeyContactController@indexPaged');
        Route::post('/create-key-contact','KeyContactController@store');
        Route::post('/update-key-contact','KeyContactController@update');
        Route::post('/delete-key-contact','KeyContactController@destroy');

        // Death record
        Route::get('/get-death-record','DeathRecordController@show');
        Route::get('/get-death-records','DeathRecordController@indexPaged');
        Route::post('/create-death-record','DeathRecordController@store');
        Route::post('/delete-death-record','DeathRecordController@destroy');

        // Purchase Order (CRUD)✅
        Route::get('/get-purchase-order','PurchaseOrderController@show');
        Route::get('/get-purchase-order','PurchaseOrderController@indexPaged');
        Route::get('/get-purchase-orders-paged','PurchaseOrderController@indexPaged');
        Route::post('/create-purchase-order','PurchaseOrderController@store');
        Route::post('/update-purchase-order','PurchaseOrderController@update');
        Route::post('/delete-purchase-order','PurchaseOrderController@destroy');

        // Document
        Route::get('/get-documents','DocumentController@index');
        Route::get('/get-document','DocumentController@show');
        Route::post('/create-document','DocumentController@store');
        Route::post('/create-document-bulk','DocumentController@storeAll');
        Route::post('/update-document','DocumentController@update');
        Route::post('/delete-document','DocumentController@destroy');

        // DocumentStaff
        Route::get('/get-document-staffs','DocumentStaffController@index');
        Route::get('/get-document-staff','DocumentStaffController@show');
        Route::post('/create-document-staff','DocumentStaffController@store');
        Route::post('/create-document-staff-bulk','DocumentStaffController@storeAll');
        Route::post('/update-document-staff','DocumentStaffController@update');
        Route::post('/delete-document-staff','DocumentStaffController@destroy');


        //
        Route::get('/get-client-assigned-staff', 'ClientController@clientAssignedStaff');
    });

    // Health Issue
    Route::get('/get-health-issues','HealthIssueController@index');
    Route::get('/get-health-issue','HealthIssueController@show');
    Route::post('/create-health-issue','HealthIssueController@store');

    // Incident
    Route::get('/get-incidents','IncidentController@index');
    Route::get('/get-incident','IncidentController@show');
    Route::post('/create-incident','IncidentController@store');
    Route::post('/update-incident','IncidentController@update');

});

Route::group([
    'namespace' => 'App\Http\Controllers\Api',
    'prefix' => 'admin',
], function (){

    Route::group([
        'middleware' => ['auth:api','admin'],
    ], function() {

        // FAQ FAQController
        Route::get('/get-faqs','FAQController@index');
        Route::get('/get-faqs-paged','FAQController@indexPaged');
        Route::get('/get-faq','FAQController@show');
        Route::post('/create-faq','FAQController@store');
        Route::post('/update-faq','FAQController@update');
        Route::post('/delete-faq','FAQController@destroy');

        //flaggesTask
        Route::get('/get-flagged-tasks','FlaggedTaskController@index');
        Route::get('/get-flagged-task/{id}','FlaggedTaskController@show');

        // Service Application
        Route::post('/reject-application', [ServiceApplicationController::class, 'reject']);
        Route::post('/approve-application', [ServiceApplicationController::class, 'approve']);

        // Staff Reports
        Route::get('/staff-reports', 'StaffReportController@index');
        Route::get('/staff-reports/{id}', 'StaffReportController@show');
        Route::get('/staff-reports/{id}/download', 'StaffReportController@downloadPdf');
        Route::get('/staff-reports/options/staff', 'StaffReportController@getStaffOptions');
        Route::get('/staff-reports/analytics/statistics', 'StaffReportController@getStatistics');
        Route::get('/staff-reports/options/periods', 'StaffReportController@getAvailablePeriods');
        Route::get('/staff-reports/actions/archive', 'StaffReportController@archiveOldReports');

        // Dashboard
        Route::get('/payroll-dashboard', 'ApiController@payrollDashboard');
        Route::get('/care-dashboard', 'ApiController@chartDashboard');
        Route::get('/inventory-dashboard', 'ApiController@inventoryDashboard');
        Route::get('/billing-dashboard', 'ApiController@billingDashboard');

        // Health Issue
        Route::get('/get-health-issues','HealthIssueController@index');
        Route::get('/get-health-issue','HealthIssueController@show');
        Route::post('/create-health-issue','HealthIssueController@store');
        Route::post('/update-health-issue','HealthIssueController@update');
        Route::post('/delete-health-issue','HealthIssueController@destroy');

        // Incident
        Route::get('/get-incidents','IncidentController@index');
        Route::get('/get-incident','IncidentController@show');
        Route::post('/create-incident','IncidentController@store');
        Route::post('/update-incident','IncidentController@update');
        Route::post('/delete-incident','IncidentController@destroy');

        // Item Categories
        Route::get('/get-item-categories','ItemCategoryController@index');
        Route::get('/get-item-category','ItemCategoryController@show');
        Route::post('/create-item-category','ItemCategoryController@store');
        Route::post('/update-item-category','ItemCategoryController@update');
        Route::delete('delete-item-category','ItemCategoryController@destroy');

        // designations
        Route::get('/get-designations','DesignationController@index'); //{{base_url}}admin/get-designation
        Route::get('/get-designations-paged','DesignationController@indexPaged'); //{{base_url}}admin/get-designation
        Route::get('/get-designation','DesignationController@show');
        Route::post('/create-designation','DesignationController@store'); //{{base_url}}admin/create-designation // name
        Route::post('/update-designation','DesignationController@update');
        Route::post('/delete-designation','DesignationController@destroy');

        // departments
        Route::get('/get-departments','DepartmentController@index');
        Route::get('/get-departments-paged','DepartmentController@indexPaged');
        Route::get('/get-department','DepartmentController@show');
        Route::post('/create-department','DepartmentController@store');
        Route::post('/update-department','DepartmentController@update');
        Route::post('/delete-department','DepartmentController@destroy');

        // Receipt
        Route::get('/get-receipt','ReceiptController@show');
        Route::get('/get-receipts','ReceiptController@index');
        Route::get('/get-receipts-paged','ReceiptController@indexPaged');
        Route::post('/create-receipt','ReceiptController@store');
        Route::post('/update-receipt','ReceiptController@update');
        Route::post('/delete-receipt','ReceiptController@destroy');

        // Invoice
        Route::get('/get-invoice','InvoiceController@show');
        Route::get('/get-invoices','InvoiceController@indexPaged');
        Route::post('/create-invoice','InvoiceController@store');
        Route::post('/update-invoice','InvoiceController@update');
        Route::post('/delete-invoice','InvoiceController@destroy');
        Route::post('/delete-multiple-invoice','InvoiceController@destroyMultiple');
        Route::post('/send-invoice-email', [InvoiceController::class, 'send_invoice_email']);
        // Route::post('/send-invoice-email2', [InvoiceController::class, 'send_invoice_email2']);
        //Start-invoice-cron
        Route::post('/start-invoice-cron', [CronJobController::class, 'start_cron']);
        //stop-invoice-cron
        Route::post('/stop-invoice-cron', [CronJobController::class, 'stop_cron']);
        //Status_invoice cron
        Route::get('/status-invoice-cron', [CronJobController::class, 'status_cron']);
       //For send email USD and NGN
       Route::post('/send-invoice-email-usd-ngn', [InvoiceController::class, 'sendInvoiceEmailUsdNgn']);

       //Get the sum of Unpaid invoices,
       Route::get('/get-invoices-unpaid', [InvoiceController::class, 'getunpaidinvoices']);
       Route::get('/get-invoices-unpaid-new', [InvoiceController::class, 'getunpaidinvoicesnew']);

        Route::post('/send-employee-payrun-email','PayRunController@payrun_email');

   //Exchange Rate Crud,
       Route::get('/get-exchange-rates', [ExchangeRateController::class, 'index']);
       Route::post('/create-exchange-rate', [ExchangeRateController::class, 'store']);
       Route::post('/update-exchange-rate', [ExchangeRateController::class, 'update']);
       Route::post('/delete-exchange-rate', [ExchangeRateController::class, 'destroy']);



  Route::get('/demo-email', [InvoiceController::class, 'demo']);










        // Transactions
        Route::get('/get-transaction','TransactionController@show');
        Route::get('/get-transactions','TransactionController@indexPaged');
        Route::post('/delete-transaction','TransactionController@destroy');

        // Medicine Name
        Route::get('/get-medicine-name','MedicineNameController@show');
        Route::get('/get-medicine-names','MedicineNameController@index');
        Route::get('/get-medicine-names-paged','MedicineNameController@indexPaged');
        Route::post('/create-medicine-name','MedicineNameController@store');
        Route::post('/update-medicine-name','MedicineNameController@update');
        Route::post('/delete-medicine-name','MedicineNameController@destroy');

        // Medication Type
        Route::get('/get-medication-type','MedicationTypeController@show');
        Route::get('/get-medication-types','MedicationTypeController@index');
        Route::get('/get-medication-types-paged','MedicationTypeController@indexPaged');
        Route::post('/create-medication-type','MedicationTypeController@store');
        Route::post('/update-medication-type','MedicationTypeController@update');
        Route::post('/delete-medication-type','MedicationTypeController@destroy');


        // Medication InTake
        Route::get('/get-medication-intake','MedicationInTakeController@show');
        Route::get('/get-medication-intakes','MedicationInTakeController@index');
        Route::get('/get-medication-intakes-paged','MedicationInTakeController@indexPaged');
        Route::post('/create-medication-intake','MedicationInTakeController@store');
        Route::post('/update-medication-intake','MedicationInTakeController@update');
        Route::post('/delete-medication-intake','MedicationInTakeController@destroy');


        // Client Medication
        Route::get('/get-client-medication','ClientMedicationController@show');
        Route::get('/get-client-medications-paged','ClientMedicationController@indexPaged');
        Route::get('/get-client-medications','ClientMedicationController@indexPaged');
        Route::post('/create-client-medication','ClientMedicationController@store');
        Route::post('/update-client-medication','ClientMedicationController@update');
        Route::post('/delete-client-medication','ClientMedicationController@destroy');


        // Medical History
        Route::get('/get-medical-history','MedicalHistoryController@show');
        Route::get('/get-medical-histories','MedicalHistoryController@index');
        Route::get('/get-medical-histories-paged','MedicalHistoryController@indexPaged')->name('admin-medical-histories-paged');
        Route::post('/create-medical-history','MedicalHistoryController@store');
        Route::post('/update-medical-history','MedicalHistoryController@update');

        // Key Contact
        Route::get('/get-key-contact','KeyContactController@show');
        Route::get('/get-key-contacts','KeyContactController@index');
        Route::get('/get-key-contacts-paged','KeyContactController@indexPaged');
        Route::post('/create-key-contact','KeyContactController@store');
        Route::post('/update-key-contact','KeyContactController@update');
        Route::post('/delete-key-contact','KeyContactController@destroy');

        // Employee
        Route::get('/get-employee','EmployeeController@show');
        Route::get('/get-employees','EmployeeController@index');
        Route::get('/get-employees-paged','EmployeeController@indexPaged');
        Route::post('/create-employee','EmployeeController@store');
        Route::post('/update-employee','EmployeeController@update');
        Route::post('/delete-employee','EmployeeController@destroy');
        Route::get('/get-staffs-summary', 'EmployeeController@staffChart');


        // Report Staff
        Route::get('/get-active-staffs', 'EmployeeController@staffActive');
        Route::get('/get-inactive-staffs', 'EmployeeController@staffNotActive');
        Route::get('/get-assigned-staffs', 'EmployeeController@staffAssigned');
        Route::get('/get-unassigned-staffs','EmployeeController@staffNotAssigned');
         Route::post('/deactivate-staffs','EmployeeController@deactivate');
        Route::post('/reactivate-staffs','EmployeeController@reactivate');

        // Client
        Route::get('/get-client','ClientController@show');
        Route::get('/get-clients','ClientController@index');
        Route::get('/get-clients-paged','ClientController@indexPaged');
        Route::post('/create-client','ClientController@store');
        Route::post('/update-client','ClientController@update');
        Route::post('/delete-client','ClientController@destroy');

        // Report Client
        Route::get('/get-active-clients', 'ClientController@clientActive');
        Route::get('/get-inactive-clients', 'ClientController@clientNotActive');
        Route::get('/get-assigned-clients', 'ClientController@clientAssigned');
        Route::get('/get-unassigned-clients', 'ClientController@clientNotAssigned');

        Route::post('/deactivate-client','ClientController@deactivate');
        Route::post('/reactivate-client','ClientController@reactivate');
        Route::get('/get-clients-summary', 'ClientController@clientChart');


        // Death record
        Route::get('/get-death-record','DeathRecordController@show');
        Route::get('/get-death-records','DeathRecordController@indexPaged');
        Route::post('/create-death-record','DeathRecordController@store');
        Route::post('/delete-death-record','DeathRecordController@destroy');

        // Family Friend Assignment
        Route::get('/get-familyfriend-assignment','FamilyFriendsAssignmentController@show');
        Route::get('/get-familyfriend-assignments','FamilyFriendsAssignmentController@index');
        Route::get('/get-familyfriend-assignments-paged','FamilyFriendsAssignmentController@indexPaged');
        Route::post('/create-familyfriend-assignment','FamilyFriendsAssignmentController@store');
        Route::post('/delete-familyfriend-assignment','FamilyFriendsAssignmentController@destroy');

        // Staff Assignment
        Route::get('/get-staff-assignment','StaffAssignmentController@show');
        Route::get('/get-staff-assignments','StaffAssignmentController@index');
        Route::get('/get-staff-assignments-paged','StaffAssignmentController@indexPaged');
        Route::post('/create-staff-assignment','StaffAssignmentController@store');
        Route::post('/create-staff-assignment-bulk','StaffAssignmentController@storeAll');
        Route::post('/delete-staff-assignment','StaffAssignmentController@destroy');

        // NotificationSettings
        Route::get('/get-notification-setting','NotificationSettingsController@show');
        Route::get('/get-notification-settings','NotificationSettingsController@index');
        Route::post('/create-notification-setting','NotificationSettingsController@store');
        Route::post('/update-notification-setting','NotificationSettingsController@update');
        Route::post('/delete-notification-setting','NotificationSettingsController@destroy');
        Route::post('/add-notification-settings-bulk', 'NotificationSettingsController@storeAll');

        // SystemContacts
        Route::get('/get-system-contact','SystemContactsController@show');
        Route::get('/get-system-contacts','SystemContactsController@index');
        Route::post('/create-system-contact','SystemContactsController@store');
        Route::post('/update-system-contact','SystemContactsController@update');
        Route::post('/delete-system-contact','SystemContactsController@destroy');

        // InAppNotifications
        Route::get('/get-in-app-notification','InAppNotificationsController@show');
        Route::get('/get-in-app-notifications','InAppNotificationsController@index');
        Route::post('/create-in-app-notification','InAppNotificationsController@store');
        Route::post('/update-in-app-notification','InAppNotificationsController@update');
        Route::post('/delete-in-app-notification','InAppNotificationsController@destroy');

        // Tax
        Route::get('/get-tax','TaxController@show');
        Route::get('/get-taxes','TaxController@index');
        Route::get('/get-taxes-paged','TaxController@indexPaged');
        Route::post('/create-tax','TaxController@store');
        Route::post('/update-tax','TaxController@update');
        Route::post('/delete-tax','TaxController@destroy');

        // Allowance
        Route::get('/get-allowance','AllowanceController@show');
        Route::get('/get-allowances','AllowanceController@index');
        Route::get('/get-allowances-paged','AllowanceController@indexPaged');
        Route::post('/create-allowance','AllowanceController@store');
        Route::post('/update-allowance','AllowanceController@update');
        Route::post('/delete-allowance','AllowanceController@destroy');

        // Bonus
        Route::get('/get-bonus','BonusController@show');
        Route::get('/get-bonuses','BonusController@index');
        Route::get('/get-bonuses-paged','BonusController@indexPaged');
        Route::post('/create-bonus','BonusController@store');
        Route::post('/update-bonus','BonusController@update');
        Route::post('/delete-bonus','BonusController@destroy');

        // Loan
        Route::get('/get-loan','LoanController@show');
        Route::get('/get-loans','LoanController@index');
        Route::get('/get-loans-paged','LoanController@indexPaged');
        Route::post('/create-loan','LoanController@store');
        Route::post('/update-loan','LoanController@update');
        Route::post('/delete-loan','LoanController@destroy');

        // Deduction
        Route::get('/get-deduction','DeductionController@show');
        Route::get('/get-deductions','DeductionController@index');
        Route::get('/get-deductions-paged','DeductionController@indexPaged');
        Route::post('/create-deduction','DeductionController@store');
        Route::post('/update-deduction','DeductionController@update');
        Route::post('/delete-deduction','DeductionController@destroy');

        // Employee Loan
        Route::get('/get-employee-loan','EmployeeLoanController@show');
        Route::get('/get-employee-loans','EmployeeLoanController@index');
        Route::get('/get-employee-loans-paged','EmployeeLoanController@indexPaged');
        Route::post('/create-employee-loan','EmployeeLoanController@store');
        Route::post('/update-employee-loan','EmployeeLoanController@udpate');
        Route::post('/delete-employee-loan','EmployeeLoanController@destroy');

        // Employee Salary
        Route::get('/get-employee-salary','EmployeeSalaryController@show');
        Route::get('/get-employee-salaries','EmployeeSalaryController@index');
        Route::get('/get-employee-salaries-paged','EmployeeSalaryController@indexPaged');
        Route::post('/create-employee-salary','EmployeeSalaryController@store');
        Route::post('/update-employee-salary','EmployeeSalaryController@update');
        Route::post('/delete-employee-salary','EmployeeSalaryController@destroy');

        // Employee Loan Repayment
        Route::get('/get-employee-loan-repayment','EmployeeLoanRepaymentController@show');
        Route::get('/get-employee-loan-repayments','EmployeeLoanRepaymentController@indexPaged');
        Route::get('/get-employee-loan-repayments-paged','EmployeeLoanRepaymentController@indexPaged');
        Route::post('/create-employee-loan-repayment','EmployeeLoanRepaymentController@store');
        Route::post('/delete-employee-loan-repayment','EmployeeLoanRepaymentController@destroy');

//        // Employee Payrun
//        Route::get('/get-employee-payrun','EmployeePayRunController@show');
//        Route::get('/get-semployee-payruns','EmployeePayRunController@indexPaged');
//        Route::get('/get-employee-payruns-paged','EmployeePayRunController@indexPaged');
//        Route::post('/create-employee-payrun','EmployeePayRunController@store');
//        Route::post('/delete-employee-payrun','EmployeePayRunController@destroy');

        // Employee Payrun
        Route::get('/get-employee-payrun','PayRunController@show');
        Route::get('/get-employee-payruns','PayRunController@index');
        Route::post('/create-employee-payrun','PayRunController@store');
        Route::post('/update-employee-payrun','PayRunController@update');
        Route::post('/delete-employee-payrun','PayRunController@destroy');

        // Rota
        Route::get('/get-rota','RotaController@indexDateBetween');
        Route::get('/get-rota-by-date','RotaController@indexDate');
        Route::get('/get-rota-by-id','RotaController@show');
        Route::get('/get-rotas','RotaController@index');
        Route::get('/get-rotas-paged','RotaController@indexPaged');
        Route::post('/add-rota','RotaController@store');
        Route::post('/add-bulk-rota','RotaController@storeList');
        Route::post('/delete-rota','RotaController@destroy');

        // Attendances
        Route::get('/get-attendance','AttendanceController@indexDateBetweenNoAuth');

        // Service Groups
        Route::get('/get-service-group','ServiceGroupController@show');
        Route::get('/get-service-groups','ServiceGroupController@indexGroup');
        Route::post('/create-service-group','ServiceGroupController@store');
        Route::post('/update-service-group','ServiceGroupController@update');
        Route::post('/delete-service-group','ServiceGroupController@destroy');
        Route::post('/add-client-to-service-groups','ServiceGroupController@addClientToGroups');
        Route::post('/add-staff-to-service-groups','ServiceGroupController@addStaffToGroups');

        // Contact(CRUD)✅
        Route::get('/get-contact','ContactController@show');
        Route::get('/get-contacts','ContactController@index');
        Route::get('/get-contacts-paged','ContactController@indexPaged');
        Route::post('/create-contact','ContactController@store');
        Route::post('/update-contact','ContactController@update');
        Route::post('/delete-contact','ContactController@destroy');

        // Manufacturer(CRUD)✅
        Route::get('/get-manufacturer','ManufacturersController@show');
        Route::get('/get-manufacturers','ManufacturersController@index');
        Route::get('/get-manufacturers-paged','ManufacturersController@indexPaged');
        Route::post('/create-manufacturer','ManufacturersController@store');
        Route::post('/update-manufacturer','ManufacturersController@update');
        Route::post('/delete-manufacturer','ManufacturersController@destroy');

        // ItemUnit(CRUD)✅
        Route::get('/get-item-unit','ItemUnitController@show');
        Route::get('/get-item-units','ItemUnitController@index');
        Route::get('/get-item-units-paged','ItemUnitController@indexPaged');
        Route::post('/create-item-unit','ItemUnitController@store');
        Route::post('/update-item-unit','ItemUnitController@update');
        Route::post('/delete-item-unit','ItemUnitController@destroy');

        // Product Type(CRUD)✅
        Route::get('/get-product-type','ProductTypeController@show');
        Route::get('/get-product-types','ProductTypeController@index');
        Route::get('/get-product-types-paged','ProductTypeController@indexPaged');
        Route::post('/create-product-type','ProductTypeController@store');
        Route::post('/update-product-type','ProductTypeController@update');
        Route::post('/delete-product-type','ProductTypeController@destroy');

        // Item (CRUD)✅
        Route::get('/get-item','ItemController@show');
        Route::get('/get-items','ItemController@indexPaged');
        Route::get('/get-items-paged','ItemController@indexPaged');
        Route::post('/create-item','ItemController@store');
        Route::post('/update-item','ItemController@update');
        Route::post('/delete-item','ItemController@destroy');

        // Item Group (CRUD)✅
        Route::get('/get-item-group','ItemGroupController@show');
        Route::get('/get-item-groups','ItemGroupController@indexPaged');
        Route::get('/get-item-groups-paged','ItemGroupController@indexPaged');
        Route::post('/create-item-group','ItemGroupController@store');
        Route::post('/update-item-group','ItemGroupController@update');
        Route::post('/delete-item-group','ItemGroupController@destroy');

        // Stock (CRUD)✅
        Route::get('/get-stock','StockController@show');
        Route::get('/get-stocks','StockController@index');
        Route::get('/get-stocks-paged','StockController@indexPaged');
        Route::post('/create-stock','StockController@store');
        Route::post('/delete-stock','StockController@destroy');

        // Vendor (CRUD)✅
        Route::get('/get-vendor','VendorController@show');
        Route::get('/get-vendors','VendorController@index');
        Route::get('/get-vendors-paged','VendorController@indexPaged');
        Route::post('/create-vendor','VendorController@store');
        Route::post('/update-vendor','VendorController@update');
        Route::post('/delete-vendor','VendorController@destroy');

        // Sales Order (CRUD)✅
        Route::get('/get-sale-order','SaleOrderController@show');
        Route::get('/get-sale-orders','SaleOrderController@index');
        Route::get('/get-sale-orders-paged','SaleOrderController@indexPaged');
        Route::post('/create-sale-order','SaleOrderController@store');
        Route::post('/update-sale-order','SaleOrderController@update');
        Route::post('/delete-sale-order','SaleOrderController@destroy');

        // Purchase Order (CRUD)✅
        Route::get('/get-purchase-order','PurchaseOrderController@show');
        Route::get('/get-purchase-order','PurchaseOrderController@indexPaged');
        Route::get('/get-purchase-orders-paged','PurchaseOrderController@indexPaged');
        Route::post('/create-purchase-order','PurchaseOrderController@store');
        Route::post('/update-purchase-order','PurchaseOrderController@update');
        Route::post('/delete-purchase-order','PurchaseOrderController@destroy');

        // Document
        Route::get('/get-documents','DocumentController@index');
        Route::get('/get-document','DocumentController@show');
        Route::post('/create-document','DocumentController@store');
        Route::post('/update-document','DocumentController@update');
        Route::post('/delete-document','DocumentController@destroy');

        // DocumentStaff
        Route::get('/get-document-staffs','DocumentStaffController@index');
        Route::get('/get-document-staff','DocumentStaffController@show');
        Route::post('/create-document-staff','DocumentStaffController@store');
        Route::post('/update-document-staff','DocumentStaffController@update');
        Route::post('/delete-document-staff','DocumentStaffController@destroy');
    });

});


Route::group([
    'namespace' => 'App\Http\Controllers\Api\Admin',
    'prefix' => 'admin',
    'as' => 'admin.'
], function (){

    Route::post('/registration', 'AuthController@register')->name('registration');

    Route::group([
      'middleware' => ['auth:api','admin'],
    ], function() {

        //permissions
        Route::get('/get-permission','PermissionController@index'); //{{base_url}}admin/get-permission
        Route::post('/create-permission','PermissionController@store'); //{{base_url}}admin/create-permission // name
        Route::post('/update-permission','PermissionController@update');
        Route::post('/delete-permission','PermissionController@destroy');

        // roles
        Route::get('/get-roles','RoleController@index');
        Route::post('/create-role','RoleController@store');
        Route::post('/edit-role','RoleController@edit');
        Route::post('/update-role','RoleController@update');
        Route::post('/delete-role','RoleController@destroy');

        // Users
        Route::get('/get-users','UserController@index');
        Route::get('/get-user-page','UserController@indexPaged');
        Route::get('/get-user','UserController@show');
        Route::post('/create-user','UserController@store');
        Route::post('/update-user','UserController@update');
        Route::post('/delete-user','UserController@destroy');
        Route::post('/block-user','UserController@blockUser');
        Route::get('/get-birthdays','UserController@birthDateList');


        // Shift
        Route::get('/get-shifts','ShiftController@index');
        Route::get('/get-shifts-paged','ShiftController@indexPaged');
        Route::get('/get-shift','ShiftController@show');
        Route::post('/create-shift','ShiftController@store');
        Route::post('/update-shift','ShiftController@update');
        Route::post('/delete-shift','ShiftController@destroy');

        // FAQ FAQController
        Route::get('/get-plans','PlanController@index');
        Route::get('/get-plans-paged','PlanController@indexPaged');
        Route::get('/get-plan','PlanController@show');
        Route::post('/create-plan','PlanController@store');
        Route::post('/update-plan','PlanController@update');
        Route::post('/delete-plan','PlanController@destroy');

    });

});
