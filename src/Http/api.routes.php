<?php

Route::prefix('communication')->group(
    function () {
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

        Route::prefix('accounts')->group(
            function () {
                Route::get('/', 'Accounts\AccountsController@index');
                Route::get('/actions', 'Accounts\AccountsController@getActions');

                Route::get('{communication_accounts}/tags ', 'Accounts\AccountsController@tags');
                Route::post('{communication_accounts}/tags ', 'Accounts\AccountsController@saveTags');
                Route::get('{communication_accounts}/addresses ', 'Accounts\AccountsController@addresses');
                Route::post('{communication_accounts}/addresses ', 'Accounts\AccountsController@saveAddresses');

                Route::get('/{communication_accounts}/{subObjects}', 'Accounts\AccountsController@relatedObjects');
                Route::get('/{communication_accounts}', 'Accounts\AccountsController@show');

                Route::post('/', 'Accounts\AccountsController@store');
                Route::post('/{communication_accounts}/do/{action}', 'Accounts\AccountsController@doAction');

                Route::patch('/{communication_accounts}', 'Accounts\AccountsController@update');
                Route::delete('/{communication_accounts}', 'Accounts\AccountsController@destroy');
            }
        );

        Route::prefix('smtp-servers')->group(
            function () {
                Route::get('/', 'SmtpServers\SmtpServersController@index');
                Route::get('/actions', 'SmtpServers\SmtpServersController@getActions');

                Route::get('{communication_smtp_servers}/tags ', 'SmtpServers\SmtpServersController@tags');
                Route::post('{communication_smtp_servers}/tags ', 'SmtpServers\SmtpServersController@saveTags');
                Route::get('{communication_smtp_servers}/addresses ', 'SmtpServers\SmtpServersController@addresses');
                Route::post('{communication_smtp_servers}/addresses ', 'SmtpServers\SmtpServersController@saveAddresses');

                Route::get('/{communication_smtp_servers}/{subObjects}', 'SmtpServers\SmtpServersController@relatedObjects');
                Route::get('/{communication_smtp_servers}', 'SmtpServers\SmtpServersController@show');

                Route::post('/', 'SmtpServers\SmtpServersController@store');
                Route::post('/{communication_smtp_servers}/do/{action}', 'SmtpServers\SmtpServersController@doAction');

                Route::patch('/{communication_smtp_servers}', 'SmtpServers\SmtpServersController@update');
                Route::delete('/{communication_smtp_servers}', 'SmtpServers\SmtpServersController@destroy');
            }
        );

        Route::prefix('contact-identifiers')->group(
            function () {
                Route::get('/', 'ContactIdentifiers\ContactIdentifiersController@index');
                Route::get('/actions', 'ContactIdentifiers\ContactIdentifiersController@getActions');

                Route::get('{cci}/tags ', 'ContactIdentifiers\ContactIdentifiersController@tags');
                Route::post('{cci}/tags ', 'ContactIdentifiers\ContactIdentifiersController@saveTags');
                Route::get('{cci}/addresses ', 'ContactIdentifiers\ContactIdentifiersController@addresses');
                Route::post('{cci}/addresses ', 'ContactIdentifiers\ContactIdentifiersController@saveAddresses');

                Route::get('/{cci}/{subObjects}', 'ContactIdentifiers\ContactIdentifiersController@relatedObjects');
                Route::get('/{cci}', 'ContactIdentifiers\ContactIdentifiersController@show');

                Route::post('/', 'ContactIdentifiers\ContactIdentifiersController@store');
                Route::post('/{cci}/do/{action}', 'ContactIdentifiers\ContactIdentifiersController@doAction');

                Route::patch('/{cci}', 'ContactIdentifiers\ContactIdentifiersController@update');
                Route::delete('/{cci}', 'ContactIdentifiers\ContactIdentifiersController@destroy');
            }
        );

        Route::prefix('contacts')->group(
            function () {
                Route::get('/', 'Contacts\ContactsController@index');
                Route::get('/actions', 'Contacts\ContactsController@getActions');

                Route::get('{communication_contacts}/tags ', 'Contacts\ContactsController@tags');
                Route::post('{communication_contacts}/tags ', 'Contacts\ContactsController@saveTags');
                Route::get('{communication_contacts}/addresses ', 'Contacts\ContactsController@addresses');
                Route::post('{communication_contacts}/addresses ', 'Contacts\ContactsController@saveAddresses');

                Route::get('/{communication_contacts}/{subObjects}', 'Contacts\ContactsController@relatedObjects');
                Route::get('/{communication_contacts}', 'Contacts\ContactsController@show');

                Route::post('/', 'Contacts\ContactsController@store');
                Route::post('/{communication_contacts}/do/{action}', 'Contacts\ContactsController@doAction');

                Route::patch('/{communication_contacts}', 'Contacts\ContactsController@update');
                Route::delete('/{communication_contacts}', 'Contacts\ContactsController@destroy');
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

        Route::prefix('threads')->group(
            function () {
                Route::get('/', 'Threads\ThreadsController@index');
                Route::get('/actions', 'Threads\ThreadsController@getActions');

                Route::get('{communication_threads}/tags ', 'Threads\ThreadsController@tags');
                Route::post('{communication_threads}/tags ', 'Threads\ThreadsController@saveTags');
                Route::get('{communication_threads}/addresses ', 'Threads\ThreadsController@addresses');
                Route::post('{communication_threads}/addresses ', 'Threads\ThreadsController@saveAddresses');

                Route::get('/{communication_threads}/{subObjects}', 'Threads\ThreadsController@relatedObjects');
                Route::get('/{communication_threads}', 'Threads\ThreadsController@show');

                Route::post('/', 'Threads\ThreadsController@store');
                Route::post('/{communication_threads}/do/{action}', 'Threads\ThreadsController@doAction');

                Route::patch('/{communication_threads}', 'Threads\ThreadsController@update');
                Route::delete('/{communication_threads}', 'Threads\ThreadsController@destroy');
            }
        );

        Route::prefix('thread-assignments')->group(
            function () {
                Route::get('/', 'ThreadAssignments\ThreadAssignmentsController@index');
                Route::get('/actions', 'ThreadAssignments\ThreadAssignmentsController@getActions');

                Route::get('{communication_thread_assignments}/tags ', 'ThreadAssignments\ThreadAssignmentsController@tags');
                Route::post('{communication_thread_assignments}/tags ', 'ThreadAssignments\ThreadAssignmentsController@saveTags');
                Route::get('{communication_thread_assignments}/addresses ', 'ThreadAssignments\ThreadAssignmentsController@addresses');
                Route::post('{communication_thread_assignments}/addresses ', 'ThreadAssignments\ThreadAssignmentsController@saveAddresses');

                Route::get('/{communication_thread_assignments}/{subObjects}', 'ThreadAssignments\ThreadAssignmentsController@relatedObjects');
                Route::get('/{communication_thread_assignments}', 'ThreadAssignments\ThreadAssignmentsController@show');

                Route::post('/', 'ThreadAssignments\ThreadAssignmentsController@store');
                Route::post('/{communication_thread_assignments}/do/{action}', 'ThreadAssignments\ThreadAssignmentsController@doAction');

                Route::patch('/{communication_thread_assignments}', 'ThreadAssignments\ThreadAssignmentsController@update');
                Route::delete('/{communication_thread_assignments}', 'ThreadAssignments\ThreadAssignmentsController@destroy');
            }
        );

        Route::prefix('messages')->group(
            function () {
                Route::get('/', 'Messages\MessagesController@index');
                Route::get('/actions', 'Messages\MessagesController@getActions');

                Route::get('{communication_messages}/tags ', 'Messages\MessagesController@tags');
                Route::post('{communication_messages}/tags ', 'Messages\MessagesController@saveTags');
                Route::get('{communication_messages}/addresses ', 'Messages\MessagesController@addresses');
                Route::post('{communication_messages}/addresses ', 'Messages\MessagesController@saveAddresses');

                Route::get('/{communication_messages}/{subObjects}', 'Messages\MessagesController@relatedObjects');
                Route::get('/{communication_messages}', 'Messages\MessagesController@show');

                Route::post('/', 'Messages\MessagesController@store');
                Route::post('/{communication_messages}/do/{action}', 'Messages\MessagesController@doAction');

                Route::patch('/{communication_messages}', 'Messages\MessagesController@update');
                Route::delete('/{communication_messages}', 'Messages\MessagesController@destroy');
            }
        );

        Route::prefix('message-events')->group(
            function () {
                Route::get('/', 'MessageEvents\MessageEventsController@index');
                Route::get('/actions', 'MessageEvents\MessageEventsController@getActions');

                Route::get('{communication_message_events}/tags ', 'MessageEvents\MessageEventsController@tags');
                Route::post('{communication_message_events}/tags ', 'MessageEvents\MessageEventsController@saveTags');
                Route::get('{communication_message_events}/addresses ', 'MessageEvents\MessageEventsController@addresses');
                Route::post('{communication_message_events}/addresses ', 'MessageEvents\MessageEventsController@saveAddresses');

                Route::get('/{communication_message_events}/{subObjects}', 'MessageEvents\MessageEventsController@relatedObjects');
                Route::get('/{communication_message_events}', 'MessageEvents\MessageEventsController@show');

                Route::post('/', 'MessageEvents\MessageEventsController@store');
                Route::post('/{communication_message_events}/do/{action}', 'MessageEvents\MessageEventsController@doAction');

                Route::patch('/{communication_message_events}', 'MessageEvents\MessageEventsController@update');
                Route::delete('/{communication_message_events}', 'MessageEvents\MessageEventsController@destroy');
            }
        );

        Route::prefix('unsubscribes')->group(
            function () {
                Route::get('/', 'Unsubscribes\UnsubscribesController@index');
                Route::get('/actions', 'Unsubscribes\UnsubscribesController@getActions');

                Route::get('{communication_unsubscribes}/tags ', 'Unsubscribes\UnsubscribesController@tags');
                Route::post('{communication_unsubscribes}/tags ', 'Unsubscribes\UnsubscribesController@saveTags');
                Route::get('{communication_unsubscribes}/addresses ', 'Unsubscribes\UnsubscribesController@addresses');
                Route::post('{communication_unsubscribes}/addresses ', 'Unsubscribes\UnsubscribesController@saveAddresses');

                Route::get('/{communication_unsubscribes}/{subObjects}', 'Unsubscribes\UnsubscribesController@relatedObjects');
                Route::get('/{communication_unsubscribes}', 'Unsubscribes\UnsubscribesController@show');

                Route::post('/', 'Unsubscribes\UnsubscribesController@store');
                Route::post('/{communication_unsubscribes}/do/{action}', 'Unsubscribes\UnsubscribesController@doAction');

                Route::patch('/{communication_unsubscribes}', 'Unsubscribes\UnsubscribesController@update');
                Route::delete('/{communication_unsubscribes}', 'Unsubscribes\UnsubscribesController@destroy');
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

        // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
















        Route::prefix('channels')->group(
            function () {
                Route::post('/{communication_channels}/code/send', 'Channels\ChannelsController@sendCode');
                Route::post('/{communication_channels}/code/verify', 'Channels\ChannelsController@verifyCode');
            }
        );

    }
);

