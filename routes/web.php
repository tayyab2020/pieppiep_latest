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

// Main domain routes
// Route::domain('localhost')->group(function () {
//   Route::get('/','FrontendController@index')->name('front.index');
//   // Add other routes for the main domain here

// });

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;

// Subdomain routes
Route::domain('{shop}.'.config('app.url'))->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('shop.home');
    Route::get('shopproduct/{id}', [ShopController::class, 'showProduct'])->name('product.show');
});

Route::get('/', 'FrontendController@index')->name('front.index');
Route::get('/privacy-verklaring', 'FrontendController@privacy');
Route::get('/cookies', 'FrontendController@cookies');
Route::get('/verwerkersovereenkomst', 'FrontendController@verwerkersovereenkomst');
Route::get('/algemene-voorwaarden-consumenten', 'FrontendController@AlgemeneVoorwaardenConsumenten');
Route::get('/algemene-voorwaarden-zakelijk', 'FrontendController@AlgemeneVoorwaardenZakelijk');
Route::post('/download-quote-request-api', 'FrontendController@DownloadQuoteRequestApi')->name('download-quote-request-api');
Route::post('/accept-quotation-api', 'FrontendController@AcceptQuotationApi')->name('accept-quotation-api');
Route::get('/products', 'FrontendController@products')->name('front.products');
Route::get('/product/{id}', 'FrontendController@product')->name('front.product');
Route::get('/our-services', 'FrontendController@services')->name('front.services');
Route::get('/service/{id}', 'FrontendController@service')->name('front.service');
Route::get('/products-by-id', 'FrontendController@productsById')->name('all-products-by-id');
Route::get('/handymanproducts-by-id', 'FrontendController@handymanproductsById');
Route::get('/products-model-number-by-model', 'FrontendController@productsModelNumberByModel')->name('all-products-model-number-by-model');
Route::get('/products-models-by-brands', 'FrontendController@productsModelsByBrands')->name('all-products-models-by-brands');
Route::get('/products-brands-by-category', 'FrontendController@productsBrandsByCategory')->name('all-products-brands-by-category');
Route::get('/products-data-by-category', 'FrontendController@productsDataByCategory')->name('all-products-data-by-category');
Route::get('/account-products-models-by-brands', 'FrontendController@accountProductsModelsByBrands')->name('products-models-by-brands');
Route::get('/account-products-brands-by-category', 'FrontendController@accountProductsBrandsByCategory')->name('products-brands-by-category');
Route::get('/get-questions', 'FrontendController@GetQuestions');
Route::get('/get-service-questions', 'FrontendController@GetServiceQuestions');
Route::get('/thankyou/{id}', 'FrontendController@Thankyou');
Route::get('/thankyou-page/{id}', 'FrontendController@ThankyouPage');
Route::post('/lang-change', 'FrontendController@LanguageChange')->name('lang.change');
Route::post('/lang-client-change', 'FrontendController@LanguageClientChange')->name('lang.clientchange');
Route::post('/lang-handyman-change', 'FrontendController@LanguageHandymanChange')->name('lang.handymanchange');
Route::get('/cart', 'FrontendController@Cart')->name('cart');
Route::post('/update-rate', 'FrontendController@UpdateRate');
Route::post('/delete-cart', 'FrontendController@DeleteCart');
Route::get('/services', 'UserController@Services');
Route::get('/get-quotation-data', 'UserController@GetQuotationData');
Route::get('/sub-services', 'UserController@SubServices');
Route::get('/get-id', 'UserController@GetID');
Route::get('/user-services', 'UserController@UserServices');
Route::get('/user-subservices', 'UserController@UserSubServices');
Route::get('/payment-mollie', 'CategoryController@preparePayment')->name('payment-mollie');
Route::name('webhooks.mollie')->post('webhooks/mollie', 'MollieWebhookController@handle');
Route::name('webhooks.first')->post('/mollie', 'MollieFirstPayment@handle');
Route::name('webhooks.last')->post('/mollie1', 'MollieLastPayment@handle');
Route::name('webhooks.quotation_payment')->post('/webhooks/quotation_payment', 'MollieQuotationPaymentController@handle');
Route::get('/handymans', 'FrontendController@users')->name('front.users');
Route::get('/handymans/featured', 'FrontendController@featured')->name('front.featured');
Route::get('/handyman-profile/{id}', 'FrontendController@user')->name('front.user');
Route::get('/category/{slug}', 'FrontendController@types')->name('front.types');
Route::get('/handymans/search/', 'FrontendController@search')->name('user.search');
Route::post('/quote', 'FrontendController@quote')->name('user.quote');
Route::post('/handymans/filter/', 'FrontendController@FilterHandymans')->name('filter-handymans');
Route::get('/veel-gestelde-vragen', 'FrontendController@faq')->name('front.faq');
Route::get('/ads/{id}', 'FrontendController@ads')->name('front.ads');
Route::get('/over-ons', 'FrontendController@about')->name('front.about');
Route::get('/contact', 'FrontendController@contact')->name('front.contact');
Route::get('/blog', 'FrontendController@blog')->name('front.blog');
Route::get('/blog/{id}', 'FrontendController@blogshow')->name('front.blogshow');
Route::post('/contact', 'FrontendController@contactemail')->name('front.contact.submit');
Route::post('/subscribe', 'FrontendController@subscribe')->name('front.subscribe.submit');
Route::post('/aanbieder/contact', 'FrontendController@useremail')->name('front.user.submit');
Route::get('/aanbieder/refresh_code', 'FrontendController@refresh_code');
Route::get('/login', 'Auth\UserLoginController@showLoginForm')->name('user-login');
Route::post('/login', 'Auth\UserLoginController@login')->name('user-login-submit');
Route::get('/logout', 'Auth\UserLoginController@logout')->name('user-logout');
Route::get('/registreren', 'Auth\UserRegisterController@showHandymanRegisterForm')->name('handyman-register');
Route::post('/registreren', 'Auth\UserRegisterController@HandymanRegister')->name('handyman-register-submit');
Route::get('/compress-images', 'UserController@compress_image');


