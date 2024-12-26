<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('user')->group(function () {
     Route::post('/create', 'UserController@register')->name('user.create');
     Route::post('/signin', 'UserController@userSignin')->name('user.signin');
     Route::post('/send-magic-login', 'UserController@sendMagicLogin')->name('user.magic_login');
     Route::post('/magic-login/{token}', 'UserController@attemptMagicLogin')->name('user.attempt_magic_login');
     Route::post('/activate/{token}', 'UserController@activateAccount')->name('user.activate');
     Route::post('/password/send-reset-link', 'PasswordResetController@sendResetLink')->name('user.password-reset.send');
     Route::post('/password-reset', 'PasswordResetController@resetPassword')->name('user.password-reset');
     Route::get('/password/confirm-token/{token}', 'PasswordResetController@confirmToken');

     Route::middleware(['auth:api'])->group(function () {
          Route::get('/builder', 'BuilderController@getData')->middleware(['role:builder']);
          Route::post('/builder', 'BuilderController@updateAsBuilder')->middleware(['role:builder']);
          Route::get('/favorites', 'PropertyController@userFavoritedProperties')->name('user.favorites');
          Route::post('/favorite/{property}', 'PropertyController@favoriteProperty')->name('property.favorite');
          Route::post('/unfavorite/{property}', 'PropertyController@unFavoriteProperty')->name('property.unfavorite');
          Route::post('/update-profile', 'UserController@updateProfile');
          Route::post('/update-password', 'UserController@updatePassword');
          Route::get('/my-profile', 'UserController@me');
     });
});

Route::prefix('user-searches')->group(function () {
     Route::post('/', 'UserSearchesController@store');
     Route::get('/{id}', 'UserSearchesController@find');
     Route::delete('/{id}', 'UserSearchesController@destroy');
     Route::get('/list/{id}', 'UserSearchesController@list');
});

Route::prefix('property')->group(function () {
     Route::post('/schedule-listing', 'PropertyController@scheduleListing')->name('properties.schedule-listing');
     Route::middleware([
          'auth:api', 'role:builder'
     ])->group(function () {
          Route::post('/add', 'PropertyController@storeAsBuilder');
          Route::get('/get_amenities', 'PropertyController@getAmenities');
          Route::get('/get_price_formats', 'PropertyController@getPriceFormats');
          Route::get('/get_statuses', 'PropertyStatusController@getList');
          Route::get('/get_categories', 'CategoryController@getList');
          Route::get('/get_builders', 'BuilderController@getList');
          Route::get('/get_styles', 'StyleController@getList');
          Route::get('/get_neighborhoods', 'BuilderController@dataTreeArea');
     });

     Route::get('/{path?}', 'PropertyController@showSingleProperty')
          ->where('path', '(.*)')
          ->name('property.single');
});

Route::prefix('properties')->group(function () {
     Route::get('/list', 'PropertyController@getProperties')->name('properties.list');
     Route::post('query-info', 'PropertyController@getPropertyQueryInfo')->name('properties.info');
     Route::post('filter', 'PropertyController@filterProperties')->name('properties.filter');
     Route::post('filter-stream', 'PropertyController@streamFilterProperties')->name('properties.filter-stream');
     Route::post('/address-lookup', 'PropertyController@searchByAddress')->name('properties.address_lookup');
     Route::post('/filter/builders', 'PropertyController@filterPropertyBuilders')->name('properties.filter.builders');
});

Route::prefix('flood-zones')->group(function () {
     Route::post('/list', 'FloodZoneController@postFloodZones')->name('flood_zones.list');
     Route::get('/list/{post_id}', 'FloodZoneController@getFloodZones')->name('flood_zones.list-get');
     Route::get('/legends', 'FloodZoneController@getFloodZoneLegends')->name('flood_zones.legends');
});

Route::prefix('school-zones')->group(function () {
     Route::post('/list', 'SchoolZoneController@getSchoolZones')->name('school_zones.list');
     Route::get('/legends', 'SchoolZoneController@getSchoolZoneLegends')->name('school_zones.legends');
});

Route::prefix('polygon')->group(function () {
    Route::get('/schools/{polygonId}', 'PolygonController@getPolygonSchools');
    Route::get('/properties/{polygonSlug}', 'PolygonController@getPolygonProperties');
    Route::get('/points-of-interest/{polygonId}', 'PolygonController@getPolygonPointsOfInterest');
    Route::get('/trunk/{polyId}', 'PolygonController@getPolygonTrunk');
    Route::get('/coordinates/{polyId}', 'PolygonController@getPolygonCoordinates');
    Route::post('/get-list', 'PolygonController@getPolygonList');
    Route::post('/get-list-v2', 'PolygonController@getPolygonListV2');

    Route::get('/{path?}', 'PolygonController@showSingleNeighborhood')
        ->where('path', '(.*)');
});

Route::prefix('polygons')->group(function () {
     Route::get('/list', 'PolygonController@list');
     Route::post('/list-points', 'PolygonController@listViewportPoints');
     Route::post('/geometry', 'PolygonController@getGeometry');
});

Route::prefix('builders')->group(function () {
     Route::get('list', 'BuilderController@getListBuilder')->name('builders.list');
     Route::get('search/{keyword}', 'BuilderController@searchBuilder')->name('search.a-z');
     Route::get('searchText/{keyword}', 'BuilderController@searchBuilderText')->name('search.text');
});

Route::prefix('neighborhood')->group(function () {
     //A-z search neighborhood
     Route::get('/search/{keyword}', 'PolygonController@searchNeighborhood')->name('neighboors.search.a-z');
     // Neighborhood Text search
     Route::get('/searchText/{keyword}/{flatresponse?}', 'PolygonController@searchNeighborhoodText')->name('neighboors.search.text');
});

Route::get('/builder/{slug}', 'BuilderController@showSingleBuilder')->name('builder.single');
Route::get('/builder-logo/{builder_id}', 'BuilderController@getBuilderPrimaryLogo')->name('builder.logo');

// Neighborhood list
Route::get('/neighbors/list', 'PolygonController@getNeighborList')->name('neighboors.list');
Route::get('/home-grid/list', 'PolygonController@getHomeGridList')->name('home-grid.list');

//fetch frontend scripts from admin inserted scripts
Route::get('/fetch/frontend/scripts', 'SettingController@fetchScripts');
Route::post('/report-bug', 'ReportBugController@submit');
Route::get('/settings', 'SettingController@getByName');
