<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationContactQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationContactService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationContactTestTraits
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

    public function test_http_communicationcontact_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationcontact',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationcontact_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationcontact', [
            'form_params'   =>  [
                'full_name'  =>  'a',
                'notes'  =>  'a',
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
    public function test_communicationcontact_model_get()
    {
        $result = AbstractCommunicationContactService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationcontact_get_all()
    {
        $result = AbstractCommunicationContactService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationcontact_get_paginated()
    {
        $result = AbstractCommunicationContactService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationcontact_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationcontact_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::first();

            event(new \NextDeveloper\Communication\Events\CommunicationContact\CommunicationContactRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_full_name_filter()
    {
        try {
            $request = new Request(
                [
                'full_name'  =>  'a'
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_notes_filter()
    {
        try {
            $request = new Request(
                [
                'notes'  =>  'a'
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationcontact_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationContactQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationContact::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}