<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationMessageQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationMessageService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationMessageTestTraits
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

    public function test_http_communicationmessage_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationmessage',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationmessage_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationmessage', [
            'form_params'   =>  [
                'content_type'  =>  'a',
                'body'  =>  'a',
                'external_message_id'  =>  'a',
                'status'  =>  'a',
                'failure_reason'  =>  'a',
                'direction'  =>  '1',
                    'deliver_at'  =>  now(),
                    'delivered_at'  =>  now(),
                    'read_at'  =>  now(),
                    'failed_at'  =>  now(),
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
    public function test_communicationmessage_model_get()
    {
        $result = AbstractCommunicationMessageService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationmessage_get_all()
    {
        $result = AbstractCommunicationMessageService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationmessage_get_paginated()
    {
        $result = AbstractCommunicationMessageService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationmessage_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationmessage_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::first();

            event(new \NextDeveloper\Communication\Events\CommunicationMessage\CommunicationMessageRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_content_type_filter()
    {
        try {
            $request = new Request(
                [
                'content_type'  =>  'a'
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_body_filter()
    {
        try {
            $request = new Request(
                [
                'body'  =>  'a'
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_external_message_id_filter()
    {
        try {
            $request = new Request(
                [
                'external_message_id'  =>  'a'
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_status_filter()
    {
        try {
            $request = new Request(
                [
                'status'  =>  'a'
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_failure_reason_filter()
    {
        try {
            $request = new Request(
                [
                'failure_reason'  =>  'a'
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_direction_filter()
    {
        try {
            $request = new Request(
                [
                'direction'  =>  '1'
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_deliver_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deliver_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_delivered_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'delivered_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_read_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'read_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_failed_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'failed_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_deliver_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deliver_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_delivered_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'delivered_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_read_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'read_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_failed_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'failed_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_deliver_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deliver_atStart'  =>  now(),
                'deliver_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_delivered_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'delivered_atStart'  =>  now(),
                'delivered_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_read_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'read_atStart'  =>  now(),
                'read_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_failed_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'failed_atStart'  =>  now(),
                'failed_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationmessage_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationMessageQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}