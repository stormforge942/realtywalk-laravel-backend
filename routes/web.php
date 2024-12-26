<?php

use Illuminate\Support\Facades\Route;

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

// Builders Frontend
Route::get('import', 'PropertyController@propertiesImport');

//---front end routes start
Route::view('/', 'builders.frontend');
Route::view('/about', 'builders.frontend');
Route::view('/builders', 'builders.frontend');
Route::view('/neighborhoods', 'builders.frontend');
Route::view('/property/add', 'builders.frontend');
Route::view('/address_lookup', 'builders.frontend');
Route::get("/property/{path?}", "RoutesController@properties")
    ->where('path', '(.*)');

Route::get("/neighborhood/{path?}", "RoutesController@neighborhoods")
    ->where('path', '(.*)');

Route::get('/activate/{token}', 'RoutesController@activateAccount')->name('activation_link');
Route::view('/magic-login/{token}', 'builders.frontend')->name('magic.login');
Route::view('/users/signin', 'builders.frontend');
Route::view('/users/signin-with-password', 'builders.frontend');
Route::view('/users/create', 'builders.frontend');
Route::view('/users/favorites', 'builders.frontend');
Route::view('/users/profile', 'builders.frontend');
Route::view('/users/forgot-password', 'builders.frontend');
Route::view('/user/password-reset/{id}', 'builders.frontend');

Route::view('/report-bug', 'builders.frontend');

//---front end routes end

/* Auth routes */
Route::namespace('Auth')->group(function () {
    // Login
    Route::get('system/login', 'LoginController@showLoginForm')->name('login');
    Route::post('system/login', 'LoginController@login');

    // Logout
    Route::get('system/logout', 'LoginController@logout')->name('logout');

    // Reset Password
    Route::post('system/password/email', 'ForgotPasswordController@sendResetLinkEmail')
        ->name('password.email');
    Route::get('system/password/reset', 'ForgotPasswordController@showLinkRequestForm')
        ->name('password.request');
    Route::post('system/password/reset', 'ForgotPasswordController@reset')
        ->name('password.update');
    Route::get('system/password/reset/{token}', 'ForgotPasswordController@showResetForm')
        ->name('password.reset');

    // Confirm Password
    Route::get('system/password/confirm', 'ConfirmPasswordController@showConfirmForm')
        ->name('password.confirm');
    Route::post('system/password/confirm', 'ConfirmPasswordController@confirm');
});

