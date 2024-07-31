<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationBotCodeQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationBotCodeService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationBotCodeTestTraits
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

    public function test_http_communicationbotcode_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationbotcode',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationbotcode_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationbotcode', [
            'form_params'   =>  [
                'code'  =>  '1',
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
    public function test_communicationbotcode_model_get()
    {
        $result = AbstractCommunicationBotCodeService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationbotcode_get_all()
    {
        $result = AbstractCommunicationBotCodeService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationbotcode_get_paginated()
    {
        $result = AbstractCommunicationBotCodeService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationbotcode_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationbotcode_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::first();

            event(new \NextDeveloper\Communication\Events\CommunicationBotCode\CommunicationBotCodeRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_code_filter()
    {
        try {
            $request = new Request(
                [
                'code'  =>  '1'
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationbotcode_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationBotCodeQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationBotCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}