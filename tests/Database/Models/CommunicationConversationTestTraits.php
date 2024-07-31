<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationConversationQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationConversationService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationConversationTestTraits
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

    public function test_http_communicationconversation_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationconversation',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationconversation_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationconversation', [
            'form_params'   =>  [
                'message'  =>  'a',
                'direction'  =>  '1',
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
    public function test_communicationconversation_model_get()
    {
        $result = AbstractCommunicationConversationService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationconversation_get_all()
    {
        $result = AbstractCommunicationConversationService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationconversation_get_paginated()
    {
        $result = AbstractCommunicationConversationService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationconversation_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationconversation_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::first();

            event(new \NextDeveloper\Communication\Events\CommunicationConversation\CommunicationConversationRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_message_filter()
    {
        try {
            $request = new Request(
                [
                'message'  =>  'a'
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_direction_filter()
    {
        try {
            $request = new Request(
                [
                'direction'  =>  '1'
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationconversation_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationConversationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationConversation::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}