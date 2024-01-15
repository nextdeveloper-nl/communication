<?php

namespace NextDeveloper\Communication\Http\Controllers\UserPreferences;

use Illuminate\Http\Request;
use NextDeveloper\Communication\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Communication\Http\Requests\UserPreferences\UserPreferencesUpdateRequest;
use NextDeveloper\Communication\Database\Filters\UserPreferencesQueryFilter;
use NextDeveloper\Communication\Database\Models\UserPreferences;
use NextDeveloper\Communication\Services\UserPreferencesService;
use NextDeveloper\Communication\Http\Requests\UserPreferences\UserPreferencesCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags;use NextDeveloper\Commons\Http\Traits\Addresses;
class UserPreferencesController extends AbstractController
{
    private $model = UserPreferences::class;

    use Tags;
    use Addresses;
    /**
     * This method returns the list of userpreferences.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  UserPreferencesQueryFilter $filter  An object that builds search query
     * @param  Request                    $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserPreferencesQueryFilter $filter, Request $request)
    {
        $data = UserPreferencesService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $userPreferencesId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = UserPreferencesService::getByRef($ref);

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
        $objects = UserPreferencesService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created UserPreferences object on database.
     *
     * @param  UserPreferencesCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(UserPreferencesCreateRequest $request)
    {
        $model = UserPreferencesService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates UserPreferences object on database.
     *
     * @param  $userPreferencesId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($userPreferencesId, UserPreferencesUpdateRequest $request)
    {
        $model = UserPreferencesService::update($userPreferencesId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates UserPreferences object on database.
     *
     * @param  $userPreferencesId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($userPreferencesId)
    {
        $model = UserPreferencesService::delete($userPreferencesId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
