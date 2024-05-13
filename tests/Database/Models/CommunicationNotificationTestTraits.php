<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationNotificationQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationNotificationService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationNotificationTestTraits
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

    public function test_http_communicationnotification_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationnotification',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationnotification_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationnotification', [
            'form_params'   =>  [
                'object_type'  =>  'a',
                'data'  =>  'a',
                    'read_at'  =>  now(),
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
    public function test_communicationnotification_model_get()
    {
        $result = AbstractCommunicationNotificationService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationnotification_get_all()
    {
        $result = AbstractCommunicationNotificationService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationnotification_get_paginated()
    {
        $result = AbstractCommunicationNotificationService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationnotification_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationnotification_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::first();

            event(new \NextDeveloper\Communication\Events\CommunicationNotification\CommunicationNotificationRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_object_type_filter()
    {
        try {
            $request = new Request(
                [
                'object_type'  =>  'a'
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_data_filter()
    {
        try {
            $request = new Request(
                [
                'data'  =>  'a'
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_read_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'read_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_read_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'read_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_read_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'read_atStart'  =>  now(),
                'read_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationnotification_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationNotificationQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationNotification::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}