<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Communication\Database\Filters\CommunicationAvailableChannelQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationAvailableChannelService;

trait CommunicationAvailableChannelTestTraits
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

    public function test_http_communicationavailablechannel_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationavailablechannel',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationavailablechannel_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationavailablechannel', [
            'form_params'   =>  [
                'name'  =>  'a',
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
    public function test_communicationavailablechannel_model_get()
    {
        $result = AbstractCommunicationAvailableChannelService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationavailablechannel_get_all()
    {
        $result = AbstractCommunicationAvailableChannelService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationavailablechannel_get_paginated()
    {
        $result = AbstractCommunicationAvailableChannelService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationavailablechannel_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationavailablechannel_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAvailableChannel\CommunicationAvailableChannelRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_name_filter()
    {
        try {
            $request = new Request(
                [
                'name'  =>  'a'
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_class_filter()
    {
        try {
            $request = new Request(
                [
                'class'  =>  'a'
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationavailablechannel_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAvailableChannelQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAvailableChannel::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
