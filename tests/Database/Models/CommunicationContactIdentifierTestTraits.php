<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationContactIdentifierQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationContactIdentifierService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationContactIdentifierTestTraits
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

    public function test_http_communicationcontactidentifier_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationcontactidentifier',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationcontactidentifier_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationcontactidentifier', [
            'form_params'   =>  [
                'channel_type'  =>  'a',
                'identifier'  =>  'a',
                'suppressed_reason'  =>  'a',
                    'suppressed_at'  =>  now(),
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
    public function test_communicationcontactidentifier_model_get()
    {
        $result = AbstractCommunicationContactIdentifierService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationcontactidentifier_get_all()
    {
        $result = AbstractCommunicationContactIdentifierService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationcontactidentifier_get_paginated()
    {
        $result = AbstractCommunicationContactIdentifierService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationcontactidentifier_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontactidentifier_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContactIdentifier\CommunicationContactIdentifierRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_channel_type_filter()
    {
        try {
            $request = new Request(
                [
                'channel_type'  =>  'a'
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_identifier_filter()
    {
        try {
            $request = new Request(
                [
                'identifier'  =>  'a'
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_suppressed_reason_filter()
    {
        try {
            $request = new Request(
                [
                'suppressed_reason'  =>  'a'
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_suppressed_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'suppressed_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_suppressed_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'suppressed_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_suppressed_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'suppressed_atStart'  =>  now(),
                'suppressed_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontactidentifier_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactIdentifierQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContactIdentifier::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}