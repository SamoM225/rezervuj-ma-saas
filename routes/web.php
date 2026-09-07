<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\BusinessSettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Platform\DirectoryController;
use App\Http\Controllers\Platform\PayPalWebhookController;
use App\Http\Controllers\Platform\RegistrationController;
use App\Http\Controllers\Platform\SiteController;
use App\Http\Controllers\Platform\SitemapController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TenantLegalController;
use App\Http\Controllers\TenantProfileController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\WorkerAvailabilityController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkerDashboardController;
use App\Jobs\SendBookingReminders;
use App\Support\ScheduledMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Platform routes (no tenant): marketing site, signup, billing, admin.
|--------------------------------------------------------------------------
*/
// The same platform routes exist once per language: "/" = sk, "/cs/…", "/en/…".
// Names are prefixed with the locale ("sk.home", "cs.home"); use Locales::route().
$platform = function (string $locale) {
    $t = fn (string $key) => __("site.paths.{$key}", [], $locale);

    Route::get('/', [SiteController::class, 'home'])->name('home');
    Route::get($t('pricing'), [SiteController::class, 'pricing'])->name('pricing');
    Route::get('/legal', [SiteController::class, 'legalIndex'])->name('legal.index');
    Route::get('/legal/{doc}', [SiteController::class, 'legalShow'])->where('doc', 'terms|dpa|privacy|cookies|aup|refunds|imprint|subprocessors')->name('legal.show');

    Route::get($t('directory'), [DirectoryController::class, 'index'])->name('directory.index');
    Route::get($t('directory').'/{category}', [DirectoryController::class, 'category'])->name('directory.category');
    Route::get($t('directory').'/{category}/{city}', [DirectoryController::class, 'city'])->name('directory.city');

    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->middleware('throttle:signup')->name('register.store');
    Route::get('/register/verify', [RegistrationController::class, 'verify'])->name('register.verify');
    Route::post('/register/verify', [RegistrationController::class, 'confirm'])->middleware('throttle:otp')->name('register.confirm');
    Route::post('/register/resend', [RegistrationController::class, 'resend'])->middleware('throttle:otp-resend')->name('register.resend');
};

Route::middleware('platform.locale:sk')->name('sk.')->group(fn () => $platform('sk'));
foreach (['cs', 'en'] as $locale) {
    Route::prefix($locale)->middleware("platform.locale:{$locale}")->name("{$locale}.")->group(fn () => $platform($locale));
}

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::post('/webhooks/paypal', PayPalWebhookController::class)->name('webhooks.paypal');

Route::post('/internal/scheduler/run', function (Request $request) {
    $expected = config('maintenance.scheduler_token');
    abort_unless(is_string($expected) && $expected !== '' && hash_equals($expected, (string) $request->header('X-Scheduler-Token')), 404);
    SendBookingReminders::dispatchSync();
    ScheduledMaintenance::purgeIfDue();

    return response()->noContent();
})->middleware('throttle:scheduler')->name('scheduler.run');

