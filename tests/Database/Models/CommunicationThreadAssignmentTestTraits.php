<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationThreadAssignmentQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationThreadAssignmentService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationThreadAssignmentTestTraits
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

    public function test_http_communicationthreadassignment_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationthreadassignment',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationthreadassignment_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationthreadassignment', [
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
    public function test_communicationthreadassignment_model_get()
    {
        $result = AbstractCommunicationThreadAssignmentService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationthreadassignment_get_all()
    {
        $result = AbstractCommunicationThreadAssignmentService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationthreadassignment_get_paginated()
    {
        $result = AbstractCommunicationThreadAssignmentService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationthreadassignment_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthreadassignment_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationthreadassignment_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::first();

            event(new \NextDeveloper\Communication\Events\CommunicationThreadAssignment\CommunicationThreadAssignmentRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthreadassignment_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationThreadAssignmentQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthreadassignment_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadAssignmentQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationthreadassignment_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationThreadAssignmentQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationThreadAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}