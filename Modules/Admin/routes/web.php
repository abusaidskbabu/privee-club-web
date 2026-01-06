<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\{AdminController, DashboardController, CommonController, ChildrenController, CustomOptionController};

Route::get('/clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return 'Cleared!';
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('admins', AdminController::class)->names('admin');
});

Route::match(['get', 'post'], '/administrator', [AdminController::class, 'adminLogin'])->name('admin.login');
Route::post('update-adminstatus', [AdminController::class, 'updateStatus'])->name('admin.update');

Route::delete('/user/{id}', [AdminController::class, 'destroy'])->name('destroy');

Route::post('/profile-approval/update', [AdminController::class, 'updateProfileApproval'])->name('profileApproval.update');
Route::post('/user/status/update', [AdminController::class, 'updateLoginStatus'])->name('user.status.update');


Route::prefix('admin')->name('admin.')->group(function () {



    Route::middleware('admin.access')->group(function () {
        Route::resource('children', ChildrenController::class);
        Route::resource('custom-options', CustomOptionController::class);


        Route::get('logout', [AdminController::class, 'logout'])->name('logout');
        // through admin
        Route::get('new-user', [DashboardController::class, 'Newuser'])->name('newuser');
        Route::get('denied-user', [DashboardController::class, 'deniedUser'])->name('deniedUser');
        Route::get('rejected-users', [DashboardController::class, 'rejectedUsers'])->name('rejectedUsers');
        Route::get('accepted-user', [DashboardController::class, 'acceptUser'])->name('acceptUser');
        Route::get('rated-in', [DashboardController::class, 'ratedIn'])->name('ratedIn');
        Route::get('rated-out', [DashboardController::class, 'ratedOut'])->name('ratedOut');
        Route::get('rated-out-applicants', [DashboardController::class, 'ratedOutApplicants'])->name('ratedOutApplicants');
        Route::get('active-profile', [DashboardController::class, 'activeProfile'])->name('activeprofile');
        Route::get('non-active-members', [DashboardController::class, 'nonActiveMembers'])->name('nonActiveMembers');


        Route::get('view-user/{id}/{type?}', [DashboardController::class, 'viewUser'])
            ->name('viewuser');
        Route::get('category', [DashboardController::class, 'Catgeory'])->name('catgeories');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('add-category', [DashboardController::class, 'add'])->name('add');

        Route::get('profile-timer', [DashboardController::class, 'timer'])->name('timer');

        Route::post('update-profile-timer', [DashboardController::class, 'timerUpdate'])->name('update-time');

        Route::get('reported-users', [DashboardController::class, 'reportedUsers'])->name('reported.users');
        Route::get('profile-update-request', [DashboardController::class, 'profileUpdateRequest'])->name('profile.update.request');
        Route::post('/image-approve-reject', [DashboardController::class, 'actionApproveReject'])->name('image.approve.reject');
        Route::get('support', [DashboardController::class, 'support'])->name('support');
        Route::get('help-us-do-better', [DashboardController::class, 'helpUsDoBetter'])->name('help.us.do.better');
        Route::post('delete-suggestion', [DashboardController::class, 'deleteSuggestion'])->name('delete.suggestion');
        Route::match(['get', 'post'], 'user-search', [DashboardController::class, 'userSearch'])->name('user.search');
        Route::get('report-reason', [DashboardController::class, 'reportReason'])->name('report.reason');
        Route::match(['get', 'post'], 'add-update-reasons/{id?}', [DashboardController::class, 'addUpdateReasons'])->name('add.update.reasons');
        Route::delete('delete-report-reason/{id}', [DashboardController::class, 'deleteReportReason'])->name('report.reason.delete');
        Route::post('report-update-status', [DashboardController::class, 'updateReportStatus'])->name('report.update.status');
        Route::post('report-delete', [DashboardController::class, 'reportDelete'])->name('report.delete');
        Route::post('support-request-update-status', [DashboardController::class, 'updateSupportRequestStatus'])->name('update.support.request.status');
        Route::post('support-request-delete', [DashboardController::class, 'supportRequestDelete'])->name('support.request.delete');

        Route::match(['get', 'post'], 'privacy/{id?}', [CommonController::class, 'privacy'])->name('privacy');
        Route::match(['get', 'post'], 'term/{id?}', [CommonController::class, 'term'])->name('term');
        Route::match(['get', 'post'], 'change-password', [AdminController::class, 'changePassword'])->name('change.password');

        Route::post('make-member', [AdminController::class, 'makeMember'])->name('make.member');

        // Location Management
        Route::get('location-management', [DashboardController::class, 'locationManagement'])->name('location.management');
        Route::match(['get', 'post'], 'add-update-country/{id?}', [DashboardController::class, 'addUpdateCountry'])->name('add.update.country');
        Route::delete('delete-country/{id}', [DashboardController::class, 'deleteCountry'])->name('country.delete');
        Route::match(['get', 'post'], 'add-update-region/{id?}', [DashboardController::class, 'addUpdateRegion'])->name('add.update.region');
        Route::delete('delete-region/{id}', [DashboardController::class, 'deleteRegion'])->name('region.delete');
        Route::match(['get', 'post'], 'add-update-city/{id?}', [DashboardController::class, 'addUpdateCity'])->name('add.update.city');
        Route::delete('delete-city/{id}', [DashboardController::class, 'deleteCity'])->name('city.delete');

        // Nationalities Management
        Route::get('nationalities-management', [DashboardController::class, 'nationalitiesManagement'])->name('nationalities.management');
        Route::match(['get', 'post'], 'add-update-nationality/{id?}', [DashboardController::class, 'addUpdateNationality'])->name('add.update.nationality');
        Route::delete('delete-nationality/{id}', [DashboardController::class, 'deleteNationality'])->name('nationality.delete');

        // Ready Members Management
        Route::get('ready-members', [DashboardController::class, 'readyMembers'])->name('ready.members');
        Route::match(['get', 'post'], 'add-ready-member', [DashboardController::class, 'addReadyMember'])->name('add.ready.member');
        Route::match(['get', 'post'], 'edit-ready-member/{id}', [DashboardController::class, 'addReadyMember'])->name('edit.ready.member');
        Route::get('view-ready-member/{id}', [DashboardController::class, 'viewReadyMember'])->name('view.ready.member');
        Route::delete('delete-ready-member/{id}', [DashboardController::class, 'deleteReadyMember'])->name('delete.ready.member');
        Route::post('toggle-ready-member-status/{id}', [DashboardController::class, 'toggleReadyMemberStatus'])->name('toggle.ready.member.status');
        Route::post('bulk-action-ready-members', [DashboardController::class, 'bulkActionReadyMembers'])->name('bulk.action.ready.members');
        Route::post('toggle-global-mobile-visibility', [DashboardController::class, 'toggleGlobalMobileVisibility'])->name('toggle.global.mobile.visibility');
        Route::post('upload-ready-member-gallery/{id}', [DashboardController::class, 'uploadReadyMemberGallery'])->name('upload.ready.member.gallery');
        Route::delete('delete-ready-member-image/{id}', [DashboardController::class, 'deleteReadyMemberImage'])->name('delete.ready.member.image');

        // Send Notifications
        Route::match(['get', 'post'], 'send-notification', [DashboardController::class, 'sendNotification'])->name('send.notification');
        Route::post('send-notification-to-users', [DashboardController::class, 'sendNotificationToUsers'])->name('send.notification.users');
        Route::post('send-notification-to-group', [DashboardController::class, 'sendNotificationToGroup'])->name('send.notification.group');
        Route::get('search-users-notification', [DashboardController::class, 'searchUsersForNotification'])->name('search.users.notification');
    });
});