Route::prefix('aanbieder')->group(function () {

    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/chat', 'UserController@chat')->name('chat');
    Route::get('/review-reasons', 'UserController@ReviewReasons')->name('review-reasons');
    Route::get('/customer-messages', 'UserController@CustomerMessages')->name('customer-messages');
    Route::get('/sent-emails/{id?}', 'UserController@SentMails')->name('sent-emails');
    Route::get('/user-update-filter', 'UserController@UserUpdateFilter')->name('user-update-filter');
    Route::post('/update-table-widths', 'UserController@UpdateTableWidths')->name('update-table-widths');
    Route::get('/get-table-widths', 'UserController@getTableWidths')->name('get-table-widths');
    Route::get('/retailer-general-terms', 'UserController@RetailerGeneralTerms')->name('retailer-general-terms');
    Route::post('/retailer-general-terms', 'UserController@RetailerGeneralTermsPost')->name('save-retailer-general-terms');
    Route::get('/new-quotations', 'UserController@NewQuotations')->name('new-quotations');
    Route::get('/new-orders', 'UserController@NewOrders')->name('new-orders');
    Route::get('/new-invoices', 'UserController@NewInvoices')->name('new-invoices');
    Route::get('/view-new-quotation/{id}', 'UserController@EditNewQuotation')->name('view-new-quotation');
    Route::get('/view-details/{id?}', 'UserController@ViewDetails')->name('view-details');
    Route::post('/update-details', 'UserController@UpdateDetails')->name('update-details');
    Route::get('/fetch-customer-quotations', 'UserController@FetchCustomerQuotations')->name('fetch-customer-quotations');
    Route::get('/view-new-invoice/{id}', 'UserController@EditNewQuotation')->name('view-new-invoice');
    Route::get('/edit-order/{id}', 'UserController@EditOrder')->name('edit-order');
    Route::get('/view-order/{id}', 'UserController@EditOrder')->name('view-order');
    Route::get('/show-quotation-pdf/{id}', 'UserController@DownloadNewQuotation')->name('show-quotation-pdf');
    Route::get('/download-new-quotation/{id}', 'UserController@DownloadNewQuotation');
    Route::get('/download-client-invoice-pdf/{id}', 'UserController@DownloadClientQuoteInvoice')->name('download-client-invoice-pdf');
    Route::get('/show-invoice-pdf/{id}', 'UserController@DownloadInvoicePDF')->name('show-invoice-pdf');
    Route::get('/download-invoice-pdf/{id}', 'UserController@DownloadInvoicePDF')->name('download-invoice-pdf');
    Route::get('/download-service-fee-invoice/{id}', 'UserController@DownloadClientQuoteInvoice')->name('download-service-fee-invoice');
    Route::get('/show-negative-invoice-pdf/{id}', 'UserController@DownloadNegativeInvoicePDF')->name('show-negative-invoice-pdf');
    Route::get('/download-negative-invoice-pdf/{id}', 'UserController@DownloadNegativeInvoicePDF');
    Route::get('/download-order-pdf/{id}', 'UserController@DownloadOrderPDF');
    Route::get('/download-full-order-pdf/{id}', 'UserController@DownloadFullOrderPDF');
    Route::get('/download-order-confirmation-pdf/{id}', 'UserController@DownloadOrderConfirmationPDF');
    Route::get('/offerte-type', 'UserController@SelectQuotationsType')->name('select-quotations-type');
    Route::get('/facturen-type', 'UserController@SelectInvoicesType')->name('select-invoices-type');
    Route::get('/offerte/binnen-zonwering', 'UserController@CreateNewQuotation')->name('create-new-quotation');
    Route::post('/store-new-quotation', 'UserController@StoreNewQuotation')->name('store-new-quotation');
    Route::post('/store-new-order', 'UserController@StoreNewOrder')->name('store-new-order');
    Route::post('/store-new-note', 'UserController@StoreNewNote')->name('store-new-note');
    Route::post('/delete-note', 'UserController@DeleteNote')->name('delete-note');
    Route::post('/store-new-tag', 'UserController@StoreNewTag')->name('store-new-tag');
    Route::post('/store-new-task', 'UserController@StoreNewTask')->name('store-new-task');
    Route::get('/get-supplier-products', 'UserController@GetSupplierProducts');
    Route::get('/get-colors', 'UserController@GetColors');
    Route::get('/get-price', 'UserController@GetPrice');
    Route::get('/get-feature-price', 'UserController@GetFeaturePrice');
    Route::get('/get-sub-products-sizes', 'UserController@GetSubProductsSizes');
    Route::get('/klanten', 'UserController@Customers')->name('customers');
    Route::get('/klant-bewerken/{id}', 'UserController@EditCustomer')->name('edit-customer');
    Route::get('/klant-verwijderen/{id}', 'UserController@DeleteCustomer')->name('delete-customer');
    Route::post('/reeleezee-credentials', 'UserController@ReeleezeeCredentials')->name('reeleezee-credentials');
    Route::post('/klanten', 'UserController@PostCustomer')->name('post-create-customer');
    Route::post('/contact-person-post', 'UserController@ContactPersonPost')->name('contact-person-post');
    Route::post('/project-post', 'UserController@ProjectPost')->name('project-post');
    Route::post('/customers-manage-post', 'UserController@CustomerManagePost')->name('customers-manage-post');
    Route::post('/export-quotations-reeleezee', 'APIController@ExportQuotationsReeleezee')->name('export-quotations-reeleezee');
    Route::post('/export-invoices-reeleezee', 'APIController@ExportInvoicesReeleezee')->name('export-invoices-reeleezee');
    Route::get('/import-reeleezee-customers', 'UserController@ImportReeleezeeCustomers')->name('import-reeleezee-customers');
    Route::get('/export-customers-to-reeleezee', 'UserController@ExportCustomersToReeleezee')->name('export-customers-to-reeleezee');
    Route::get('/import-customers', 'UserController@ImportCustomers')->name('import-customers');
    Route::post('/import-customers', 'UserController@PostImportCustomers')->name('post-import-customers');
    Route::post('/export-customers', 'UserController@ExportCustomers')->name('export-customers');
    Route::get('/export-invoices', 'UserController@ExportInvoices')->name('export-invoices');
    Route::get('/export-invoices-xml', 'UserController@ExportInvoicesXML')->name('export-invoices-xml');
    Route::get('/employee-permissions/{id}', 'UserController@EmployeePermissions')->name('employee-permissions');
    Route::post('/employee-permission-store', 'UserController@EmployeePermissionStore')->name('employee-permission-store');
    Route::get('/employees', 'UserController@Employees')->name('employees');
    Route::get('/employee-create', 'UserController@CreateEmployeeForm')->name('employee-create');
    Route::post('/employee-create', 'UserController@PostEmployee')->name('post-create-employee');
    Route::get('/edit-employee/{id}', 'UserController@EditEmployee')->name('edit-employee');
    Route::get('/delete-employee/{id}', 'UserController@DeleteEmployee')->name('delete-employee');
    Route::get('/profile-update-requests', 'UserController@ProfileUpdateRequests')->name('profile-update-requests');
    Route::get('/profile-update-request/{id}', 'UserController@ProfileUpdateRequest')->name('profile-update-request');
    Route::post('/profile-update-request', 'UserController@ProfileUpdateRequestPost')->name('profile-update-request-post');
    Route::get('/klant-aanmaken', 'UserController@CreateCustomerForm')->name('handyman-user-create');
    Route::get('/handleiding', 'UserController@InstructionManual')->name('instruction-manual');
    Route::post('/create-customer', 'UserController@CreateCustomer');
    Route::get('/get-customer-email', 'UserController@GetCustomerEmail');
    Route::get('/email-templates', 'UserController@EmailTemplates')->name('email-templates');
    Route::get('/prefix-settings', 'UserController@PrefixSettings')->name('prefix-settings');
    Route::post('/prefix-settings', 'UserController@SavePrefixSettings')->name('save-prefix-settings');
    Route::get('/general-categories', 'UserController@GeneralCategories')->name('general-categories');
    Route::post('/general-categories', 'UserController@SaveGeneralCategories');
    Route::get('/general-ledgers', 'UserController@GeneralLedgers')->name('general-ledgers');
    Route::get('/create-ledger/{id?}', 'UserController@CreateLedger')->name('create-ledger');
    Route::get('/edit-ledger/{id?}', 'UserController@EditLedger')->name('edit-ledger');
    Route::get('/delete-ledger/{id?}', 'UserController@DeleteLedger')->name('delete-ledger');
    Route::post('/general-ledgers', 'UserController@SaveGeneralLedgers')->name('save-general-ledgers');
    Route::post('/save-email-template', 'UserController@SaveEmailTemplate')->name('save-email-template');
    Route::get('/aanvrager-offerte/{id?}', 'UserController@CustomerQuotations')->name('customer-quotations');
    Route::get('/aanvrager-offerte-ajax/{id?}', 'UserController@CustomerQuotationAjax')->name('customer-quotations-ajax');

    Route::post('/get-quotations-data', 'UserController@getQuotationsData')->name('get-quotations-data');
    Route::get('/aanvrager-facturen/{id?}', 'UserController@CustomerInvoices')->name('customer-invoices');
    Route::get('/offerte/vloeren/{id?}', 'UserController@HandymanCreateQuote')->name('create-custom-quotation');
    Route::get('/aanbieder-opstellen-directe-verkoopfactuur', 'UserController@HandymanCreateQuote')->name('create-direct-invoice');
    Route::post('/opstellen-eigen-offerte', 'UserController@StoreCustomQuotation')->name('store-custom-quotation');
    Route::post('/create-direct-invoice', 'UserController@StoreCustomQuotation')->name('store-direct-invoice');
    Route::get('/quotation-requests', 'UserController@QuotationRequests')->name('client-quotation-requests');
    Route::get('/aanbieder-offerte-aanvragen', 'UserController@HandymanQuotationRequests')->name('handyman-quotation-requests');
    Route::get('/aanbieder-offertes/{id?}', 'UserController@HandymanQuotations')->name('quotations');
    Route::get('/aanbieder-verkoopfacturen/{id?}', 'UserController@HandymanQuotationsInvoices')->name('quotations-invoices');
    Route::get('/aanbieder-commissiefacturen/{id?}', 'UserController@HandymanQuotationsInvoices')->name('commission-invoices');
    Route::get('/offertes/{id?}', 'UserController@Quotations')->name('client-quotations');
    Route::get('/client-new-quotations', 'UserController@ClientNewQuotations')->name('client-new-quotations');
    Route::get('/download-client-new-quotation/{id}', 'UserController@DownloadClientNewQuotation');
    Route::get('/Offerte-op-maat/{id?}', 'UserController@CustomQuotations')->name('client-custom-quotations');
    Route::get('/Offerte-verkoopfactuur/{id?}', 'UserController@ClientNewQuotations')->name('client-quotations-invoices');
    Route::get('/bekijk-offerte-aanvraag/{id}', 'UserController@QuoteRequest');
    Route::get('/download-quote-request/{id}', 'UserController@DownloadQuoteRequest');
    Route::get('/download-quote-request-file/{id}', 'UserController@DownloadQuoteRequestFile');
    Route::get('/download-quote-invoice/{id}', 'UserController@DownloadQuoteInvoice');
    Route::get('/download-commission-invoice/{id}', 'UserController@DownloadCommissionInvoice');
    Route::get('/download-custom-quotation/{id}', 'UserController@DownloadCustomQuotation');
    Route::get('/download-client-quote-invoice/{id}', 'UserController@DownloadClientQuoteInvoice')->name('download-client-quote-invoice');
    Route::get('/download-client-custom-quotation/{id}', 'UserController@DownloadClientCustomQuoteInvoice');
    Route::post('/ask-customization', 'UserController@AskCustomization');
    Route::post('/send-msg', 'UserController@SendMsg');
    Route::get('/messages/{id}', 'UserController@Messages');
    Route::post('/accept-quotation', 'UserController@AcceptQuotationPieppiep');
    Route::post('/pay-quotation', 'UserController@PayQuotationPieppiep');
    Route::get('/quotation-payment-redirect-page/{id}', 'FrontendController@QuotationPaymentRedirectPage');
    Route::get('/versturen-eigen-offerte/{id}', 'UserController@SendCustomQuotation');
    Route::post('/send-new-quotation', 'UserController@SendNewQuotation')->name('send-new-quotation');
    Route::get('/send-quotation-admin/{id}', 'UserController@SendQuotationAdmin');
    Route::post('/send-order', 'UserController@SendOrder')->name('send-new-order');
    Route::get('/change-delivery-dates/{id}', 'UserController@ChangeDeliveryDates');
    Route::get('/supplier-order-delivered/{id}', 'UserController@SupplierOrderDelivered');
    Route::get('/retailer-mark-delivered/{id}', 'UserController@RetailerMarkDelivered');
    Route::get('/create-new-invoice/{id}', 'UserController@CreateNewInvoice');
    Route::get('/create-new-negative-invoice/{id}', 'UserController@EditNewQuotation')->name('create-new-negative-invoice');
    Route::get('/view-negative-invoice/{id}', 'UserController@EditNewQuotation')->name('view-negative-invoice');
    Route::post('/change-delivery-dates', 'UserController@UpdateDeliveryDates')->name('change-delivery-date');
    Route::post('/aangepaste-offerte/ask-customization', 'UserController@CustomQuotationAskCustomization');
    Route::get('/eigen-offerte/accepteren-offerte/{id}', 'UserController@CustomQuotationAcceptQuotation');
    Route::get('/accept-new-quotation/{id}', 'UserController@AcceptNewQuotation')->name('accept-new-quotation');
    Route::get('/accept-new-quotation-mail/{id}', 'UserController@AcceptNewQuotationMail')->name('accept-new-quotation-mail');
    Route::get('/discard-quotation/{id}', 'UserController@DiscardQuotation')->name('discard-quotation');
    Route::get('/delete-new-quotation/{id}', 'UserController@DeleteNewQuotation')->name('delete-new-quotation');
    Route::post('/quotations-delete-post', 'UserController@DeleteNewQuotationsPost')->name('quotations-delete-post');
    Route::get('/delete-new-invoice/{id}', 'UserController@DeleteNewInvoice')->name('delete-new-invoice');
    Route::get('/delete-new-negative-invoice/{id}', 'UserController@DeleteNewInvoice')->name('delete-new-negative-invoice');
    Route::post('/invoices-delete-post', 'UserController@DeleteNewInvoicesPost')->name('invoices-delete-post');
    Route::get('/approve-draft-quotation/{id}', 'UserController@ApproveDraftQuotation')->name('approve-draft-quotation');
    Route::get('/copy-new-quotation/{id}', 'UserController@CopyNewQuotation')->name('copy-new-quotation');
    Route::get('/copy-new-invoice/{id}', 'UserController@CopyNewInvoice')->name('copy-new-invoice');
    Route::get('/copy-new-negative-invoice/{id}', 'UserController@CopyNewInvoice')->name('copy-new-negative-invoice');
    Route::post('/send-invoice', 'UserController@SendInvoice')->name('send-new-invoice');
    Route::post('/send-negative-invoice', 'UserController@SendNegativeInvoice')->name('send-negative-invoice');
    Route::get('/bekijk-offerteaanvraag-aanbieder/{id}', 'UserController@HandymanQuoteRequest');
    Route::get('/download-handyman-quote-request/{id}', 'UserController@DownloadHandymanQuoteRequest');
    Route::get('/opstellen-offerte/{id}', 'UserController@CreateQuotation');
    Route::post('/opstellen-offerte', 'UserController@StoreQuotation')->name('store-quotation');
    Route::post('/update-quotation', 'UserController@StoreQuotation')->name('update-quotation');
    Route::post('/update-custom-quotation', 'UserController@StoreCustomQuotation')->name('update-custom-quotation');
    Route::post('/opstellen-factuur', 'UserController@StoreQuotation')->name('create-invoice');
    Route::post('/opstellen-eigen-factuur', 'UserController@StoreCustomQuotation')->name('post-custom-invoice');
    Route::get('/bekijk-offerte/{id}', 'UserController@ViewQuotation')->name('view-handyman-quotation');
    Route::get('/bekijk-eigen-offerte/{id}', 'UserController@ViewCustomQuotation')->name('view-custom-quotation');
    Route::get('/bewerk-offerte/{id}', 'UserController@ViewQuotation')->name('edit-handyman-quotation');
    Route::get('/bewerk-eigen-offerte/{id}', 'UserController@ViewCustomQuotation')->name('edit-custom-quotation');
    Route::get('/opstellen-factuur/{id}', 'UserController@ViewQuotation')->name('create-handyman-invoice');
    Route::get('/opstellen-eigen-factuur/{id}', 'UserController@ViewCustomQuotation')->name('create-custom-invoice');
    Route::get('/offerte/{id}', 'UserController@ViewClientQuotation')->name('view-client-quotation');
    Route::get('/aangepaste-offerte/{id}', 'UserController@ViewClientCustomQuotation')->name('view-client-custom-quotation');
    Route::get('/handyman-panel', 'UserController@HandymanPanel')->name('handyman-panel');
    Route::get('/dashboard', 'UserController@index')->name('user-dashboard');
    Route::get('/experience-years', 'UserController@ExperienceYears')->name('experience-years');
    Route::post('/post-experience-years', 'UserController@PostExperienceYears')->name('post-experience-years');
    Route::get('/cash', 'UserController@cash')->name('cash');
    Route::get('/tax', 'UserController@tax')->name('tax');

    Route::post('/insurance-upload', 'UserController@InsuranceUpload')->name('insurance-upload');

    Route::get('/insurance', 'UserController@Insurance')->name('insurance');
    Route::get('/ratings', 'UserController@Ratings')->name('ratings');
    Route::get('/client-dashboard', 'UserController@ClientIndex')->name('client-dashboard');
    Route::get('/handyman-bookings', 'UserController@HandymanBookings')->name('handyman-bookings');
    Route::get('/purchased-bookings', 'UserController@PurchasedBookings')->name('purchased-bookings');
    Route::get('/client-bookings', 'UserController@ClientBookings')->name('client-bookings');
    Route::get('/reset', 'UserController@resetform')->name('user-reset');
    Route::post('/reset', 'UserController@reset')->name('user-reset-submit');
    Route::get('/profile', 'UserController@profile')->name('user-profile');
    Route::get('/company-info', 'UserController@profile')->name('company-info');
    Route::get('/availability-manager', 'UserController@AvailabilityManager')->name('user-availability');
    Route::get('/radius-management', 'UserController@RadiusManagement')->name('radius-management');
    Route::get('/client-profile', 'UserController@ClientProfile')->name('client-profile');
    Route::get('/my-products', 'UserController@MyProducts')->name('user-products');
    Route::get('/product-create', 'UserController@MyProducts')->name('product-create');
    Route::post('/product-store', 'UserController@ProductStore')->name('product-store');
    Route::get('/product-edit/{id}', 'UserController@ProductEdit')->name('product-edit');
    Route::get('/product-delete/{id}', 'UserController@ProductDelete')->name('product-delete');
    Route::get('/product-details', 'UserController@ProductDetails');
    Route::get('/my-services', 'UserController@MyServices')->name('my-services');
    Route::post('/my-services', 'UserController@SaveMyServices');
    Route::get('/service-create', 'UserController@MyServices')->name('service-create');
    Route::post('/service-store', 'UserController@ServiceStore')->name('service-store');
    Route::get('/service-edit/{id}', 'UserController@ServiceEdit')->name('service-edit');
    Route::get('/service-delete/{id}', 'UserController@ServiceDelete')->name('service-delete');
    Route::get('/my-items', 'UserController@MyItems')->name('user-items');
    Route::get('/create-item', 'UserController@CreateItem')->name('create-item');
    Route::post('/store-item', 'UserController@StoreItem')->name('store-item');
    Route::get('/edit-item/{id}', 'UserController@EditItem')->name('edit-item');
    Route::post('/update-item/{id}', 'UserController@UpdateItem')->name('update-item');
    Route::get('/delete-item/{id}', 'UserController@DestroyItem')->name('delete-item');
    Route::get('/export-items', 'UserController@ExportItems')->name('export-items');
    Route::get('/import-items', 'UserController@ImportItems')->name('import-items');
    Route::post('/import-items', 'UserController@PostItemsImport')->name('post-import-items');
    Route::get('/my-subservices', 'UserController@MySubServices')->name('user-subservices');
    Route::get('/delete-services', 'UserController@DeleteServices');
    Route::get('/delete-subservices', 'UserController@DeleteSubServices');
    Route::post('/profile-update', 'UserController@TemporaryProfileUpdate')->name('user-temp-profile-update');
    Route::post('/profile', 'UserController@profileupdate')->name('user-profile-update');
    Route::post('/availability-manager', 'UserController@AvailabilityUpdate')->name('user-availability-update');
    Route::post('/radius-manager', 'UserController@RadiusUpdate')->name('user-radius-update');
    Route::post('/client-profile-update', 'UserController@ClientProfileUpdate')->name('client-profile-update');
    Route::post('/my-services-update', 'UserController@MyServicesUpdate')->name('user-services-update');
    Route::post('/my-subservices-update', 'UserController@MySubServicesUpdate')->name('user-subservices-update');
    Route::post('/complete-profile', 'UserController@CompleteProfileUpdate')->name('user-complete-profile-update');
    Route::get('/forgot', 'Auth\UserForgotController@showforgotform')->name('user-forgot');
    Route::post('/forgot', 'Auth\UserForgotController@forgot')->name('user-forgot-submit');
    Route::get('/register', 'Auth\UserRegisterController@showRegisterForm')->name('user-register');
    Route::get('/complete-profile', 'UserController@CompleteProfile')->name('user-complete-profile');
    Route::post('/register', 'Auth\UserRegisterController@register')->name('user-register-submit');
    Route::post('/aanbieder-status-update', 'UserController@HandymanStatusUpdate')->name('handyman-status-update');
    Route::post('/client-status-update', 'UserController@ClientStatusUpdate')->name('client-status-update');
    Route::post('/add-cart', 'UserController@AddCart')->name('add-cart');
    Route::post('/book-handyman', 'UserController@BookHandyman')->name('book-handyman');

    Route::get('/invoice/{id}', 'UserController@Invoice');
    Route::get('/cancelled-invoice/{id}', 'UserController@CancelledInvoice');
    Route::get('/view-images/{id}', 'UserController@Images');


    Route::post('/payment', 'PaymentController@store')->name('payment.submit');
    Route::get('/payment/cancle', 'PaymentController@paycancle')->name('payment.cancle');
    Route::get('/payment/return', 'PaymentController@payreturn')->name('payment.return');

    Route::get('/publish', 'UserController@publish')->name('user-publish');
    Route::get('/feature', 'UserController@feature')->name('user-feature');
    Route::get('/mark-delivered/{id}', 'UserController@MarkDelivered');
    Route::get('/mark-received/{id}', 'UserController@MarkReceived');


    Route::get('/custom-mark-delivered/{id}', 'UserController@CustomMarkDelivered');
    Route::get('/custom-mark-received/{id}', 'UserController@CustomMarkReceived');

    Route::get('/select-product-category', 'ProductController@SelectProductCategory')->name('select-product-category');
    Route::get('/products', 'ProductController@index')->name('admin-product-index');
    Route::get('/product/create', 'ProductController@create')->name('admin-product-create');
    Route::get('/product/import', 'ProductController@import')->name('admin-product-import');
    Route::post('/product/upload', 'ProductController@PostImport')->name('admin-product-upload');
    Route::get('/product/export', 'ProductController@PostExport')->name('admin-product-export');
    Route::post('/product/create', 'ProductController@store')->name('admin-product-store');
    Route::get('/product/edit/{id}', 'ProductController@edit')->name('admin-product-edit');
    Route::get('/product/copy/{id}', 'ProductController@copy')->name('admin-product-copy');
    Route::post('/product/update/{id}', 'ProductController@update')->name('admin-product-update');
    Route::get('/product/delete/{id}', 'ProductController@destroy')->name('admin-product-delete');
    Route::get('/product/get-sub-categories-by-category', 'ProductController@getSubCategoriesByCategory');
    Route::get('/product/get-sizes-by-model', 'ProductController@getSizesByModel');
    Route::get('/product/products-models-by-brands', 'ProductController@productsModelsByBrands');
    Route::get('/product/get-prices-tables', 'ProductController@pricesTables');
    Route::post('/product/store-retailer-margins', 'ProductController@storeRetailerMargins')->name('store-retailer-margins');
    Route::get('/product/reset-supplier-margins', 'ProductController@resetSupplierMargins')->name('reset-supplier-margins');
    Route::get('/product/get-features-data', 'ProductController@featuresData');
    Route::get('/suppliers/products/{id}', 'ProductController@ProductsSupplier')->name('retailer-supplier-products');

    Route::get('/price-tables', 'PriceTablesController@index')->name('admin-price-tables');
    Route::get('/price-tables/create', 'PriceTablesController@create')->name('admin-price-tables-create');
    Route::get('/price-tables/import', 'PriceTablesController@import')->name('admin-price-tables-import');
    Route::post('/price-tables/upload', 'PriceTablesController@PostImport')->name('admin-price-tables-upload');
    Route::get('/price-tables/export', 'PriceTablesController@PostExport')->name('admin-price-tables-export');
    Route::post('/price-tables/create', 'PriceTablesController@store')->name('admin-price-tables-store');
    Route::get('/price-tables/edit/{id}', 'PriceTablesController@edit')->name('admin-price-tables-edit');
    Route::get('/price-tables/prices/view/{id}', 'PriceTablesController@viewPrices')->name('admin-prices-view');
    Route::post('/price-tables/update/{id}', 'PriceTablesController@update')->name('admin-price-tables-update');
    Route::get('/price-tables/delete/{id}', 'PriceTablesController@destroy')->name('admin-price-tables-delete');
    Route::get('/price-tables/prices/delete/{id}', 'PriceTablesController@destroyPrices')->name('admin-prices-delete');

    Route::get('/colors', 'ColorController@index')->name('admin-color-index');
    Route::get('/color/create', 'ColorController@create')->name('admin-color-create');
    Route::post('/color/create', 'ColorController@store')->name('admin-color-store');
    Route::get('/color/edit/{id}', 'ColorController@edit')->name('admin-color-edit');
    Route::get('/color/delete/{id}', 'ColorController@destroy')->name('admin-color-delete');

    Route::get('/services', 'ServiceController@index')->name('admin-service-index');
    Route::get('/service/create', 'ServiceController@create')->name('admin-service-create');
    Route::post('/service/create', 'ServiceController@store')->name('admin-service-store');
    Route::get('/service/edit/{id}', 'ServiceController@edit')->name('admin-service-edit');
    Route::get('/service/delete/{id}', 'ServiceController@destroy')->name('admin-service-delete');

    Route::get('/categories', 'CategoryController@index')->name('admin-cat-index');
    Route::get('/category/create', 'CategoryController@create')->name('admin-cat-create');
    Route::post('/category/create', 'CategoryController@store')->name('admin-cat-store');
    Route::get('/category/edit/{id}', 'CategoryController@edit')->name('admin-cat-edit');
    /*Route::post('/category/update/{id}', 'CategoryController@update')->name('admin-cat-update');*/
    Route::get('/category/delete/{id}', 'CategoryController@destroy')->name('admin-cat-delete');

    Route::get('/my-features', 'FeaturesController@MyCategoriesIndex')->name('admin-features');
    Route::get('/my-features/create', 'FeaturesController@MyCategoryCreate')->name('admin-features-create');
    Route::post('/my-features/create', 'FeaturesController@MyCategoryStore')->name('admin-features-store');
    Route::get('/my-features/edit/{id}', 'FeaturesController@MyCategoryEdit')->name('admin-features-edit');
    Route::get('/my-features/delete/{id}', 'FeaturesController@MyCategoryDestroy')->name('admin-features-delete');

    Route::get('/other-suppliers-brands', 'BrandController@otherSuppliersBrands')->name('other-suppliers-brands');
    Route::post('/supplier-brands-store', 'BrandController@supplierBrandsStore')->name('supplier-brands-store');
    Route::get('/brands', 'BrandController@index')->name('admin-brand-index');
    Route::get('/brand/create', 'BrandController@create')->name('admin-brand-create');
    Route::post('/brand/create', 'BrandController@store')->name('admin-brand-store');
    Route::get('/brand/edit/{id}', 'BrandController@edit')->name('admin-brand-edit');
    Route::post('/brand/update/{id}', 'BrandController@update')->name('admin-brand-update');
    Route::get('/brand/delete/{id}', 'BrandController@destroy')->name('admin-brand-delete');

    Route::get('/other-suppliers-types', 'ModelController@otherSuppliersTypes')->name('other-suppliers-types');
    Route::post('/supplier-types-store', 'ModelController@supplierTypesStore')->name('supplier-types-store');
    Route::get('/types', 'ModelController@index')->name('admin-model-index');
    Route::get('/type/create', 'ModelController@create')->name('admin-model-create');
    Route::post('/type/create', 'ModelController@store')->name('admin-model-store');
    Route::get('/type/edit/{id}', 'ModelController@edit')->name('admin-model-edit');
    Route::post('/type/update/{id}', 'ModelController@update')->name('admin-model-update');
    Route::get('/type/delete/{id}', 'ModelController@destroy')->name('admin-model-delete');

    Route::get('/features', 'FeaturesController@index')->name('admin-feature-index');
    Route::get('/features/create', 'FeaturesController@create')->name('admin-feature-create');
    Route::post('/features/create', 'FeaturesController@store')->name('admin-feature-store');
    Route::get('/features/edit/{id}', 'FeaturesController@edit')->name('admin-feature-edit');
    Route::get('/features/delete/{id}', 'FeaturesController@destroy')->name('admin-feature-delete');
    Route::get('/features/add-default-feature/{id}', 'FeaturesController@edit')->name('add-default-feature');

    Route::group(['middleware' => ['auth:user']], function () {
        Route::get('shop', 'ShopController@create')->name('shop.create');
        Route::post('shop', 'ShopController@store')->name('shop.store');
        Route::get('/features-update-requests', 'FeaturesUpdateRequestsController@index')->name('features-update-requests');
        Route::get('/feature-update-request/{id}', 'FeaturesUpdateRequestsController@edit')->name('feature-update-request');
        Route::post('/feature-update-request/post', 'FeaturesUpdateRequestsController@post')->name('feature-update-request-post');
        Route::get('/feature-update-request/delete/{id}', 'FeaturesUpdateRequestsController@destroy')->name('feature-update-request-delete');

        Route::get('/models-update-requests', 'ModelsUpdateRequestsController@index')->name('models-update-requests');
        Route::get('/model-update-request/{id}', 'ModelsUpdateRequestsController@edit')->name('model-update-request');
        Route::post('/model-update-request/post', 'ModelsUpdateRequestsController@post')->name('model-update-request-post');
        Route::get('/model-update-request/delete/{id}', 'ModelsUpdateRequestsController@destroy')->name('model-update-request-delete');
    });

    Route::get('/models', 'PredefinedModelsController@index')->name('predefined-model-index');
    Route::get('/models/create', 'PredefinedModelsController@create')->name('predefined-model-create');
    Route::post('/models/create', 'PredefinedModelsController@store')->name('predefined-model-store');
    Route::get('/models/edit/{id}', 'PredefinedModelsController@edit')->name('predefined-model-edit');
    Route::get('/models/delete/{id}', 'PredefinedModelsController@destroy')->name('predefined-model-delete');
    Route::get('/models/add-default-model/{id}', 'PredefinedModelsController@edit')->name('add-default-model');

    Route::get('/suppliers', 'UserController@Suppliers')->name('suppliers');
    Route::post('/send-request-supplier', 'UserController@SendRequestSupplier')->name('send-request-supplier');
    Route::get('/suppliers/details/{id}', 'UserController@DetailsSupplier')->name('supplier-details');

    Route::get('/plannings', 'UserController@Plannings')->name('plannings');
    Route::get('/get-plannings', 'UserController@GetPlannings')->name('get-plannings');
    Route::post('/store-plannings', 'UserController@StorePlannings')->name('store-plannings');
    Route::post('/remove-plannings', 'UserController@RemovePlannings')->name('remove-plannings');
    Route::get('/planning-titles', 'UserController@PlanningTitles')->name('planning-titles');
    Route::get('/add-title/{id?}', 'UserController@AddPlanningTitle')->name('add-planning-title');
    Route::post('/store-title', 'UserController@StorePlanningTitle')->name('store-planning-title');
    Route::get('/delete-title/{id}', 'UserController@DeletePlanningTitle')->name('delete-planning-title');
    Route::get('/planning-statuses', 'UserController@PlanningStatuses')->name('planning-statuses');
    Route::get('/planning-statuses/add-status/{id?}', 'UserController@AddPlanningStatus')->name('add-planning-status');
    Route::post('/planning-statuses/store-status', 'UserController@StorePlanningStatus')->name('store-planning-status');
    Route::get('/planning-statuses/delete-status/{id}', 'UserController@DeletePlanningStatus')->name('delete-planning-status');
    Route::get('/planning-responsible-persons', 'UserController@PlanningResponsiblePersons')->name('planning-responsible-persons');
    Route::get('/planning-responsible-persons/edit/{id}', 'UserController@EditPlanningResponsiblePerson')->name('edit-planning-responsible-person');
    Route::post('/planning-responsible-persons/store', 'UserController@StorePlanningResponsiblePerson')->name('store-planning-responsible-person');
    Route::get('/notes', 'UserController@Notes')->name('notes');

    Route::get('/actual', 'UserController@Actual')->name('actual');
    Route::post('/submit-payment', 'UserController@SubmitPayment')->name('submit-payment');
    Route::post('/delete-payment', 'UserController@DeletePayment')->name('delete-payment');
    Route::get('/forecast', 'UserController@Forecast')->name('forecast');
    Route::get('/payment-accounts', 'UserController@PaymentAccounts')->name('payment-accounts');
    Route::post('/payment-accounts', 'UserController@StorePaymentAccounts');
    Route::post('/export-payments-reeleezee', 'APIController@ExportPaymentsReeleezee')->name('export-payments-reeleezee');

    Route::get('/retailers', 'UserController@Retailers')->name('retailers');
    Route::get('/retailers/details/{id}', 'UserController@DetailsRetailer')->name('retailer-details');
    Route::post('/accept-retailer-request', 'UserController@AcceptRetailerRequest')->name('accept-retailer-request');
    Route::post('/suspend-retailer-request', 'UserController@SuspendRetailerRequest')->name('suspend-retailer-request');
    Route::post('/delete-retailer-request', 'UserController@DeleteRetailerRequest')->name('delete-retailer-request');

    Route::get('/supplier-categories', 'UserController@SupplierCategories')->name('supplier-categories');
    Route::post('/supplier-categories-store', 'UserController@SupplierCategoriesStore')->name('supplier-categories-store');

    Route::get('/email-settings', 'MailController@emailSettings')->name('email-settings');
    Route::post('/email-settings/save', 'MailController@saveEmailSettings')->name('email-settings.save');
    Route::get('/send-email', 'MailController@showSendEmailForm')->name('send-email-form');
    Route::post('/send-email', 'MailController@sendEmail')->name('send-email');
    Route::get('/mailbox', 'MailController@receiveEmails')->name('mailbox');
    Route::get('/generate-dkim', 'DKIMController@showGenerateForm')->name('generate-dkim');
    Route::post('/generate-dkim', 'DKIMController@generateDKIMKeys')->name('post-generate-dkim');
    Route::get('/dkim-keys', 'DKIMController@showGeneratedKeys')->name('dkim-keys');
});

