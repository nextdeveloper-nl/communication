<?php

namespace NextDeveloper\Communication\Http\Controllers\Channels;

use Illuminate\Http\Request;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Commons\Http\Traits\Addresses;
use NextDeveloper\Commons\Http\Traits\Tags;
use NextDeveloper\Communication\Database\Filters\ChannelsQueryFilter;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Http\Controllers\AbstractController;
use NextDeveloper\Communication\Http\Requests\Channels\ChannelsCreateRequest;
use NextDeveloper\Communication\Http\Requests\Channels\ChannelsUpdateRequest;
use NextDeveloper\Communication\Services\ChannelsService;

class ChannelsController extends AbstractController
{
    private $model = Channels::class;

    use Tags;
    use Addresses;
    /**
     * This method returns the list of channels.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  ChannelsQueryFilter $filter  An object that builds search query
     * @param  Request             $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ChannelsQueryFilter $filter, Request $request)
    {
        $data = ChannelsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = ChannelsService::getActions();

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * Makes the related action to the object
     *
     * @param  $objectId
     * @param  $action
     * @return array
     */
    public function doAction($objectId, $action)
    {
        $actionId = ChannelsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $channelsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = ChannelsService::getByRef($ref);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method returns the list of sub objects the related object. Sub object means an object which is preowned by
     * this object.
     *
     * It can be tags, addresses, states etc.
     *
     * @param  $ref
     * @param  $subObject
     * @return void
     */
    public function relatedObjects($ref, $subObject)
    {
        $objects = ChannelsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created Channels object on database.
     *
     * @param  ChannelsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(ChannelsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = ChannelsService::create($request->validated());

        // remove fields from request, because this affects the response
        $request->offsetUnset('fields');

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates Channels object on database.
     *
     * @param  $channelsId
     * @param  ChannelsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($channelsId, ChannelsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = ChannelsService::update($channelsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates Channels object on database.
     *
     * @param  $channelsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($channelsId)
    {
        $model = ChannelsService::delete($channelsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    public function sendCode($ref)
    {
        $model = ChannelsService::sendCode($ref);

        return $this->withArray([
            'code_sent' => $model
        ]);
    }

    public function verifyCode($ref, Request $request)
    {
        $model = ChannelsService::verifyCode($request->all(), $ref);

        return $this->withArray([
            'code_verified' => $model
        ]);
    }
}
