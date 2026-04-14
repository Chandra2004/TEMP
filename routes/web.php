<?php
// ROUTER
use TheFramework\App\Router;
use TheFramework\App\View;
use TheFramework\Helpers\Helper;

// MIDDLEWARE
use TheFramework\Middleware\WAFMiddleware;
use TheFramework\Middleware\CsrfMiddleware;
use TheFramework\Middleware\AuthMiddleware;
use TheFramework\Middleware\RoleMiddleware;
use TheFramework\Middleware\PermissionMiddleware;

// CONTROLLER
use TheFramework\Http\Controllers\HomepageController;
use TheFramework\Http\Controllers\AuthController;
use TheFramework\Http\Controllers\CategoryController;
use TheFramework\Http\Controllers\CoachController;
use TheFramework\Http\Controllers\DashboardController;
use TheFramework\Http\Controllers\EventController;
use TheFramework\Http\Controllers\Services\FileController;
use TheFramework\Http\Controllers\GalleryController;
use TheFramework\Http\Controllers\MemberController;
use TheFramework\Http\Controllers\MyProfileController;
use TheFramework\Http\Controllers\NotificationController;
use TheFramework\Http\Controllers\PaymentMethodController;
use TheFramework\Http\Controllers\RegistrationController;
use TheFramework\Http\Controllers\RequirementParameterController;
use TheFramework\Http\Controllers\EventResultController;
use TheFramework\Http\Controllers\UserController;

// COMING SOON
Router::add('GET', '/coming-soon', function () {
    return View::render('layouts.layout-partials.later', [
        'user' => Helper::session_get('user'),
        'notification' => Helper::get_flash('notification'),
        'title' => 'Khafid Swimming Club (KSC) - Official Website | Segera Hadir',
    ]);
});

// GUEST
Router::add('GET', '/', HomepageController::class, 'homepage', [WAFMiddleware::class]);
Router::add('GET', '/about-us', HomepageController::class, 'aboutUs', [WAFMiddleware::class]);
Router::add('GET', '/facilities', HomepageController::class, 'facilities', [WAFMiddleware::class]);
Router::add('GET', '/coaches', HomepageController::class, 'coaches', [WAFMiddleware::class]);

Router::add('GET', '/events/search/{keyword}', HomepageController::class, 'events', [WAFMiddleware::class]);
Router::add('GET', '/events/search/{keyword}/page/{page}', HomepageController::class, 'events', [WAFMiddleware::class]);
Router::add('GET', '/events', HomepageController::class, 'events', [WAFMiddleware::class]);
Router::add('GET', '/events/page/{page}', HomepageController::class, 'events', [WAFMiddleware::class]);

Router::add('GET', '/detail-event/{slug}/{uid}', HomepageController::class, 'eventDetail', [WAFMiddleware::class]);
Router::add('POST', '/registration/event/create/process', RegistrationController::class, 'registrationCreateProcess', [WAFMiddleware::class, CsrfMiddleware::class, [RoleMiddleware::class, ['admin', 'atlet', 'pelatih', 'superadmin']]]);

Router::add('GET', '/galleries', HomepageController::class, 'galleries', [WAFMiddleware::class]);
Router::add('GET', '/contact', HomepageController::class, 'contact', [WAFMiddleware::class]);
Router::add('POST', '/contact/process', HomepageController::class, 'contactProcess', [WAFMiddleware::class, CsrfMiddleware::class]);

// AUTH
Router::add('GET', '/register', AuthController::class, 'register', [WAFMiddleware::class]);
Router::add('POST', '/register/process', AuthController::class, 'registerProcess', [WAFMiddleware::class, CsrfMiddleware::class]);

Router::add('GET', '/login', AuthController::class, 'login', [WAFMiddleware::class]);
Router::add('POST', '/login/process', AuthController::class, 'loginProcess', [WAFMiddleware::class, CsrfMiddleware::class]);

Router::add('GET', '/forgot-password', AuthController::class, 'forgotPassword', [WAFMiddleware::class]);
Router::add('POST', '/forgot-password/process', AuthController::class, 'forgotPasswordProcess', [WAFMiddleware::class, CsrfMiddleware::class]);

Router::add('GET', '/reset-password/{uid}', AuthController::class, 'resetPassword', [WAFMiddleware::class]);
Router::add('POST', '/reset-password/{uid}/process', AuthController::class, 'resetPasswordProcess', [WAFMiddleware::class, CsrfMiddleware::class]);

