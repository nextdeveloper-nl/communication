<?php

namespace NextDeveloper\Communication\Http\Controllers\AvailableChannels;

use Illuminate\Http\Request;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Commons\Http\Traits\Addresses;
use NextDeveloper\Commons\Http\Traits\Tags;
use NextDeveloper\Communication\Database\Filters\AvailableChannelsQueryFilter;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Http\Controllers\AbstractController;
use NextDeveloper\Communication\Http\Requests\AvailableChannels\AvailableChannelsCreateRequest;
use NextDeveloper\Communication\Http\Requests\AvailableChannels\AvailableChannelsUpdateRequest;
use NextDeveloper\Communication\Services\AvailableChannelsService;

class AvailableChannelsController extends AbstractController
{
    private $model = AvailableChannels::class;

    use Tags;
    use Addresses;
    /**
     * This method returns the list of availablechannels.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  AvailableChannelsQueryFilter $filter  An object that builds search query
     * @param  Request                      $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(AvailableChannelsQueryFilter $filter, Request $request)
    {
        $data = AvailableChannelsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = AvailableChannelsService::getActions();

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
        $actionId = AvailableChannelsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $availableChannelsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = AvailableChannelsService::getByRef($ref);

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
        $objects = AvailableChannelsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created AvailableChannels object on database.
     *
     * @param  AvailableChannelsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(AvailableChannelsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = AvailableChannelsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates AvailableChannels object on database.
     *
     * @param  $availableChannelsId
     * @param  AvailableChannelsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($availableChannelsId, AvailableChannelsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = AvailableChannelsService::update($availableChannelsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates AvailableChannels object on database.
     *
     * @param  $availableChannelsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($availableChannelsId)
    {
        $model = AvailableChannelsService::delete($availableChannelsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
