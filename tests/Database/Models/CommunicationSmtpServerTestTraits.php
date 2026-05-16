<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationSmtpServerQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationSmtpServerService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationSmtpServerTestTraits
{
    public $http;

    /**
     *   Creating the Guzzle object
     */
    public function setupGuzzle()
    {
        $this->http = new Client(
            [
            'base_uri'  =>  '127.0.0.1:8000'
            ]
        );
    }

    /**
     *   Destroying the Guzzle object
     */
    public function destroyGuzzle()
    {
        $this->http = null;
    }

    public function test_http_communicationsmtpserver_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationsmtpserver',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationsmtpserver_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationsmtpserver', [
            'form_params'   =>  [
                'name'  =>  'a',
                'host'  =>  'a',
                'encryption'  =>  'a',
                'username'  =>  'a',
                'password'  =>  'a',
                'from_email'  =>  'a',
                'from_name'  =>  'a',
                'reply_to'  =>  'a',
                'last_check_status'  =>  'a',
                'last_check_message'  =>  'a',
                'port'  =>  '1',
                    'verified_at'  =>  now(),
                    'last_checked_at'  =>  now(),
                            ],
                ['http_errors' => false]
            ]
        );

        $this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
    }

    /**
     * Get test
     *
     * @return bool
     */
    public function test_communicationsmtpserver_model_get()
    {
        $result = AbstractCommunicationSmtpServerService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationsmtpserver_get_all()
    {
        $result = AbstractCommunicationSmtpServerService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationsmtpserver_get_paginated()
    {
        $result = AbstractCommunicationSmtpServerService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationsmtpserver_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationsmtpserver_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::first();

            event(new \NextDeveloper\Communication\Events\CommunicationSmtpServer\CommunicationSmtpServerRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_name_filter()
    {
        try {
            $request = new Request(
                [
                'name'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_host_filter()
    {
        try {
            $request = new Request(
                [
                'host'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_encryption_filter()
    {
        try {
            $request = new Request(
                [
                'encryption'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_username_filter()
    {
        try {
            $request = new Request(
                [
                'username'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_password_filter()
    {
        try {
            $request = new Request(
                [
                'password'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_from_email_filter()
    {
        try {
            $request = new Request(
                [
                'from_email'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_from_name_filter()
    {
        try {
            $request = new Request(
                [
                'from_name'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_reply_to_filter()
    {
        try {
            $request = new Request(
                [
                'reply_to'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_last_check_status_filter()
    {
        try {
            $request = new Request(
                [
                'last_check_status'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_last_check_message_filter()
    {
        try {
            $request = new Request(
                [
                'last_check_message'  =>  'a'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_port_filter()
    {
        try {
            $request = new Request(
                [
                'port'  =>  '1'
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_verified_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'verified_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_last_checked_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'last_checked_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_verified_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'verified_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_last_checked_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'last_checked_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_verified_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'verified_atStart'  =>  now(),
                'verified_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_last_checked_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'last_checked_atStart'  =>  now(),
                'last_checked_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationsmtpserver_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationSmtpServerQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationSmtpServer::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}