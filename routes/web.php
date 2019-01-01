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



Auth::routes();
Route::get('/', function () { 
    return redirect('forms/create');
})->middleware('auth');

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::prefix('/dashboard')->group(function () {
	Route::get('/', 'DashboardController@index')->name('dashboard');
	Route::get('/change-password', 'ProfileController@changePasswordForm')->name('change-password-form');
	Route::post('/change-password', 'ProfileController@changePassword')->name('change-password');
	Route::get('/profile', 'ProfileController@index')->name('profile');
	Route::post('/profile', 'ProfileController@update')->name('update-profile');

	Route::get('/change-summary', 'DashboardController@changeSummary')->name('change-summary');
	Route::get('/change-summary/{id}', 'DashboardController@changeSummaryDetail')->name('change-summary-detail');
	Route::post('/change-summary/{id}', 'DashboardController@editSummaryDetail')->name('edit-summary-detail');
	Route::post('/remove-file', 'DashboardController@removeUploadedFile')->name('remove-image');
	Route::get('/forward-schedule', 'DashboardController@forwardSchedule')->name('forward-schedule');
	Route::get('/import-export', 'DashboardController@importExport')->name('import-export');
	Route::get( '/approve-reject', 'DashboardController@showApproveRejectForm' )->name('approve-reject');

	Route::get('/form-approved', 'FormController@form_approved')->name( 'form-approved' );
	Route::get('/form-rejected', 'FormController@form_rejected')->name( 'form-rejected' );

	Route::get('/complete-change', 'DashboardController@completeChange')->name('complete-change');
	//Route::post('/save-complete-change', 'FormController@storeCompleteChange'); // will always work
	//Route::post('/save-complete-change', 'DashboardController@saveCompleteChange')->name('save-complete-change');
});

/*
 * check here https://laravel.com/docs/5.5/controllers#resource-controllers
 * you don't need to add new route for view, add, update, delete users
 */
Route::resource('users', 'UserController')->middleware('auth');
Route::post('/users/approveuser/{id}', 'UserController@approveUser')->name('approve-user');
Route::get('/users/approveuser/{id}', 'UserController@approveUser')->name('approve-user-by-mail');
Route::resource('forms', 'FormController')->middleware('auth');
Route::prefix('/forms')->group(function () {
	Route::post('export-csv', 'FormController@exportCSV')->name('export-csv');
	Route::post('import-csv', 'FormController@importCSV')->name('import-csv');
	Route::post('store-complete-change', 'FormController@storeCompleteChangeForm')->name('store-complete-change');
});

Route::resource('availablecauserforms', 'AvailableCaUserController')->middleware('auth');
Route::resource('completechange', 'CompleteChangeFormController')->middleware('auth');

Route::prefix('/cron')->group(function () {
Route::get('/send-email-asking-cruser-to-complete-their-change-request', 'CronController@sendEmailAskingCrUserToCompleteTheirChangeRequest')->name('send-email-asking-cruser-to-complete-their-change-request');
Route::get('/send-email-to-CA-once-a-week', 'CronController@sendEmailtoCAonceaweek')->name('send-email-to-CA-once-a-week');
});