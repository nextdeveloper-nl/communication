<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Communication\Database\Filters\CommunicationBotQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationBotService;

trait CommunicationBotTestTraits
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

    public function test_http_communicationbot_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationbot',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationbot_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationbot', [
            'form_params'   =>  [
                'name'  =>  'a',
                'description'  =>  'a',
                'token'  =>  'a',
                'class'  =>  'a',
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
    public function test_communicationbot_model_get()
    {
        $result = AbstractCommunicationBotService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationbot_get_all()
    {
        $result = AbstractCommunicationBotService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationbot_get_paginated()
    {
        $result = AbstractCommunicationBotService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationbot_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbot_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBot\CommunicationBotRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_name_filter()
    {
        try {
            $request = new Request(
                [
                'name'  =>  'a'
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_description_filter()
    {
        try {
            $request = new Request(
                [
                'description'  =>  'a'
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_token_filter()
    {
        try {
            $request = new Request(
                [
                'token'  =>  'a'
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_class_filter()
    {
        try {
            $request = new Request(
                [
                'class'  =>  'a'
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbot_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBot::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
