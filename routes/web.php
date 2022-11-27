<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentsController;
use App\Http\Controllers\Admin\DesignationsController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TypesController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\PurchaseCodeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['installed']], function () {
    Auth::routes(['verify' => false]);
});
Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'middleware' => ['web', 'install']], function () {
    Route::post('environment/saveWizard', [
        'as'   => 'environmentSaveWizard',
        'uses' => [EnvironmentController::class,'saveWizard'],
    ]);

    Route::get('purchase-code', [
        'as'   => 'purchase_code',
        'uses' => [PurchaseCodeController::class,'index'],
    ]);

    Route::post('purchase-code', [
        'as'   => 'purchase_code.check',
        // 'uses' => 'PurchaseCodeController@action',
        'uses' =>  [PurchaseCodeController::class,'action'],
    ]);
});

Route::redirect('/index.php/', '/index.php/admin/dashboard')->middleware('backend_permission');
Route::redirect('/admin', '/index.php/admin/dashboard')->middleware('backend_permission');

Route::group(['prefix' => 'admin', 'middleware' => ['installed'], 'namespace' => 'Admin', 'as' => 'admin.'], function () {
    // Route::get('login', 'Auth\LoginController@showLoginForm');
    Route::get('login', [LoginController::class,'showLoginForm']);
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'installed','backend_permission'], 'namespace' => 'Admin', 'as' => 'admin.'], function () {

    Route::get('dashboard', [DashboardController::class,'index'])->name('dashboard.index');

    Route::get('profile', [ProfileController::class,'index'])->name('profile');
    Route::put('profile/update/{profile}', [ProfileController::class,'update'])->name('profile.update');
    Route::put('profile/change', [ProfileController::class,'change'])->name('profile.change');
    Route::resource('adminusers', AdminUserController::class);
    Route::get('get-adminusers', [AdminUserController::class,'getAdminUsers'])->name('adminusers.get-adminusers');
    Route::resource('role', RoleController::class);
    Route::post('role/save-permission/{id}', [RoleController::class,'savePermission'])->name('role.save-permission');

    //designations
    Route::resource('designations', DesignationsController::class);
    Route::get('get-designations', [DesignationsController::class,'getDesignations'])->name('designations.get-designations');
	
	//types
    Route::resource('types', TypesController::class);
    Route::get('get-types', [TypesController::class,'getTypes'])->name('types.get-types');

    //departments
    Route::resource('departments', DepartmentsController::class);
    Route::get('get-departments', [DepartmentsController::class,'getDepartments'])->name('departments.get-departments');

    //employee route
    Route::resource('employees', EmployeeController::class);
    Route::get('get-employees', 'EmployeeController@getEmployees')->name('employees.get-employees');
    Route::get('employees/get-pre-registers/{id}', 'EmployeeController@getPreRegister')->name('employees.get-pre-registers');
    Route::get('employees/get-visitors/{id}', 'EmployeeController@getVisitor')->name('employees.get-visitors');
    Route::put('employees/check/{id}','EmployeeController@checkEmployee')->name('employees.check');

    //pre-registers
    Route::resource('pre-registers', 'PreRegisterController');
    Route::get('get-pre-registers', 'PreRegisterController@getPreRegister')->name('pre-registers.get-pre-registers');

    //visitors
    Route::resource('visitors', 'VisitorController');
    Route::get('get-visitors', 'VisitorController@getVisitor')->name('visitors.get-visitors');

    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {

        Route::get('/', 'SettingController@index')->name('index');
        Route::post('/', 'SettingController@siteSettingUpdate')->name('site-update');
        Route::get('sms', 'SettingController@smsSetting')->name('sms');
        Route::post('sms', 'SettingController@smsSettingUpdate')->name('sms-update');
        Route::get('email', 'SettingController@emailSetting')->name('email');
        Route::post('email', 'SettingController@emailSettingUpdate')->name('email-update');
        Route::get('notification', 'SettingController@notificationSetting')->name('notification');
        Route::post('notification', 'SettingController@notificationSettingUpdate')->name('notification-update');
        Route::get('emailtemplate', 'SettingController@emailTemplateSetting')->name('email-template');
        Route::post('emailtemplate', 'SettingController@mailTemplateSettingUpdate')->name('email-template-update');
        Route::get('homepage', 'SettingController@homepageSetting')->name('homepage');
        Route::post('homepage', 'SettingController@homepageSettingUpdate')->name('homepage-update');
    });


});



/*Multi step form*/

Route::group(['middleware' => ['installed']], function () {
    Route::group(['middleware' => ['frontend']], function () {
        Route::get('/', 'CheckInController@index')->name('/');

        Route::get('/check-in', [
            'as' => 'check-in',
            'uses' => 'CheckInController@index'
        ]);

        Route::get('/check-in/create-step-one', [
            'as' => 'check-in.step-one',
            'uses' => 'CheckInController@createStepOne'
        ]);
        Route::post('/check-in/create-step-one', [
            'as' => 'check-in.step-one.next',
            'uses' => 'CheckInController@postCreateStepOne'
        ]);

        Route::get('/check-in/create-step-two', [
            'as' => 'check-in.step-two',
            'uses' => 'CheckInController@createStepTwo'
        ]);
        Route::post('/check-in/create-step-two', [
            'as' => 'check-in.step-two.next',
            'uses' => 'CheckInController@store'
        ]);

        Route::get('/check-in/show/{id}', [
            'as' => 'check-in.show',
            'uses' => 'CheckInController@show'
        ]);
        Route::get('/check-in/return', [
            'as' => 'check-in.return',
            'uses' => 'CheckInController@visitor_return'
        ]);
        Route::post('/check-in/return', [
            'as' => 'check-in.find.visitor',
            'uses' => 'CheckInController@find_visitor'
        ]);

        Route::get('/check-in/pre-registered', [
            'as' => 'check-in.pre.registered',
            'uses' => 'CheckInController@pre_registered'
        ]);
        Route::post('/check-in/pre-registered', [
            'as' => 'check-in.find.pre.visitor',
            'uses' => 'CheckInController@find_pre_visitor'
        ]);
    });
});