/* Main Menus */
Route::middleware(['auth', 'has_role:admin|secretary', 'retrieve.menu'])->group(function () {
    // Dashboard
    //Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/system', 'DashboardController@index')->name('dashboard');
    Route::prefix('dashboard')->group(function () {
        Route::get('properties_per_category', 'DashboardController@getPropertiesPerCategory')
            ->name('dashboard.properties_per_category');
        Route::get('properties_per_style', 'DashboardController@getPropertiesPerStyle')
            ->name('dashboard.properties_per_style');
    });

    // Profile
    Route::get('system/profile', 'ProfileController@index')->name('profile.index');
    Route::patch('profile', 'ProfileController@update')->name('profile.update');

    Route::prefix('system')->group(function () {

        // Builder
        Route::prefix('builders')->group(function () {
            Route::get('datatable', 'BuilderController@dataTable')->name('dt.builders');
            Route::get('datatable-unmatched', 'BuilderController@dataTableUnmatchedBuilders')->name('dt.unmatched_builders');
            Route::get('{builder}/aliases', 'BuilderController@aliases')->name('dt.builder-aliases');
            Route::get('dataarea', 'BuilderController@dataTreeArea')->name('dtr.builders.areas');
            Route::get('list', 'BuilderController@getList')->name('list.builders');

            Route::post('similar-to', 'BuilderController@similarToQuery');

            /* Upload image */
            Route::name('builders.image.')->prefix('image')->group(function () {
                Route::post('delete', 'BuilderController@deleteBuilderImages')->name('delete');
                Route::get('fetch', 'BuilderController@fetchBuilderImages')->name('fetch');
                Route::get('logo', 'BuilderController@fetchBuilderLogo')->name('logo');
                Route::get('sort', 'BuilderController@sortBuilderImages')->name('sort');
            });
        });
        Route::resource('builders', 'BuilderController');

        Route::resource('unmatched_builders', 'UnmatchedBuilderController')->only(['index', 'edit', 'update']);

        Route::get('properties/datatable', 'PropertyController@dataTable')->name('dt.properties');

        Route::middleware(['role:admin'])->group(function () {

            // Property
            Route::prefix('properties')->group(function () {
                /* Amenity */
                Route::get('amenities/datatable', 'AmenityController@dataTable')->name('dt.amenities');
                Route::resource('amenities', 'AmenityController');

                /* Property status */
                Route::get('status/datatable', 'PropertyStatusController@dataTable')->name('dt.status');
                Route::get('status/create', 'PropertyStatusController@create')->name('status.create');
                Route::get('status', 'PropertyStatusController@index')->name('status.index');
                Route::get('status/{propertiesstatus}/edit', 'PropertyStatusController@edit')->name('status.edit');
                Route::post('status', 'PropertyStatusController@store')->name('status.store');
                Route::put('status/{propertiesstatus}', 'PropertyStatusController@update')->name('status.update');
                Route::delete('status/{propertiesstatus}', 'PropertyStatusController@destroy')->name('status.destroy');

                /* Category */
                Route::get('categories/datatable', 'CategoryController@dataTable')->name('dt.categories');
                Route::get('categories/list', 'CategoryController@getList')->name('list.categories');
                Route::resource('categories', 'CategoryController');

                /* Style */
                Route::get('styles/datatable', 'StyleController@dataTable')->name('dt.styles');
                Route::get('styles/list', 'StyleController@getList')->name('list.styles');
                Route::resource('styles', 'StyleController');

                /* Upload image */
                Route::name('properties.image.')->prefix('image')->group(function () {
                    Route::post('delete', 'PropertyController@deleteImageUpload')->name('delete');
                    Route::get('fetch', 'PropertyController@fetchPropertyImages')->name('fetch');
                    Route::get('sort', 'PropertyController@sortPropertyImages')->name('sort');
                });
            });

            Route::prefix('polygons')->group(function () {
                /* Zone */
                Route::get('zones/datatable', 'ZoneController@dataTable')->name('dt.zones');
                Route::get('zones/datatree', 'ZoneController@dataTree')->name('dtr.zones');
                Route::resource('zones', 'ZoneController');
                /* Statistic */
                Route::prefix('statistics')->group(function () {
                    Route::get('types/datatable', 'StatisticTypeController@dataTable')->name('dt.statistic_types');
                    Route::resource('types', 'StatisticTypeController');
                    Route::get('datatable', 'StatisticController@dataTable')->name('dt.statistics');
                });
                Route::resource('statistics', 'StatisticController');
                /* Polygon */
                Route::get('datatable', 'PolygonController@dataTable')->name('dt.polygons');
                Route::get('datatree', 'PolygonController@dataTree')->name('dtr.polygons');
                /* Upload image */
                Route::name('polygons.image.')->prefix('image')->group(function () {
                    Route::post('delete', 'PolygonController@deletePolygonImages')->name('delete');
                    Route::get('fetch', 'PolygonController@fetchPolygonImages')->name('fetch');
                    Route::get('sort', 'PolygonController@sortPolygonImages')->name('sort');
                });
            });
            Route::resource('polygons', 'PolygonController');

            // User
            Route::get('users/datatable', 'UserController@dataTable')->name('dt.users');
            Route::resource('users', 'UserController');
            Route::prefix('settings')->group(function () {
                Route::get('/', 'SettingController@index')->name('settings.index');
                Route::patch('/', 'SettingController@updateGeneralSettings')->name('settings.index.update');
                Route::get('scripts', 'SettingController@indexScripts')->name('settings.scripts');
                Route::put('scripts', 'SettingController@updateScripts')->name('settings.update.scripts');
                Route::get('email', 'SettingController@indexEmail')->name('settings.email');
                Route::patch('email', 'SettingController@updateEmailSettings')->name('settings.email.update');
                Route::get('builder', 'SettingController@indexBuilder')->name('settings.builder');
                Route::patch('builder', 'SettingController@updateBuilderSettings')->name('settings.builder.update');
            });
        });

        Route::resource('properties', 'PropertyController');

        Route::get('upload-process-status', 'ProcessController@checkUploadStatus');
    });
});