/*
|--------------------------------------------------------------------------
| Tenant routes: rezervuj-ma.online/{slug}/…
|--------------------------------------------------------------------------
| ResolveTenant makes the tenant current and registers `tenant` as a URL
| default, so route('home') etc. keep working unchanged inside this group.
*/
Route::prefix('{tenant}')
    ->where(['tenant' => config('tenancy.slug_pattern')])
    ->middleware(['tenant', 'locale'])
    ->group(function () {
        // Public profile: what search engines and the directory link to.
        Route::get('/', [TenantProfileController::class, 'show'])->name('tenant.profile');

        Route::get('/booking', [BookingController::class, 'home'])->name('home');
        Route::get('/ochrana-osobnych-udajov', [TenantLegalController::class, 'privacy'])->name('privacy');
        Route::get('/podmienky-rezervacie', [TenantLegalController::class, 'terms'])->name('terms');
        Route::get('/widget.js', [BookingController::class, 'widgetScript'])->name('widget.script');

        Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');
        Route::get('/api/languages/available', [LanguageController::class, 'available'])->name('api.languages.available');
        Route::get('/api/languages/all', [LanguageController::class, 'all'])->name('api.languages.all');

        Route::get('/booking-confirmation/{booking}', [BookingController::class, 'confirmation'])->middleware('signed')->name('booking.confirmation');
        Route::get('/booking/{booking}/calendar.ics', [BookingController::class, 'downloadIcs'])->middleware('signed')->name('booking.ics');
        Route::get('/booking/{booking}/cancel', [BookingController::class, 'showCancellation'])->middleware('signed')->name('booking.cancel.show');
        Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancelByCustomer'])->middleware(['signed', 'throttle:cancel'])->name('booking.cancel');

        Route::get('/login', fn () => view('auth.login'))->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.submit');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
        Route::get('/2fa/challenge', [AuthController::class, 'showTwoFactorChallenge'])->name('2fa.challenge');
        Route::post('/2fa/challenge', [AuthController::class, 'verifyTwoFactor'])->middleware('throttle:otp')->name('2fa.verify');
        Route::post('/2fa/resend', [AuthController::class, 'resendOtp'])->middleware('throttle:otp-resend')->name('2fa.resend');

        Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
        Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:password')->name('password.email');
        Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
        Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:password')->name('password.update');

        Route::middleware('auth')->group(function () {
            Route::get('/account/two-factor', [TwoFactorController::class, 'show'])->name('account.2fa.show');
            Route::post('/account/two-factor/enable', [TwoFactorController::class, 'enable'])->name('account.2fa.enable');
            Route::post('/account/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('account.2fa.confirm');
            Route::post('/account/two-factor/disable', [TwoFactorController::class, 'disable'])->name('account.2fa.disable');
        });

        Route::post('/booking', [BookingController::class, 'store'])->middleware(['antibot', 'throttle:booking', 'blacklist'])->name('booking.store');
        Route::post('/bug-report', [BugReportController::class, 'store'])->middleware(['antibot', 'throttle:bug-report'])->name('bug-reports.store');

        Route::get('/web/cities', [WorkerController::class, 'getCitiesWeb'])->name('web.cities');
        Route::get('/web/categories/{cityId?}', [CategoryController::class, 'webCategories'])->name('web.categories');
        Route::get('/web/procedures/{categoryId}', [ServiceController::class, 'getProceduresWeb'])->name('web.procedures');
        Route::get('/web/workers/{categoryId}', [WorkerController::class, 'getWorkersWeb'])->name('web.workers');
        Route::get('/web/dates/{workerId}', [BookingController::class, 'getAvaiableDatesWeb'])->name('web.dates');
        Route::get('/web/times/{workerId}/{date}', [BookingController::class, 'getAvailableTimesWeb'])->name('web.times');
        Route::post('/web/hold', [BookingController::class, 'holdSlot'])->middleware(['antibot', 'throttle:hold'])->name('web.hold');
        Route::get('/web/theme-colors', [AdminController::class, 'getThemeColors'])->name('web.theme.colors');

        Route::middleware(['auth', 'role:worker'])->prefix('worker')->name('worker.')->group(function () {
            Route::get('/dashboard', [WorkerDashboardController::class, 'index'])->name('dashboard');
            Route::get('/calendar', [WorkerDashboardController::class, 'calendar'])->name('calendar');
            Route::get('/calendar/feed', [CalendarController::class, 'feed'])->name('calendar.feed');
            Route::post('/calendar/blocks', [CalendarController::class, 'storeBlock'])->name('calendar.blocks.store');
            Route::patch('/calendar/blocks/{block}', [CalendarController::class, 'updateBlock'])->name('calendar.blocks.update');
            Route::delete('/calendar/blocks/{block}', [CalendarController::class, 'destroyBlock'])->name('calendar.blocks.destroy');
            Route::get('/bookings', [WorkerDashboardController::class, 'bookings'])->name('bookings');
            Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
            Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
            Route::patch('/bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
            Route::get('/availability', [WorkerAvailabilityController::class, 'workerIndex'])->name('availability');
            Route::post('/availability', [WorkerAvailabilityController::class, 'workerStore'])->name('availability.store');
            Route::get('/availability/{availability}/edit', [WorkerAvailabilityController::class, 'workerEdit'])->name('availability.edit');
            Route::put('/availability/{availability}', [WorkerAvailabilityController::class, 'workerUpdate'])->name('availability.update');
            Route::delete('/availability/{availability}', [WorkerAvailabilityController::class, 'workerDestroy'])->name('availability.destroy');
        });

        Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/workers', [WorkerController::class, 'adminIndex'])->name('workers');
            Route::get('/workers/create', [WorkerController::class, 'create'])->name('workers.create');
            Route::post('/workers', [WorkerController::class, 'store'])->name('workers.store');
            Route::get('/workers/{worker}/edit', [WorkerController::class, 'edit'])->name('workers.edit');
            Route::put('/workers/{worker}', [WorkerController::class, 'update'])->name('workers.update');
            Route::delete('/workers/{worker}', [WorkerController::class, 'delete'])->name('workers.delete');
            Route::post('/workers/{worker}/update-color', [WorkerController::class, 'updateColor'])->name('workers.updateColor');
            Route::get('/categories', [CategoryController::class, 'adminIndex'])->name('categories');
            Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
            Route::get('/bookings', [BookingController::class, 'adminIndex'])->name('bookings.index');
            Route::get('/bookings/calendar-data', [CalendarController::class, 'feed'])->name('bookings.calendar-data');
            Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
            Route::patch('/bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
            Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
            Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
            Route::post('/calendar/blocks', [CalendarController::class, 'storeBlock'])->name('calendar.blocks.store');
            Route::patch('/calendar/blocks/{block}', [CalendarController::class, 'updateBlock'])->name('calendar.blocks.update');
            Route::delete('/calendar/blocks/{block}', [CalendarController::class, 'destroyBlock'])->name('calendar.blocks.destroy');
            Route::get('/services', [ServiceController::class, 'adminIndex'])->name('services');
            Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
            Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
            Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
            Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
            Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
            Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
            Route::get('/cities/create', [CityController::class, 'create'])->name('cities.create');
            Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
            Route::get('/cities/{city}/edit', [CityController::class, 'edit'])->name('cities.edit');
            Route::put('/cities/{city}', [CityController::class, 'update'])->name('cities.update');
            Route::delete('/cities/{city}', [CityController::class, 'destroy'])->name('cities.destroy');
            Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
            Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
            Route::post('/customers/email', [CustomerController::class, 'sendEmails'])->name('customers.email');
            Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
            Route::get('/blacklist', [BlacklistController::class, 'index'])->name('blacklist.index');
            Route::get('/blacklist/create', [BlacklistController::class, 'create'])->name('blacklist.create');
            Route::post('/blacklist', [BlacklistController::class, 'store'])->name('blacklist.store');
            Route::get('/blacklist/{blacklist}/edit', [BlacklistController::class, 'edit'])->name('blacklist.edit');
            Route::put('/blacklist/{blacklist}', [BlacklistController::class, 'update'])->name('blacklist.update');
            Route::delete('/blacklist/{blacklist}', [BlacklistController::class, 'destroy'])->name('blacklist.destroy');
            Route::post('/blacklist/{blacklist}/toggle-status', [BlacklistController::class, 'toggleStatus'])->name('blacklist.toggle-status');
            Route::get('/blacklist/check', [BlacklistController::class, 'check'])->name('blacklist.check');
            Route::get('/blacklist/customer-status/{email}', [BlacklistController::class, 'customerStatus'])->name('blacklist.customer-status');
        });

        Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/workers/{worker}/availability', [WorkerAvailabilityController::class, 'index'])->name('workers.availability');
            Route::post('/worker-availability', [WorkerAvailabilityController::class, 'store'])->name('workers.availability.store');
            Route::get('/worker-availability/{availability}/edit', [WorkerAvailabilityController::class, 'edit'])->name('workers.availability.edit');
            Route::put('/worker-availability/{availability}', [WorkerAvailabilityController::class, 'update'])->name('workers.availability.update');
            Route::delete('/worker-availability/{availability}', [WorkerAvailabilityController::class, 'destroy'])->name('workers.availability.destroy');
            Route::get('/api/workers/{worker}/availability-check', [WorkerAvailabilityController::class, 'checkAvailability'])->name('workers.availability.check');
            Route::get('/account/export', [AccountController::class, 'export'])->name('account.export');
            Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
            Route::get('/billing', [BillingController::class, 'index'])->name('billing');
            Route::post('/billing/activate', [BillingController::class, 'activate'])->name('billing.activate');
            Route::post('/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
            Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
            Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
            Route::get('/business-settings', [BusinessSettingController::class, 'index'])->name('business-settings');
            Route::put('/business-settings', [BusinessSettingController::class, 'update'])->name('business-settings.update');
            Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
            Route::put('/email-templates', [EmailTemplateController::class, 'update'])->name('email-templates.update');
            Route::get('/email-templates/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');
            Route::post('/email-templates/reset', [EmailTemplateController::class, 'reset'])->name('email-templates.reset');
        });
    });
