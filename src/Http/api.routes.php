<?php

Route::prefix('communication')->group(
    function () {
        Route::prefix('notifications')->group(
            function () {
                Route::get('/', 'Notifications\NotificationsController@index');
                Route::get('/actions', 'Notifications\NotificationsController@getActions');

                Route::get('{communication_notifications}/tags ', 'Notifications\NotificationsController@tags');
                Route::post('{communication_notifications}/tags ', 'Notifications\NotificationsController@saveTags');
                Route::get('{communication_notifications}/addresses ', 'Notifications\NotificationsController@addresses');
                Route::post('{communication_notifications}/addresses ', 'Notifications\NotificationsController@saveAddresses');

                Route::get('/{communication_notifications}/{subObjects}', 'Notifications\NotificationsController@relatedObjects');
                Route::get('/{communication_notifications}', 'Notifications\NotificationsController@show');

                Route::post('/', 'Notifications\NotificationsController@store');
                Route::post('/{communication_notifications}/do/{action}', 'Notifications\NotificationsController@doAction');

                Route::patch('/{communication_notifications}', 'Notifications\NotificationsController@update');
                Route::delete('/{communication_notifications}', 'Notifications\NotificationsController@destroy');
            }
        );

        Route::prefix('user-preferences')->group(
            function () {
                Route::get('/', 'UserPreferences\UserPreferencesController@index');
                Route::get('/actions', 'UserPreferences\UserPreferencesController@getActions');

                Route::get('{communication_user_preferences}/tags ', 'UserPreferences\UserPreferencesController@tags');
                Route::post('{communication_user_preferences}/tags ', 'UserPreferences\UserPreferencesController@saveTags');
                Route::get('{communication_user_preferences}/addresses ', 'UserPreferences\UserPreferencesController@addresses');
                Route::post('{communication_user_preferences}/addresses ', 'UserPreferences\UserPreferencesController@saveAddresses');

                Route::get('/{communication_user_preferences}/{subObjects}', 'UserPreferences\UserPreferencesController@relatedObjects');
                Route::get('/{communication_user_preferences}', 'UserPreferences\UserPreferencesController@show');

                Route::post('/', 'UserPreferences\UserPreferencesController@store');
                Route::post('/{communication_user_preferences}/do/{action}', 'UserPreferences\UserPreferencesController@doAction');

                Route::patch('/{communication_user_preferences}', 'UserPreferences\UserPreferencesController@update');
                Route::delete('/{communication_user_preferences}', 'UserPreferences\UserPreferencesController@destroy');
            }
        );

        Route::prefix('remindables')->group(
            function () {
                Route::get('/', 'Remindables\RemindablesController@index');
                Route::get('/actions', 'Remindables\RemindablesController@getActions');

                Route::get('{communication_remindables}/tags ', 'Remindables\RemindablesController@tags');
                Route::post('{communication_remindables}/tags ', 'Remindables\RemindablesController@saveTags');
                Route::get('{communication_remindables}/addresses ', 'Remindables\RemindablesController@addresses');
                Route::post('{communication_remindables}/addresses ', 'Remindables\RemindablesController@saveAddresses');

                Route::get('/{communication_remindables}/{subObjects}', 'Remindables\RemindablesController@relatedObjects');
                Route::get('/{communication_remindables}', 'Remindables\RemindablesController@show');

                Route::post('/', 'Remindables\RemindablesController@store');
                Route::post('/{communication_remindables}/do/{action}', 'Remindables\RemindablesController@doAction');

                Route::patch('/{communication_remindables}', 'Remindables\RemindablesController@update');
                Route::delete('/{communication_remindables}', 'Remindables\RemindablesController@destroy');
            }
        );

        Route::prefix('emails')->group(
            function () {
                Route::get('/', 'Emails\EmailsController@index');
                Route::get('/actions', 'Emails\EmailsController@getActions');

                Route::get('{communication_emails}/tags ', 'Emails\EmailsController@tags');
                Route::post('{communication_emails}/tags ', 'Emails\EmailsController@saveTags');
                Route::get('{communication_emails}/addresses ', 'Emails\EmailsController@addresses');
                Route::post('{communication_emails}/addresses ', 'Emails\EmailsController@saveAddresses');

                Route::get('/{communication_emails}/{subObjects}', 'Emails\EmailsController@relatedObjects');
                Route::get('/{communication_emails}', 'Emails\EmailsController@show');

                Route::post('/', 'Emails\EmailsController@store');
                Route::post('/{communication_emails}/do/{action}', 'Emails\EmailsController@doAction');

                Route::patch('/{communication_emails}', 'Emails\EmailsController@update');
                Route::delete('/{communication_emails}', 'Emails\EmailsController@destroy');
            }
        );

        Route::prefix('bot-codes')->group(
            function () {
                Route::get('/', 'BotCodes\BotCodesController@index');
                Route::get('/actions', 'BotCodes\BotCodesController@getActions');

                Route::get('{communication_bot_codes}/tags ', 'BotCodes\BotCodesController@tags');
                Route::post('{communication_bot_codes}/tags ', 'BotCodes\BotCodesController@saveTags');
                Route::get('{communication_bot_codes}/addresses ', 'BotCodes\BotCodesController@addresses');
                Route::post('{communication_bot_codes}/addresses ', 'BotCodes\BotCodesController@saveAddresses');

                Route::get('/{communication_bot_codes}/{subObjects}', 'BotCodes\BotCodesController@relatedObjects');
                Route::get('/{communication_bot_codes}', 'BotCodes\BotCodesController@show');

                Route::post('/', 'BotCodes\BotCodesController@store');
                Route::post('/{communication_bot_codes}/do/{action}', 'BotCodes\BotCodesController@doAction');

                Route::patch('/{communication_bot_codes}', 'BotCodes\BotCodesController@update');
                Route::delete('/{communication_bot_codes}', 'BotCodes\BotCodesController@destroy');
            }
        );

        Route::prefix('bot-users')->group(
            function () {
                Route::get('/', 'BotUsers\BotUsersController@index');
                Route::get('/actions', 'BotUsers\BotUsersController@getActions');

                Route::get('{communication_bot_users}/tags ', 'BotUsers\BotUsersController@tags');
                Route::post('{communication_bot_users}/tags ', 'BotUsers\BotUsersController@saveTags');
                Route::get('{communication_bot_users}/addresses ', 'BotUsers\BotUsersController@addresses');
                Route::post('{communication_bot_users}/addresses ', 'BotUsers\BotUsersController@saveAddresses');

                Route::get('/{communication_bot_users}/{subObjects}', 'BotUsers\BotUsersController@relatedObjects');
                Route::get('/{communication_bot_users}', 'BotUsers\BotUsersController@show');

                Route::post('/', 'BotUsers\BotUsersController@store');
                Route::post('/{communication_bot_users}/do/{action}', 'BotUsers\BotUsersController@doAction');

                Route::patch('/{communication_bot_users}', 'BotUsers\BotUsersController@update');
                Route::delete('/{communication_bot_users}', 'BotUsers\BotUsersController@destroy');
            }
        );

        Route::prefix('users')->group(
            function () {
                Route::get('/', 'Users\UsersController@index');
                Route::get('/actions', 'Users\UsersController@getActions');

                Route::get('{communication_users}/tags ', 'Users\UsersController@tags');
                Route::post('{communication_users}/tags ', 'Users\UsersController@saveTags');
                Route::get('{communication_users}/addresses ', 'Users\UsersController@addresses');
                Route::post('{communication_users}/addresses ', 'Users\UsersController@saveAddresses');

                Route::get('/{communication_users}/{subObjects}', 'Users\UsersController@relatedObjects');
                Route::get('/{communication_users}', 'Users\UsersController@show');

                Route::post('/', 'Users\UsersController@store');
                Route::post('/{communication_users}/do/{action}', 'Users\UsersController@doAction');

                Route::patch('/{communication_users}', 'Users\UsersController@update');
                Route::delete('/{communication_users}', 'Users\UsersController@destroy');
            }
        );

        Route::prefix('conversations')->group(
            function () {
                Route::get('/', 'Conversations\ConversationsController@index');
                Route::get('/actions', 'Conversations\ConversationsController@getActions');

                Route::get('{communication_conversations}/tags ', 'Conversations\ConversationsController@tags');
                Route::post('{communication_conversations}/tags ', 'Conversations\ConversationsController@saveTags');
                Route::get('{communication_conversations}/addresses ', 'Conversations\ConversationsController@addresses');
                Route::post('{communication_conversations}/addresses ', 'Conversations\ConversationsController@saveAddresses');

                Route::get('/{communication_conversations}/{subObjects}', 'Conversations\ConversationsController@relatedObjects');
                Route::get('/{communication_conversations}', 'Conversations\ConversationsController@show');

                Route::post('/', 'Conversations\ConversationsController@store');
                Route::post('/{communication_conversations}/do/{action}', 'Conversations\ConversationsController@doAction');

                Route::patch('/{communication_conversations}', 'Conversations\ConversationsController@update');
                Route::delete('/{communication_conversations}', 'Conversations\ConversationsController@destroy');
            }
        );

        Route::prefix('bots')->group(
            function () {
                Route::get('/', 'Bots\BotsController@index');
                Route::get('/actions', 'Bots\BotsController@getActions');

                Route::get('{communication_bots}/tags ', 'Bots\BotsController@tags');
                Route::post('{communication_bots}/tags ', 'Bots\BotsController@saveTags');
                Route::get('{communication_bots}/addresses ', 'Bots\BotsController@addresses');
                Route::post('{communication_bots}/addresses ', 'Bots\BotsController@saveAddresses');

                Route::get('/{communication_bots}/{subObjects}', 'Bots\BotsController@relatedObjects');
                Route::get('/{communication_bots}', 'Bots\BotsController@show');

                Route::post('/', 'Bots\BotsController@store');
                Route::post('/{communication_bots}/do/{action}', 'Bots\BotsController@doAction');

                Route::patch('/{communication_bots}', 'Bots\BotsController@update');
                Route::delete('/{communication_bots}', 'Bots\BotsController@destroy');
            }
        );

        // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE






































































































































































    }
);



























