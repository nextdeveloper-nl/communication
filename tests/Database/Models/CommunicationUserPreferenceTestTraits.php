<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Communication\Database\Filters\CommunicationUserPreferenceQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationUserPreferenceService;

trait CommunicationUserPreferenceTestTraits
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

    public function test_http_communicationuserpreference_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationuserpreference',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationuserpreference_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationuserpreference', [
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
    public function test_communicationuserpreference_model_get()
    {
        $result = AbstractCommunicationUserPreferenceService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationuserpreference_get_all()
    {
        $result = AbstractCommunicationUserPreferenceService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationuserpreference_get_paginated()
    {
        $result = AbstractCommunicationUserPreferenceService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationuserpreference_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationuserpreference_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::first();

            event(new \NextDeveloper\Communication\Events\CommunicationUserPreference\CommunicationUserPreferenceRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationuserpreference_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationUserPreferenceQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationUserPreference::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
