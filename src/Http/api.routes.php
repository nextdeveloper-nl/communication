<?php

Route::prefix('communication')->group(function () {
Route::prefix('emails')->group(
    function () {
        Route::get('/', 'Emails\EmailsController@index');

        Route::get('{communication_emails}/tags ', 'Emails\EmailsController@tags');
        Route::post('{communication_emails}/tags ', 'Emails\EmailsController@saveTags');
        Route::get('{communication_emails}/addresses ', 'Emails\EmailsController@addresses');
        Route::post('{communication_emails}/addresses ', 'Emails\EmailsController@saveAddresses');

        Route::get('/{communication_emails}/{subObjects}', 'Emails\EmailsController@relatedObjects');
        Route::get('/{communication_emails}', 'Emails\EmailsController@show');

        Route::post('/', 'Emails\EmailsController@store');
        Route::patch('/{communication_emails}', 'Emails\EmailsController@update');
        Route::delete('/{communication_emails}', 'Emails\EmailsController@destroy');
    }
);

Route::prefix('notifications')->group(
    function () {
        Route::get('/', 'Notifications\NotificationsController@index');

        Route::get('{communication_notifications}/tags ', 'Notifications\NotificationsController@tags');
        Route::post('{communication_notifications}/tags ', 'Notifications\NotificationsController@saveTags');
        Route::get('{communication_notifications}/addresses ', 'Notifications\NotificationsController@addresses');
        Route::post('{communication_notifications}/addresses ', 'Notifications\NotificationsController@saveAddresses');

        Route::get('/{communication_notifications}/{subObjects}', 'Notifications\NotificationsController@relatedObjects');
        Route::get('/{communication_notifications}', 'Notifications\NotificationsController@show');

        Route::post('/', 'Notifications\NotificationsController@store');
        Route::patch('/{communication_notifications}', 'Notifications\NotificationsController@update');
        Route::delete('/{communication_notifications}', 'Notifications\NotificationsController@destroy');
    }
);

Route::prefix('remindables')->group(
    function () {
        Route::get('/', 'Remindables\RemindablesController@index');

        Route::get('{communication_remindables}/tags ', 'Remindables\RemindablesController@tags');
        Route::post('{communication_remindables}/tags ', 'Remindables\RemindablesController@saveTags');
        Route::get('{communication_remindables}/addresses ', 'Remindables\RemindablesController@addresses');
        Route::post('{communication_remindables}/addresses ', 'Remindables\RemindablesController@saveAddresses');

        Route::get('/{communication_remindables}/{subObjects}', 'Remindables\RemindablesController@relatedObjects');
        Route::get('/{communication_remindables}', 'Remindables\RemindablesController@show');

        Route::post('/', 'Remindables\RemindablesController@store');
        Route::patch('/{communication_remindables}', 'Remindables\RemindablesController@update');
        Route::delete('/{communication_remindables}', 'Remindables\RemindablesController@destroy');
    }
);

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE



