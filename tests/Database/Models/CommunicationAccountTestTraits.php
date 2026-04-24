<?php

namespace NextDeveloper\Communication\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Communication\Database\Filters\CommunicationAccountQueryFilter;
use NextDeveloper\Communication\Services\AbstractServices\AbstractCommunicationAccountService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait CommunicationAccountTestTraits
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

    public function test_http_communicationaccount_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/communication/communicationaccount',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_communicationaccount_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/communication/communicationaccount', [
            'form_params'   =>  [
                'plan'  =>  'a',
                'suspension_reason'  =>  'a',
                'max_contacts'  =>  '1',
                'max_emails_per_month'  =>  '1',
                'max_sms_per_month'  =>  '1',
                'max_channels'  =>  '1',
                'emails_sent_this_period'  =>  '1',
                'sms_sent_this_period'  =>  '1',
                    'current_period_start'  =>  now(),
                    'current_period_end'  =>  now(),
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
    public function test_communicationaccount_model_get()
    {
        $result = AbstractCommunicationAccountService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationaccount_get_all()
    {
        $result = AbstractCommunicationAccountService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_communicationaccount_get_paginated()
    {
        $result = AbstractCommunicationAccountService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_communicationaccount_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_communicationaccount_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::first();

            event(new \NextDeveloper\Communication\Events\CommunicationAccount\CommunicationAccountRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_plan_filter()
    {
        try {
            $request = new Request(
                [
                'plan'  =>  'a'
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_suspension_reason_filter()
    {
        try {
            $request = new Request(
                [
                'suspension_reason'  =>  'a'
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_max_contacts_filter()
    {
        try {
            $request = new Request(
                [
                'max_contacts'  =>  '1'
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_max_emails_per_month_filter()
    {
        try {
            $request = new Request(
                [
                'max_emails_per_month'  =>  '1'
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_max_sms_per_month_filter()
    {
        try {
            $request = new Request(
                [
                'max_sms_per_month'  =>  '1'
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_max_channels_filter()
    {
        try {
            $request = new Request(
                [
                'max_channels'  =>  '1'
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_emails_sent_this_period_filter()
    {
        try {
            $request = new Request(
                [
                'emails_sent_this_period'  =>  '1'
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_sms_sent_this_period_filter()
    {
        try {
            $request = new Request(
                [
                'sms_sent_this_period'  =>  '1'
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_current_period_start_filter_start()
    {
        try {
            $request = new Request(
                [
                'current_period_startStart'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_current_period_end_filter_start()
    {
        try {
            $request = new Request(
                [
                'current_period_endStart'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_current_period_start_filter_end()
    {
        try {
            $request = new Request(
                [
                'current_period_startEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_current_period_end_filter_end()
    {
        try {
            $request = new Request(
                [
                'current_period_endEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_current_period_start_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'current_period_startStart'  =>  now(),
                'current_period_startEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_current_period_end_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'current_period_endStart'  =>  now(),
                'current_period_endEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_communicationaccount_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new CommunicationAccountQueryFilter($request);

            $model = \NextDeveloper\Communication\Database\Models\CommunicationAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}