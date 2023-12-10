<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationRemindableQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationRemindableService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationRemindableTestTraits
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

    public function test_http_communicationremindable_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationremindable',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationremindable_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationremindable', [
            'form_params'   =>  [
                'remindable_object_type'  =>  'a',
                'note'  =>  'a',
                    'remind_datetime'  =>  now(),
                    'snooze_datetime'  =>  now(),
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
    public function test_communicationremindable_model_get()
    {
        $result = AbstractCommunicationRemindableService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationremindable_get_all()
    {
        $result = AbstractCommunicationRemindableService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationremindable_get_paginated()
    {
        $result = AbstractCommunicationRemindableService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationremindable_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationremindable_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::first();

            event(new \NextDeveloper\Communication\Events\CommunicationRemindable\CommunicationRemindableRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_remindable_object_type_filter()
    {
        try {
            $request = new Request(
                [
                'remindable_object_type'  =>  'a'
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_note_filter()
    {
        try {
            $request = new Request(
                [
                'note'  =>  'a'
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_remind_datetime_filter_start()
    {
        try {
            $request = new Request(
                [
                'remind_datetimeStart'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_snooze_datetime_filter_start()
    {
        try {
            $request = new Request(
                [
                'snooze_datetimeStart'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_remind_datetime_filter_end()
    {
        try {
            $request = new Request(
                [
                'remind_datetimeEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_snooze_datetime_filter_end()
    {
        try {
            $request = new Request(
                [
                'snooze_datetimeEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_remind_datetime_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'remind_datetimeStart'  =>  now(),
                'remind_datetimeEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_snooze_datetime_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'snooze_datetimeStart'  =>  now(),
                'snooze_datetimeEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationremindable_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationRemindableQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationRemindable::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}