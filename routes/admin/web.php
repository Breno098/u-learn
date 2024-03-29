<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\PasswordResetController;
use App\Http\Controllers\Admin\Auth\PasswordSendLinkController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CommonQuestionController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\JobVacancyController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\LiveEventController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\QuizzController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/auth/sign-in');
Route::redirect('/admin', '/admin/auth/sign-in');

Route::middleware('guest:admin')
    ->get('admin/auth/sign-in', [AuthController::class, 'form'])
    ->name('admin.auth.sign-in');

Route::middleware('guest:admin')
    ->post('admin/auth/login', [AuthController::class, 'login'])
    ->name('admin.auth.login');

Route::middleware('guest:admin')
    ->get('admin/auth/forgot-password', [PasswordSendLinkController::class, 'form'])
    ->name('admin.auth.forgot-password-form');

Route::post('admin/auth/forgot-password-send-link', [PasswordSendLinkController::class, 'sendLink'])
    ->name('admin.auth.forgot-password-send-link');

Route::middleware('guest:admin')
    ->post('admin/auth/reset-password', [PasswordResetController::class, 'reset'])
    ->name('admin.auth.password.reset');

Route::middleware('auth:admin')
    ->post('admin/auth/logout', [AuthController::class, 'logout'])
    ->name('admin.auth.logout');

Route::middleware('guest:admin')
    ->get('admin/auth/reset-password/{token}', [PasswordResetController::class, 'form'])
    ->name('admin.password.reset');

Route::middleware('auth:admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function() {
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/notification', [NotificationController::class, 'index'])->name('notification.index');

        Route::post('order/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
        Route::get('order/{order}/situation/edit', [OrderController::class, 'editSituation'])->name('order.situation.edit');
        Route::get('order/{order}/situation', [OrderController::class, 'showSituation'])->name('order.situation.show');

        Route::resource('user', UserController::class);
        Route::post('user/destroy-multiples', [UserController::class, 'destroyMultiples'])->name('user.destroy-multiples');

        Route::resource('role', RoleController::class);
        Route::post('role/destroy-multiples', [RoleController::class, 'destroyMultiples'])->name('role.destroy-multiples');

        Route::resource('student', StudentController::class);
        Route::post('student/destroy-multiples', [StudentController::class, 'destroyMultiples'])->name('student.destroy-multiples');

        Route::resource('certificate', CertificateController::class);
        Route::post('certificate/destroy-multiples', [CertificateController::class, 'destroyMultiples'])->name('certificate.destroy-multiples');

        Route::resource('category', CategoryController::class);
        Route::post('category/destroy-multiples', [CategoryController::class, 'destroyMultiples'])->name('category.destroy-multiples');

        Route::resource('group', GroupController::class);
        Route::post('group/destroy-multiples', [GroupController::class, 'destroyMultiples'])->name('group.destroy-multiples');

        Route::resource('job-vacancy', JobVacancyController::class);
        Route::post('job-vacancy/destroy-multiples', [JobVacancyController::class, 'destroyMultiples'])->name('job-vacancy.destroy-multiples');

        Route::resource('partner', PartnerController::class);
        Route::post('partner/destroy-multiples', [PartnerController::class, 'destroyMultiples'])->name('partner.destroy-multiples');

        Route::resource('campaign', CampaignController::class);
        Route::post('campaign/destroy-multiples', [CampaignController::class, 'destroyMultiples'])->name('campaign.destroy-multiples');

        Route::resource('common-question', CommonQuestionController::class);
        Route::post('common-question/destroy-multiples', [CommonQuestionController::class, 'destroyMultiples'])->name('common-question.destroy-multiples');

        Route::resource('product', ProductController::class);
        Route::post('product/destroy-multiples', [ProductController::class, 'destroyMultiples'])->name('product.destroy-multiples');

        Route::resource('item', ItemController::class);
        Route::post('item/destroy-multiples', [ItemController::class, 'destroyMultiples'])->name('item.destroy-multiples');

        Route::resource('live-event', LiveEventController::class);
        Route::post('live-event/destroy-multiples', [LiveEventController::class, 'destroyMultiples'])->name('live-event.destroy-multiples');

        Route::resource('meeting', MeetingController::class);
        Route::post('meeting/destroy-multiples', [MeetingController::class, 'destroyMultiples'])->name('meeting.destroy-multiples');

        Route::resource('course', CourseController::class);
        Route::post('course/destroy-multiples', [CourseController::class, 'destroyMultiples'])->name('course.destroy-multiples');

        Route::resource('course.module', ModuleController::class);
        Route::post('course/{course}/module/reorder', [ModuleController::class, 'reorder'])->name('course.module.reorder');

        Route::resource('module.lesson', LessonController::class);
        Route::resource('module.exam', ExamController::class);

        Route::resource('quizz', QuizzController::class);
        Route::post('quizz/destroy-multiples', [QuizzController::class, 'destroyMultiples'])->name('quizz.destroy-multiples');

        Route::get('quizz/{content}/linkables/{type}', [QuizzController::class, 'linkables'])->name('quizz.linkables');
    });