Router::group([
    'prefix' => '/{role}',
    'middleware' => [
        AuthMiddleware::class,
        CsrfMiddleware::class,
        WAFMiddleware::class,
    ]
], function () {
    // DASHBOARD
    Router::add('GET', '/dashboard', DashboardController::class, 'dashboard', [[PermissionMiddleware::class, 'view-dashboard']]);

    Router::add('GET', '/dashboard/registration-history', RegistrationController::class, 'registrationHistory', [[PermissionMiddleware::class, 'register-event']]);

    // PAYMENT MANAGEMENT
    Router::add('GET', '/dashboard/management-payment', PaymentMethodController::class, 'paymentMethod', [[PermissionMiddleware::class, 'manage-payments']]);
    Router::add('POST', '/{uidUser}/dashboard/management-payment/create/process', PaymentMethodController::class, 'paymentMethodCreateProcess', [[PermissionMiddleware::class, 'manage-payments']]);
    Router::add('POST', '/{uidUser}/{uidPaymentMethod}/dashboard/management-payment/edit/process', PaymentMethodController::class, 'paymentMethodEditProcess', [[PermissionMiddleware::class, 'manage-payments']]);
    Router::add('POST', '/{uidUser}/{uidPaymentMethod}/dashboard/management-payment/delete/process', PaymentMethodController::class, 'paymentMethodDeleteProcess', [[PermissionMiddleware::class, 'manage-payments']]);

    // CATEGORY MANAGEMENT
    Router::add('GET', '/dashboard/management-category', CategoryController::class, 'category', [[PermissionMiddleware::class, 'manage-categories']]);
    Router::add('GET', '/dashboard/management-category/page/{page}', CategoryController::class, 'category', [[PermissionMiddleware::class, 'manage-categories']]);
    Router::add('POST', '/{uidUser}/dashboard/management-category/create/process', CategoryController::class, 'categoryCreateProcess', [[PermissionMiddleware::class, 'manage-categories']]);
    Router::add('POST', '/{uidUser}/{uidCategory}/dashboard/management-category/edit/process', CategoryController::class, 'categoryEditProcess', [[PermissionMiddleware::class, 'manage-categories']]);
    Router::add('POST', '/{uidUser}/{uidCategory}/dashboard/management-category/delete/process', CategoryController::class, 'categoryDeleteProcess', [[PermissionMiddleware::class, 'manage-categories']]);

    // EVENT MANAGEMENT
    Router::add('GET', '/dashboard/management-event', EventController::class, 'event', [[PermissionMiddleware::class, 'manage-events']]);
    Router::add('GET', '/dashboard/event', EventController::class, 'event', [[PermissionMiddleware::class, 'register-event']]);
    Router::add('GET', '/dashboard/event/page/{page}', EventController::class, 'event', [[PermissionMiddleware::class, 'register-event']]);
    Router::add('GET', '/dashboard/management-event/page/{page}', EventController::class, 'event', [[PermissionMiddleware::class, 'manage-events']]);
    Router::add('POST', '/{uidUser}/dashboard/management-event/create/process', EventController::class, 'eventCreateProcess', [[PermissionMiddleware::class, 'manage-events']]);
    Router::add('POST', '/{uidUser}/{uidEvent}/dashboard/management-event/edit/process', EventController::class, 'eventEditProcess', [[PermissionMiddleware::class, 'manage-events']]);
    Router::add('POST', '/{uidUser}/{uidEvent}/dashboard/management-event/delete/process', EventController::class, 'eventDeleteProcess', [[PermissionMiddleware::class, 'manage-events']]);

    // REGISTRATION MANAGEMENT
    Router::add('GET', '/dashboard/management-registration', RegistrationController::class, 'registration', [[PermissionMiddleware::class, 'manage-registrations']]);
    Router::add('POST', '/{uidUser}/{uidRegistration}/dashboard/management-registration/edit/process', RegistrationController::class, 'registrationEditProcess', [[PermissionMiddleware::class, 'manage-registrations']]);
    Router::add('POST', '/{uidUser}/{uidRegistration}/dashboard/management-registration/delete/process', RegistrationController::class, 'registrationDeleteProcess', [[PermissionMiddleware::class, 'manage-registrations']]);

    // GALLERY MANAGEMENT
    Router::add('GET', '/dashboard/management-gallery', GalleryController::class, 'gallery', [[PermissionMiddleware::class, 'manage-galleries']]);
    Router::add('GET', '/dashboard/management-gallery/page/{page}', GalleryController::class, 'galleryPage', [[PermissionMiddleware::class, 'manage-galleries']]);
    Router::add('POST', '/{uidUser}/dashboard/management-gallery/create/process', GalleryController::class, 'galleryCreateProcess', [[PermissionMiddleware::class, 'manage-galleries']]);
    Router::add('POST', '/{uidUser}/dashboard/management-gallery/{uidGallery}/edit/process', GalleryController::class, 'galleryEditProcess', [[PermissionMiddleware::class, 'manage-galleries']]);
    Router::add('POST', '/{uidUser}/dashboard/management-gallery/{uidGallery}/delete/process', GalleryController::class, 'galleryDeleteProcess', [[PermissionMiddleware::class, 'manage-galleries']]);

    // USER MANAGEMENT
    Router::add('GET', '/dashboard/management-user', UserController::class, 'user', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/dashboard/management-user/create/process', UserController::class, 'userCreateProcess', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/{uidPerson}/dashboard/management-user/edit/process', UserController::class, 'userEditProcess', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/{uidPerson}/dashboard/management-user/delete/process', UserController::class, 'userDeleteProcess', [[PermissionMiddleware::class, 'manage-users']]);

    // PELATIH MANAGEMENT
    Router::add('GET', '/dashboard/management-coach', CoachController::class, 'coach', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/dashboard/management-coach/create/process', CoachController::class, 'coachCreateProcess', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/dashboard/management-coach/{uidCoach}/edit/process', CoachController::class, 'coachEditProcess', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/dashboard/management-coach/{uidCoach}/delete/process', CoachController::class, 'coachDeleteProcess', [[PermissionMiddleware::class, 'manage-users']]);

    // MEMBER MANAGEMENT
    Router::add('GET', '/dashboard/management-member', MemberController::class, 'member', [[PermissionMiddleware::class, 'manage-members']]);
    Router::add('POST', '/{uidUser}/dashboard/management-member/create/process', MemberController::class, 'memberCreateProcess', [[PermissionMiddleware::class, 'manage-members']]);
    Router::add('POST', '/{uidUser}/dashboard/management-member/{uidMember}/edit/process', MemberController::class, 'memberEditProcess', [[PermissionMiddleware::class, 'manage-members']]);
    Router::add('POST', '/{uidUser}/dashboard/management-member/{uidMember}/delete/process', MemberController::class, 'memberDeleteProcess', [[PermissionMiddleware::class, 'manage-members']]);

    // NOTIFICATION
    Router::add('GET', '/dashboard/notifications', NotificationController::class, 'notification', [[PermissionMiddleware::class, 'view-dashboard']]);
    Router::add('POST', '/{uidUser}/dashboard/notifications/create/process', NotificationController::class, 'notificationCreateProcess', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/dashboard/notifications/mark-all-as-read/process', NotificationController::class, 'notificationMarkAllAsReadProcess', [[PermissionMiddleware::class, 'view-dashboard']]);
    Router::add('POST', '/{uidUser}/dashboard/notifications/delete-all/process', NotificationController::class, 'notificationDeleteAllProcess', [[PermissionMiddleware::class, 'view-dashboard']]);
    Router::add('POST', '/{uidUser}/dashboard/notifications/{uidNotification}/edit/process', NotificationController::class, 'notificationEditProcess', [[PermissionMiddleware::class, 'view-dashboard']]);
    Router::add('POST', '/{uidUser}/dashboard/notifications/{uidNotification}/delete/process', NotificationController::class, 'notificationDeleteProcess', [[PermissionMiddleware::class, 'view-dashboard']]);

    // REQUIREMENT PARAMETER MANAGEMENT
    Router::add('GET', '/dashboard/management-requirement-parameter', RequirementParameterController::class, 'index', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/dashboard/management-requirement-parameter/create/process', RequirementParameterController::class, 'createProcess', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/{uidParam}/dashboard/management-requirement-parameter/edit/process', RequirementParameterController::class, 'editProcess', [[PermissionMiddleware::class, 'manage-users']]);
    Router::add('POST', '/{uidUser}/{uidParam}/dashboard/management-requirement-parameter/delete/process', RequirementParameterController::class, 'deleteProcess', [[PermissionMiddleware::class, 'manage-users']]);

    // RESULT MANAGEMENT
    Router::add('GET', '/dashboard/management-result', EventResultController::class, 'index', [[PermissionMiddleware::class, 'manage-events']]);
    Router::add('GET', '/dashboard/management-result/{uidEvent}/categories', EventResultController::class, 'categoryList', [[PermissionMiddleware::class, 'manage-events']]);
    Router::add('GET', '/dashboard/management-result/{uidEvent}/input/{uidEventCategory}', EventResultController::class, 'inputForm', [[PermissionMiddleware::class, 'manage-events']]);
    Router::add('POST', '/dashboard/management-result/{uidEvent}/input/{uidEventCategory}/process', EventResultController::class, 'store', [[PermissionMiddleware::class, 'manage-events']]);

    // REPORT
    Router::add('GET', '/dashboard/export-reports', DashboardController::class, 'report', [[PermissionMiddleware::class, 'view-reports']]);
    Router::add('GET', '/dashboard/export-reports/process', DashboardController::class, 'exportProcess', [[PermissionMiddleware::class, 'view-reports']]);
    Router::add('GET', '/dashboard/management-event/{uidEvent}/export-buku-acara', EventController::class, 'exportBukuAcara', [[PermissionMiddleware::class, 'view-reports']]);
    Router::add('GET', '/dashboard/management-event/{uidEvent}/export-buku-hasil', EventController::class, 'exportBukuHasil', [[PermissionMiddleware::class, 'view-reports']]);

    // MY PROFILE
    Router::add('GET', '/dashboard/my-profile', MyProfileController::class, 'myProfile', [[PermissionMiddleware::class, 'view-dashboard']]);
    Router::add('POST', '/{uidUser}/dashboard/my-profile/edit/process', MyProfileController::class, 'myProfileEditProcess', [[PermissionMiddleware::class, 'view-dashboard']]);

    // LOGOUT
});

Router::add('GET', '/file/{category}/{filename}', FileController::class, 'serve');
Router::add('GET', '/logout', AuthController::class, 'logout', [AuthMiddleware::class]);
