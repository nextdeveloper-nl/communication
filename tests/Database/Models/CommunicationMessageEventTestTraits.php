<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationMessageEventQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationMessageEventService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationMessageEventTestTraits
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

    public function test_http_communicationmessageevent_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationmessageevent',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationmessageevent_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationmessageevent', [
            'form_params'   =>  [
                'event_type'  =>  'a',
                    'occurred_at'  =>  now(),
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
    public function test_communicationmessageevent_model_get()
    {
        $result = AbstractCommunicationMessageEventService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationmessageevent_get_all()
    {
        $result = AbstractCommunicationMessageEventService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationmessageevent_get_paginated()
    {
        $result = AbstractCommunicationMessageEventService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationmessageevent_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessageevent_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessageevent_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessageEvent\CommunicationMessageEventRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessageevent_event_event_type_filter()
    {
        try {
            $request = new Request(
                [
                'event_type'  =>  'a'
                ]
            );

            $filter = new CommunicationMessageEventQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessageevent_event_occurred_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'occurred_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageEventQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessageevent_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageEventQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessageevent_event_occurred_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'occurred_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageEventQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessageevent_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageEventQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessageevent_event_occurred_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'occurred_atStart'  =>  now(),
                'occurred_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageEventQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessageevent_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageEventQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessageEvent::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}