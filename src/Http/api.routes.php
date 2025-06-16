<?php

Route::prefix('communication')->group(
    function () {
        Route::prefix('channels')->group(
            function () {
                Route::get('/', 'Channels\ChannelsController@index');
                Route::get('/actions', 'Channels\ChannelsController@getActions');

                Route::get('{communication_channels}/tags ', 'Channels\ChannelsController@tags');
                Route::post('{communication_channels}/tags ', 'Channels\ChannelsController@saveTags');
                Route::get('{communication_channels}/addresses ', 'Channels\ChannelsController@addresses');
                Route::post('{communication_channels}/addresses ', 'Channels\ChannelsController@saveAddresses');

                Route::get('/{communication_channels}/{subObjects}', 'Channels\ChannelsController@relatedObjects');
                Route::get('/{communication_channels}', 'Channels\ChannelsController@show');

                Route::post('/', 'Channels\ChannelsController@store');
                Route::post('/{communication_channels}/do/{action}', 'Channels\ChannelsController@doAction');

                Route::patch('/{communication_channels}', 'Channels\ChannelsController@update');
                Route::delete('/{communication_channels}', 'Channels\ChannelsController@destroy');
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

        Route::prefix('available-channels')->group(
            function () {
                Route::get('/', 'AvailableChannels\AvailableChannelsController@index');
                Route::get('/actions', 'AvailableChannels\AvailableChannelsController@getActions');

                Route::get('{communication_available_channels}/tags ', 'AvailableChannels\AvailableChannelsController@tags');
                Route::post('{communication_available_channels}/tags ', 'AvailableChannels\AvailableChannelsController@saveTags');
                Route::get('{communication_available_channels}/addresses ', 'AvailableChannels\AvailableChannelsController@addresses');
                Route::post('{communication_available_channels}/addresses ', 'AvailableChannels\AvailableChannelsController@saveAddresses');

                Route::get('/{communication_available_channels}/{subObjects}', 'AvailableChannels\AvailableChannelsController@relatedObjects');
                Route::get('/{communication_available_channels}', 'AvailableChannels\AvailableChannelsController@show');

                Route::post('/', 'AvailableChannels\AvailableChannelsController@store');
                Route::post('/{communication_available_channels}/do/{action}', 'AvailableChannels\AvailableChannelsController@doAction');

                Route::patch('/{communication_available_channels}', 'AvailableChannels\AvailableChannelsController@update');
                Route::delete('/{communication_available_channels}', 'AvailableChannels\AvailableChannelsController@destroy');
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

        // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE





















        Route::prefix('channels')->group(
            function () {
                Route::post('/{communication_channels}/code/send', 'Channels\ChannelsController@sendCode');
                Route::post('/{communication_channels}/code/verify', 'Channels\ChannelsController@verifyCode');
            }
        );

































































































































































    }
);




























