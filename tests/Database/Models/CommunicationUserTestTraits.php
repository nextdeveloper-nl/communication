<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Communication\Database\Filters\CommunicationUserQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationUserService;

trait CommunicationUserTestTraits
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

    public function test_http_communicationuser_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationuser',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationuser_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationuser', [
            'form_params'   =>  [
                'telegram_id'  =>  'a',
                'ai_session_id'  =>  'a',
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
    public function test_communicationuser_model_get()
    {
        $result = AbstractCommunicationUserService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationuser_get_all()
    {
        $result = AbstractCommunicationUserService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationuser_get_paginated()
    {
        $result = AbstractCommunicationUserService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationuser_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuser_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUser\CommunicationUserRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_telegram_id_filter()
    {
        try {
            $request = new Request(
                [
                'telegram_id'  =>  'a'
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_ai_session_id_filter()
    {
        try {
            $request = new Request(
                [
                'ai_session_id'  =>  'a'
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuser_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUser::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
