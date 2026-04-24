<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationUnsubscribeQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationUnsubscribeService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationUnsubscribeTestTraits
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

    public function test_http_communicationunsubscribe_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationunsubscribe',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationunsubscribe_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationunsubscribe', [
            'form_params'   =>  [
                'channel_type'  =>  'a',
                'identifier'  =>  'a',
                'reason'  =>  'a',
                'source'  =>  'a',
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
    public function test_communicationunsubscribe_model_get()
    {
        $result = AbstractCommunicationUnsubscribeService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationunsubscribe_get_all()
    {
        $result = AbstractCommunicationUnsubscribeService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationunsubscribe_get_paginated()
    {
        $result = AbstractCommunicationUnsubscribeService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationunsubscribe_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationunsubscribe_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationunsubscribe_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUnsubscribe\CommunicationUnsubscribeRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationunsubscribe_event_channel_type_filter()
    {
        try {
            $request = new Request(
                [
                'channel_type'  =>  'a'
                ]
            );

            $filter = new CommunicationUnsubscribeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationunsubscribe_event_identifier_filter()
    {
        try {
            $request = new Request(
                [
                'identifier'  =>  'a'
                ]
            );

            $filter = new CommunicationUnsubscribeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationunsubscribe_event_reason_filter()
    {
        try {
            $request = new Request(
                [
                'reason'  =>  'a'
                ]
            );

            $filter = new CommunicationUnsubscribeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationunsubscribe_event_source_filter()
    {
        try {
            $request = new Request(
                [
                'source'  =>  'a'
                ]
            );

            $filter = new CommunicationUnsubscribeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationunsubscribe_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationUnsubscribeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationunsubscribe_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUnsubscribeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationunsubscribe_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUnsubscribeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUnsubscribe::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}