Route::get('finalize', 'FrontendController@finalize');
Route::post('the/genius/ocean/2441139', 'FrontEndController@subscription');

Route::post('/user/payment/notify', 'PaymentController@notify')->name('payment.notify');
Route::post('/stripe-submit', 'StripeController@store')->name('stripe.submit');

Route::prefix('logstof')->group(function () {

    Route::get('/items', 'ItemController@index')->name('admin-item-index');
    Route::get('/item/create', 'ItemController@create')->name('admin-item-create');
    Route::post('/item/create', 'ItemController@store')->name('admin-item-store');
    Route::get('/item/edit/{id}', 'ItemController@edit')->name('admin-item-edit');
    Route::post('/item/update/{id}', 'ItemController@update')->name('admin-item-update');
    Route::get('/item/delete/{id}', 'ItemController@destroy')->name('admin-item-delete');
    Route::get('/get-products-by-retailer', 'ItemController@getProductsByRetailer')->name('get-products-by-retailer');

    Route::get('/my-categories', 'MyCategoryController@MyCategoriesIndex')->name('admin-my-cat-index');
    Route::get('/my-category/create', 'MyCategoryController@MyCategoryCreate')->name('admin-my-cat-create');
    Route::post('/my-category/create', 'MyCategoryController@MyCategoryStore')->name('admin-my-cat-store');
    Route::get('/my-category/edit/{id}', 'MyCategoryController@MyCategoryEdit')->name('admin-my-cat-edit');
    Route::get('/my-category/delete/{id}', 'MyCategoryController@MyCategoryDestroy')->name('admin-my-cat-delete');

    Route::get('/pages', 'PageSettingController@index')->name('admin-pages-index');
    Route::get('/pages/create', 'PageSettingController@create')->name('admin-pages-create');
    Route::post('/pages/create', 'PageSettingController@store')->name('admin-pages-store');
    Route::get('/pages/edit/{id}', 'PageSettingController@edit')->name('admin-pages-edit');
    Route::get('/pages/delete/{id}', 'PageSettingController@destroy')->name('admin-pages-delete');

    Route::get('/my-brands', 'MyBrandController@index')->name('admin-my-brand-index');
    Route::get('/my-brand/create', 'MyBrandController@create')->name('admin-my-brand-create');
    Route::post('/my-brand/create', 'MyBrandController@store')->name('admin-my-brand-store');
    Route::get('/my-brand/edit/{id}', 'MyBrandController@edit')->name('admin-my-brand-edit');
    Route::get('/my-brand/delete/{id}', 'MyBrandController@destroy')->name('admin-my-brand-delete');
    Route::get('/my-brand/edit-requests/{id}', 'MyBrandController@editRequests')->name('admin-my-brand-edit-requests');
    Route::get('/my-brand/edit-request/{id}', 'MyBrandController@editRequest')->name('admin-my-brand-edit-request');
    Route::get('/my-brand/delete-edit-request/{id}', 'MyBrandController@deleteEditRequest')->name('admin-my-brand-delete-edit-request');

    Route::get('/features', 'DefaultFeaturesController@index')->name('default-features-index');
    Route::get('/features/create', 'DefaultFeaturesController@create')->name('default-features-create');
    Route::post('/features/create', 'DefaultFeaturesController@store')->name('default-features-store');
    Route::get('/features/edit/{id}', 'DefaultFeaturesController@edit')->name('default-features-edit');
    Route::get('/features/delete/{id}', 'DefaultFeaturesController@destroy')->name('default-features-delete');

    Route::group(['middleware' => ['auth:admin']], function () {
        Route::get('/features-update-requests', 'FeaturesUpdateRequestsController@index')->name('admin-features-update-requests');
        Route::get('/feature-update-request/{id}', 'FeaturesUpdateRequestsController@edit')->name('admin-feature-update-request');
        Route::post('/feature-update-request/post', 'FeaturesUpdateRequestsController@post')->name('admin-feature-update-request-post');
        Route::get('/feature-update-request/delete/{id}', 'FeaturesUpdateRequestsController@destroy')->name('admin-feature-update-request-delete');

        Route::get('/models-update-requests', 'ModelsUpdateRequestsController@index')->name('admin-models-update-requests');
        Route::get('/model-update-request/{id}', 'ModelsUpdateRequestsController@edit')->name('admin-model-update-request');
        Route::post('/model-update-request/post', 'ModelsUpdateRequestsController@post')->name('admin-model-update-request-post');
        Route::get('/model-update-request/delete/{id}', 'ModelsUpdateRequestsController@destroy')->name('admin-model-update-request-delete');
    });

    Route::get('/models', 'DefaultPredefinedModelsController@index')->name('default-models-index');
    Route::get('/models/create', 'DefaultPredefinedModelsController@create')->name('default-models-create');
    Route::post('/models/create', 'DefaultPredefinedModelsController@store')->name('default-models-store');
    Route::get('/models/edit/{id}', 'DefaultPredefinedModelsController@edit')->name('default-models-edit');
    Route::get('/models/delete/{id}', 'DefaultPredefinedModelsController@destroy')->name('default-models-delete');

    Route::get('/mark-delivered/{id}', 'AdminUserController@MarkDelivered');
    Route::get('/mark-received/{id}', 'AdminUserController@MarkReceived');
    Route::get('/dashboard', 'AdminController@index')->name('admin-dashboard');
    Route::get('/invoice/{id}', 'AdminController@Invoice')->name('admin-hi');
    Route::get('/cancelled-invoice/{id}', 'AdminController@CancelledInvoice')->name('admin-chi');
    Route::get('/client-invoice/{id}', 'AdminController@ClientInvoice')->name('admin-ci');
    Route::get('/client-cancelled-invoice/{id}', 'AdminController@ClientCancelledInvoice')->name('admin-cci');
    Route::get('/view-images/{id}', 'AdminController@Images');
    Route::get('/download-invoice/{id}', 'AdminController@DownloadInvoice')->name('admin-download-hi');
    Route::get('/download-cancelled-invoice/{id}', 'AdminController@DownloadCancelledInvoice')->name('admin-download-chi');
    Route::get('/client-download-invoice/{id}', 'AdminController@ClientDownloadInvoice')->name('admin-download-ci');
    Route::get('/client-download-cancelled-invoice/{id}', 'AdminController@ClientDownloadCancelledInvoice')->name('admin-download-cci');
    Route::get('/add-terminals', 'AdminController@AddTerminals')->name('add-terminals');
    Route::get('/profile', 'AdminController@profile')->name('admin-profile');
    Route::post('/profile', 'AdminController@profileupdate')->name('admin-profile-update');
    Route::get('/reset-password', 'AdminController@passwordreset')->name('admin-password-reset');
    Route::post('/reset-password', 'AdminController@changepass')->name('admin-password-change');
    Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('admin-login');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin-login-submit');
    Route::get('/logout', 'Auth\AdminLoginController@logout')->name('admin-logout');
    Route::post('/admin-status-update', 'AdminController@AdminStatusUpdate')->name('admin-status-update');
    Route::get('/documents-index', 'AdminController@Documents')->name('admin-documents-index');
    Route::post('/documents-post', 'AdminController@DocumentsPost')->name('admin-documents-post');
    Route::get('/handyman-terms', 'AdminController@HandymanTerms')->name('admin-handyman-terms');
    Route::get('/client-terms', 'AdminController@ClientTerms')->name('admin-client-terms');
    Route::post('/handyman-terms-post', 'AdminController@HandymanTermsPost')->name('admin-handyman-terms-post');
    Route::post('/client-terms-post', 'AdminController@ClientTermsPost')->name('admin-client-terms-post');
    Route::get('/instruction-manual', 'AdminController@InstructionManual')->name('admin-instruction-manual');
    Route::post('/instruction-manual-post', 'AdminController@InstructionManualPost')->name('admin-instruction-manual-post');

    Route::get('/quotation-questions', 'AdminUserController@QuotationQuestions')->name('quotation-questions');
    Route::get('/create-question', 'AdminUserController@CreateQuestion')->name('create-question');
    Route::post('/create-question', 'AdminUserController@SubmitQuestion')->name('save-question');
    Route::get('/edit-question/{id}', 'AdminUserController@EditQuestion')->name('edit-question');
    Route::get('/delete-question/{id}', 'AdminUserController@DeleteQuestion')->name('delete-question');


    Route::get('/services-quotation-questions', 'AdminUserController@ServicesQuotationQuestions')->name('services-quotation-questions');
    Route::get('/create-services-question', 'AdminUserController@CreateServicesQuestion')->name('create-services-question');
    Route::post('/create-services-question', 'AdminUserController@SubmitServicesQuestion')->name('save-services-question');
    Route::get('/edit-services-question/{id}', 'AdminUserController@EditServicesQuestion')->name('edit-services-question');
    Route::get('/delete-services-question/{id}', 'AdminUserController@DeleteServicesQuestion')->name('delete-services-question');

    Route::get('/quotation-requests', 'AdminUserController@QuotationRequests')->name('quotation-requests');
    Route::get('/retailer-quotations/{id?}', 'AdminUserController@HandymanQuotations')->name('handyman-quotations');
    Route::get('/handyman-quotations-invoices/{id?}', 'AdminUserController@HandymanQuotationsInvoices')->name('handyman-quotations-invoices');
    Route::get('/view-new-quotation/{id}', 'AdminUserController@ViewNewQuotation')->name('admin-view-new-quotation');
    Route::get('/handyman-commission-invoices/{id?}', 'AdminUserController@HandymanQuotationsInvoices')->name('handyman-commission-invoices');
    Route::get('/view-quote-request/{id}', 'AdminUserController@QuoteRequest');
    Route::get('/view-quotation/{id}', 'AdminUserController@ViewQuotation')->name('view-quotation');
    Route::get('/download-quote-request/{id}', 'AdminUserController@DownloadQuoteRequest');
    Route::get('/download-quote-request-file/{id}', 'AdminUserController@DownloadQuoteRequestFile');
    Route::get('/download-quote-invoice/{id}', 'AdminUserController@DownloadQuoteInvoice');
    Route::get('/download-commission-invoice/{id}', 'AdminUserController@DownloadCommissionInvoice');
    Route::get('/send-quote-request/{id}', 'AdminUserController@SendQuoteRequest');
    Route::post('/send-quote-request', 'AdminUserController@SendQuoteRequestHandymen')->name('send-quote-request');
    Route::post('/approve-handyman-quotations', 'AdminUserController@ApproveHandymanQuotations')->name('approve-handyman-quotations');
    Route::get('/retailers', 'AdminUserController@index')->name('admin-user-index');
    Route::get('/suppliers-organizations', 'AdminUserController@SuppliersOrganizations')->name('admin-suppliers');
    Route::get('/suppliers/{id}', 'AdminUserController@Suppliers')->name('admin-supplier-index');
    Route::post('/supplier-manage', 'AdminUserController@SupplierManagePost')->name('admin-supplier-manage-post');
    Route::get('/clients', 'AdminUserController@Clients')->name('admin-user-client');
    Route::get('/bookings', 'AdminUserController@UserBookings')->name('admin-user-bookings');
    Route::get('/user-requests', 'AdminUserController@UserRequests')->name('admin-user-requests');
    Route::get('/request/{id}', 'AdminUserController@UserRequest')->name('admin-user-request');
    Route::post('/request-update', 'AdminUserController@RequestProfileUpdate')->name('request-profile-update');
    Route::get('/retailers/create', 'AdminUserController@create')->name('admin-user-create');
    Route::post('/retailers/create', 'AdminUserController@store')->name('admin-user-store');
    Route::get('/retailers/edit/{id}', 'AdminUserController@edit')->name('admin-user-edit');
    Route::post('/retailers/update/{id}', 'AdminUserController@update')->name('admin-user-update');
    Route::post('/retailers/insurance-update/{id}', 'AdminUserController@InsuranceUpdate')->name('admin-user-insurance-update');
    Route::get('/retailers/delete/{id}', 'AdminUserController@destroy')->name('admin-user-delete');
    Route::get('/retailers/insurance/{id}', 'AdminUserController@Insurance')->name('admin-user-insurance');

    Route::get('/suppliers/create', 'AdminUserController@createSupplier')->name('admin-supplier-create');
    Route::get('/suppliers/edit/{id}', 'AdminUserController@editSupplier')->name('admin-supplier-edit');
    Route::get('/suppliers/details/{id}', 'AdminUserController@DetailsSupplier')->name('admin-supplier-details');
    Route::get('/suppliers/organization-details/{id}', 'AdminUserController@DetailsSupplier')->name('admin-supplier-organization-details');
    Route::post('/suppliers/organization-details', 'AdminUserController@DetailsOrganizationUpdate')->name('admin-supplier-organization-update');
    Route::get('/suppliers/products/{id}', 'ProductController@ProductsSupplier')->name('supplier-products');
    Route::get('/suppliers/live/{id}', 'AdminUserController@liveToggle')->name('admin-supplier-live');

    Route::get('/categories-mapping', 'PrestashopController@categoriesMapping')->name('categories-mapping');
    Route::post('/categories-mapping', 'PrestashopController@storeMappedCategories');
    Route::post('/prestashop-credentials', 'PrestashopController@prestashopCredentials')->name('prestashop-credentials');
    Route::post('/export-products-prestashop', 'PrestashopController@exportProductsPrestashop')->name('export-products-prestashop');

    Route::get('/retailers/details/{id}', 'AdminUserController@Details')->name('admin-user-details');
    Route::get('/retailers/client-details/{id}', 'AdminUserController@ClientDetails')->name('admin-user-client-details');
    Route::get('/retailers/status/{id1}/{id2}', 'AdminUserController@status')->name('admin-user-st');

    Route::get('/products', 'AdminController@allProducts')->name('all-products');
    Route::post('/product/create', 'AdminController@createProduct')->name('all-product-create');
    Route::post('/product/store', 'AdminController@storeProduct')->name('all-product-store');
    Route::get('/product/edit/{id}', 'AdminController@editProduct')->name('all-product-edit');
    Route::get('/product/delete/{id}', 'AdminController@destroyProduct')->name('all-product-delete');
    Route::get('/all-products-export', 'AdminController@allProductsExport')->name('all-products-export');
    Route::get('/admin-update-products-filter', 'AdminController@AdminUpdateProductsFilter')->name('admin-update-products-filter');
    /*Route::get('/categories', 'CategoryController@index')->name('admin-cat-index');
  Route::get('/category/create', 'CategoryController@create')->name('admin-cat-create');
  Route::post('/category/create', 'CategoryController@store')->name('admin-cat-store');
  Route::get('/category/edit/{id}', 'CategoryController@edit')->name('admin-cat-edit');
  //Route::post('/category/update/{id}', 'CategoryController@update')->name('admin-cat-update');//
  Route::get('/category/delete/{id}', 'CategoryController@destroy')->name('admin-cat-delete');*/

    /*Route::get('/services', 'ServiceController@index')->name('admin-service-index');
  Route::get('/service/create', 'ServiceController@create')->name('admin-service-create');
  Route::post('/service/create', 'ServiceController@store')->name('admin-service-store');
  Route::get('/service/edit/{id}', 'ServiceController@edit')->name('admin-service-edit');
  Route::get('/service/delete/{id}', 'ServiceController@destroy')->name('admin-service-delete');*/

    /*Route::get('/products', 'ProductController@index')->name('admin-product-index');
  Route::get('/product/create', 'ProductController@create')->name('admin-product-create');
  Route::get('/product/import', 'ProductController@import')->name('admin-product-import');
  Route::post('/product/upload', 'ProductController@PostImport')->name('admin-product-upload');
  Route::get('/product/export', 'ProductController@PostExport')->name('admin-product-export');
  Route::post('/product/create', 'ProductController@store')->name('admin-product-store');
  Route::get('/product/edit/{id}', 'ProductController@edit')->name('admin-product-edit');
  Route::post('/product/update/{id}', 'ProductController@update')->name('admin-product-update');
  Route::get('/product/delete/{id}', 'ProductController@destroy')->name('admin-product-delete');
  Route::get('/product/products-models-by-brands', 'ProductController@productsModelsByBrands');
  Route::get('/product/get-prices-tables', 'ProductController@pricesTables');*/

    /*Route::get('/price-tables', 'PriceTablesController@index')->name('admin-price-tables');
  Route::get('/price-tables/create', 'PriceTablesController@create')->name('admin-price-tables-create');
  Route::get('/price-tables/import', 'PriceTablesController@import')->name('admin-price-tables-import');
  Route::post('/price-tables/upload', 'PriceTablesController@PostImport')->name('admin-price-tables-upload');
  Route::get('/price-tables/export', 'PriceTablesController@PostExport')->name('admin-price-tables-export');
  Route::post('/price-tables/create', 'PriceTablesController@store')->name('admin-price-tables-store');
  Route::get('/price-tables/edit/{id}', 'PriceTablesController@edit')->name('admin-price-tables-edit');
  Route::get('/price-tables/prices/view/{id}', 'PriceTablesController@viewPrices')->name('admin-prices-view');
  Route::post('/price-tables/update/{id}', 'PriceTablesController@update')->name('admin-price-tables-update');
  Route::get('/price-tables/delete/{id}', 'PriceTablesController@destroy')->name('admin-price-tables-delete');
  Route::get('/price-tables/prices/delete/{id}', 'PriceTablesController@destroyPrices')->name('admin-prices-delete');*/

    /*Route::get('/colors', 'ColorController@index')->name('admin-color-index');
  Route::get('/color/create', 'ColorController@create')->name('admin-color-create');
  Route::post('/color/create', 'ColorController@store')->name('admin-color-store');
  Route::get('/color/edit/{id}', 'ColorController@edit')->name('admin-color-edit');
  Route::get('/color/delete/{id}', 'ColorController@destroy')->name('admin-color-delete');*/

    /*Route::get('/brands', 'BrandController@index')->name('admin-brand-index');
  Route::get('/brand/create', 'BrandController@create')->name('admin-brand-create');
  Route::post('/brand/create', 'BrandController@store')->name('admin-brand-store');
  Route::get('/brand/edit/{id}', 'BrandController@edit')->name('admin-brand-edit');
  Route::post('/brand/update/{id}', 'BrandController@update')->name('admin-brand-update');
  Route::get('/brand/delete/{id}', 'BrandController@destroy')->name('admin-brand-delete');*/

    /*Route::get('/models', 'ModelController@index')->name('admin-model-index');
  Route::get('/model/create', 'ModelController@create')->name('admin-model-create');
  Route::post('/model/create', 'ModelController@store')->name('admin-model-store');
  Route::get('/model/edit/{id}', 'ModelController@edit')->name('admin-model-edit');
  Route::post('/model/update/{id}', 'ModelController@update')->name('admin-model-update');
  Route::get('/model/delete/{id}', 'ModelController@destroy')->name('admin-model-delete');*/

    /*Route::get('/items', 'ItemController@index')->name('admin-item-index');
  Route::get('/item/create', 'ItemController@create')->name('admin-item-create');
  Route::post('/item/create', 'ItemController@store')->name('admin-item-store');
  Route::get('/item/edit/{id}', 'ItemController@edit')->name('admin-item-edit');
  Route::post('/item/update/{id}', 'ItemController@update')->name('admin-item-update');
  Route::get('/item/delete/{id}', 'ItemController@destroy')->name('admin-item-delete');*/

    /*Route::get('/features', 'FeaturesController@index')->name('admin-feature-index');
  Route::get('/features/create', 'FeaturesController@create')->name('admin-feature-create');
  Route::post('/features/create', 'FeaturesController@store')->name('admin-feature-store');
  Route::get('/features/edit/{id}', 'FeaturesController@edit')->name('admin-feature-edit');
  Route::post('/features/update/{id}', 'FeaturesController@update')->name('admin-feature-update');
  Route::get('/features/delete/{id}', 'FeaturesController@destroy')->name('admin-feature-delete');*/

    Route::get('/faq', 'FaqController@index')->name('admin-fq-index');
    Route::get('/faq/create', 'FaqController@create')->name('admin-fq-create');
    Route::post('/faq/create', 'FaqController@store')->name('admin-fq-store');
    Route::get('/faq/edit/{id}', 'FaqController@edit')->name('admin-fq-edit');
    Route::post('/faq/update/{id}', 'FaqController@update')->name('admin-fq-update');
    Route::post('/faqup', 'PageSettingController@faqupdate')->name('admin-faq-update');
    Route::get('/faq/delete/{id}', 'FaqController@destroy')->name('admin-fq-delete');


    Route::get('/blog', 'AdminBlogController@index')->name('admin-blog-index');
    Route::get('/blog/create', 'AdminBlogController@create')->name('admin-blog-create');
    Route::post('/blog/create', 'AdminBlogController@store')->name('admin-blog-store');
    Route::get('/blog/edit/{id}', 'AdminBlogController@edit')->name('admin-blog-edit');
    Route::post('/blog/edit/{id}', 'AdminBlogController@update')->name('admin-blog-update');
    Route::get('/blog/delete/{id}', 'AdminBlogController@destroy')->name('admin-blog-delete');

    Route::get('/testimonial', 'PortfolioController@index')->name('admin-ad-index');
    Route::get('/testimonial/create', 'PortfolioController@create')->name('admin-ad-create');
    Route::post('/testimonial/create', 'PortfolioController@store')->name('admin-ad-store');
    Route::get('/testimonial/edit/{id}', 'PortfolioController@edit')->name('admin-ad-edit');
    Route::post('/testimonial/edit/{id}', 'PortfolioController@update')->name('admin-ad-update');
    Route::get('/testimonial/delete/{id}', 'PortfolioController@destroy')->name('admin-ad-delete');


    Route::get('/advertise', 'AdvertiseController@index')->name('admin-adv-index');
    Route::get('/advertise/st/{id1}/{id2}', 'AdvertiseController@status')->name('admin-adv-st');
    Route::get('/advertise/create', 'AdvertiseController@create')->name('admin-adv-create');
    Route::post('/advertise/create', 'AdvertiseController@store')->name('admin-adv-store');
    Route::get('/advertise/edit/{id}', 'AdvertiseController@edit')->name('admin-adv-edit');
    Route::post('/advertise/edit/{id}', 'AdvertiseController@update')->name('admin-adv-update');
    Route::get('/advertise/delete/{id}', 'AdvertiseController@destroy')->name('admin-adv-delete');

    Route::get('/page-settings/about', 'PageSettingController@about')->name('admin-ps-about');
    Route::post('/page-settings/about', 'PageSettingController@aboutupdate')->name('admin-ps-about-submit');
    Route::get('/page-settings/contact', 'PageSettingController@contact')->name('admin-ps-contact');
    Route::post('/page-settings/contact', 'PageSettingController@contactupdate')->name('admin-ps-contact-submit');



    Route::get('/assign-permissions', 'AdminController@AssignPermissions')->name('admin-assign-permissions');
    Route::get('/assign-permissions/edit/{id}', 'AdminController@AssignPermissionEdit')->name('admin-assign-permission-edit');
    Route::post('/assign-permissions/store', 'AdminController@AssignPermissionsStore')->name('admin-assign-permission-store');
    Route::get('/permissions', 'AdminController@Permissions')->name('admin-permissions-index');
    Route::get('/permissions/create', 'AdminController@PermissionsCreate')->name('admin-permission-create');
    Route::post('/permissions/create', 'AdminController@PermissionsStore')->name('admin-permission-store');
    Route::get('/permissions/edit/{id}', 'AdminController@PermissionEdit')->name('admin-permission-edit');
    Route::get('/social', 'SocialSettingController@index')->name('admin-social-index');
    Route::get('/how-it-works', 'AdminController@HowItWorks')->name('admin-how-it-works');
    Route::post('/social/update', 'SocialSettingController@update')->name('admin-social-update');
    Route::post('/how-it-works/update', 'AdminController@HowItWorksUpdate')->name('admin-how-it-works-update');
    Route::get('/reasons-to-book', 'AdminController@ReasonsToBook')->name('admin-reasons-to-book');
    Route::post('/reasons-to-book/update', 'AdminController@ReasonsToBookUpdate')->name('admin-reasons-to-book-update');

    Route::get('/filter-settings', 'SocialSettingController@FilterSettings')->name('admin-filter-settings');
    Route::post('/filter/update', 'SocialSettingController@FilterUpdate')->name('admin-filter-update');


    Route::get('/seotools/analytics', 'SeoToolController@analytics')->name('admin-seotool-analytics');
    Route::post('/seotools/analytics/update', 'SeoToolController@analyticsupdate')->name('admin-seotool-analytics-update');
    Route::get('/seotools/keywords', 'SeoToolController@keywords')->name('admin-seotool-keywords');
    Route::post('/seotools/keywords/update', 'SeoToolController@keywordsupdate')->name('admin-seotool-keywords-update');

    Route::get('/general-settings/logo', 'GeneralSettingController@logo')->name('admin-gs-logo');
    Route::post('/general-settings/logo', 'GeneralSettingController@logoup')->name('admin-gs-logoup');

    Route::get('/general-settings/favicon', 'GeneralSettingController@fav')->name('admin-gs-fav');
    Route::post('/general-settings/favicon', 'GeneralSettingController@favup')->name('admin-gs-favup');


    Route::get('/general-settings/loader', 'GeneralSettingController@load')->name('admin-gs-load');
    Route::post('/general-settings/loader', 'GeneralSettingController@loadup')->name('admin-gs-loadup');

    Route::get('/general-settings/payments', 'GeneralSettingController@payments')->name('admin-gs-payments');
    Route::post('/general-settings/payments', 'GeneralSettingController@paymentsup')->name('admin-gs-paymentsup');

    Route::get('/general-settings/vats', 'GeneralSettingController@vats')->name('admin-gs-vats');
    Route::get('/general-settings/view-vat/{id}', 'GeneralSettingController@viewVat');
    Route::get('/general-settings/delete-vat/{id}', 'GeneralSettingController@deleteVat');
    Route::get('/general-settings/create-vat', 'GeneralSettingController@createVat')->name('admin-gs-create-vat');
    Route::post('/general-settings/vats', 'GeneralSettingController@vatsup')->name('admin-gs-vatsup');

    Route::get('/general-settings/contents', 'GeneralSettingController@contents')->name('admin-gs-contents');
    Route::post('/general-settings/contents', 'GeneralSettingController@contentsup')->name('admin-gs-contentsup');

    Route::post('/general-settings/contents-change', 'GeneralSettingController@contentsChange')->name('theme.change');

    Route::get('/general-settings/bgimg', 'GeneralSettingController@bgimg')->name('admin-gs-bgimg');
    Route::post('/general-settings/bgimgup', 'GeneralSettingController@bgimgup')->name('admin-gs-bgimgup');

    Route::get('/general-settings/about', 'GeneralSettingController@about')->name('admin-gs-about');
    Route::post('/general-settings/about', 'GeneralSettingController@aboutup')->name('admin-gs-aboutup');

    Route::get('/general-settings/address', 'GeneralSettingController@address')->name('admin-gs-address');
    Route::post('/general-settings/address', 'GeneralSettingController@addressup')->name('admin-gs-addressup');

    Route::get('/general-settings/footer', 'GeneralSettingController@footer')->name('admin-gs-footer');
    Route::post('/general-settings/footer', 'GeneralSettingController@footerup')->name('admin-gs-footerup');

    Route::get('/general-settings/bg-info', 'GeneralSettingController@bginfo')->name('admin-gs-bginfo');
    Route::post('/general-settings/bg-info', 'GeneralSettingController@bginfoup')->name('admin-gs-bginfoup');

    Route::get('/subscribers', 'SubscriberController@index')->name('admin-subs-index');
    Route::get('/subscribers/download', 'SubscriberController@download')->name('admin-subs-download');

    Route::get('/languages', 'LanguageController@lang')->name('admin-lang-index');
    Route::post('/languages', 'LanguageController@langup')->name('admin-lang-submit');
});

// Route::domain('localhost')->group(function () {
//   Route::get('{slug}', [
//     'uses' => 'FrontendController@getPage'
//   ])->where('slug', '([A-Za-z0-9\-\/]+)');

//   });


// use Illuminate\Support\Facades\Log;

// Route::get('/test-log', function () {
//     Log::info('This is a test log entry.');
//     return 'Check the log file for the entry.';
// });
