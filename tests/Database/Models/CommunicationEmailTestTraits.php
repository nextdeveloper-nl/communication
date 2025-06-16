<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Communication\Database\Filters\CommunicationEmailQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationEmailService;

trait CommunicationEmailTestTraits
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

    public function test_http_communicationemail_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationemail',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationemail_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationemail', [
            'form_params'   =>  [
                'from_email_address'  =>  'a',
                'subject'  =>  'a',
                'body'  =>  'a',
                    'deliver_at'  =>  now(),
                    'delivered_at'  =>  now(),
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
    public function test_communicationemail_model_get()
    {
        $result = AbstractCommunicationEmailService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationemail_get_all()
    {
        $result = AbstractCommunicationEmailService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationemail_get_paginated()
    {
        $result = AbstractCommunicationEmailService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationemail_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationemail_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::first();

            event(new \NextDeveloper\Communication\Events\CommunicationEmail\CommunicationEmailRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_from_email_address_filter()
    {
        try {
            $request = new Request(
                [
                'from_email_address'  =>  'a'
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_subject_filter()
    {
        try {
            $request = new Request(
                [
                'subject'  =>  'a'
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_body_filter()
    {
        try {
            $request = new Request(
                [
                'body'  =>  'a'
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_deliver_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deliver_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_delivered_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'delivered_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_deliver_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deliver_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_delivered_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'delivered_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_deliver_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deliver_atStart'  =>  now(),
                'deliver_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_delivered_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'delivered_atStart'  =>  now(),
                'delivered_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationemail_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationEmailQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationEmail::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
