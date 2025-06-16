<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Communication\Database\Filters\CommunicationBotUserQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationBotUserService;

trait CommunicationBotUserTestTraits
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

    public function test_http_communicationbotuser_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationbotuser',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationbotuser_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationbotuser', [
            'form_params'   =>  [
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
    public function test_communicationbotuser_model_get()
    {
        $result = AbstractCommunicationBotUserService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationbotuser_get_all()
    {
        $result = AbstractCommunicationBotUserService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationbotuser_get_paginated()
    {
        $result = AbstractCommunicationBotUserService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationbotuser_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotuser_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotUser\CommunicationBotUserRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotuser_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
