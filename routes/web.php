<?php

Route::get('/', 'PagesController@root')->name('root');

Auth::routes(['verify' => true]);

// auth 中間件代表需要登入，verified中間件代表需要經過電子郵件驗證
Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');
    Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
    Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');
});
