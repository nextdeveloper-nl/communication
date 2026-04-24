<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationThreadQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationThreadService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationThreadTestTraits
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

    public function test_http_communicationthread_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationthread',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationthread_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationthread', [
            'form_params'   =>  [
                'subject'  =>  'a',
                'status'  =>  'a',
                    'assigned_at'  =>  now(),
                    'resolved_at'  =>  now(),
                    'last_message_at'  =>  now(),
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
    public function test_communicationthread_model_get()
    {
        $result = AbstractCommunicationThreadService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationthread_get_all()
    {
        $result = AbstractCommunicationThreadService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationthread_get_paginated()
    {
        $result = AbstractCommunicationThreadService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationthread_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthread_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThread\CommunicationThreadRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_subject_filter()
    {
        try {
            $request = new Request(
                [
                'subject'  =>  'a'
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_status_filter()
    {
        try {
            $request = new Request(
                [
                'status'  =>  'a'
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_assigned_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'assigned_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_resolved_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'resolved_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_last_message_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'last_message_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_assigned_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'assigned_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_resolved_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'resolved_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_last_message_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'last_message_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_assigned_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'assigned_atStart'  =>  now(),
                'assigned_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_resolved_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'resolved_atStart'  =>  now(),
                'resolved_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_last_message_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'last_message_atStart'  =>  now(),
                'last_message_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthread_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThread::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}