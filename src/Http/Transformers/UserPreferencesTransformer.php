<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\UserPreferences;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractUserPreferencesTransformer;

/**
 * Class UserPreferencesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class UserPreferencesTransformer extends AbstractUserPreferencesTransformer
{

    /**
     * @param UserPreferences $model
     *
     * @return array
     */
    public function transform(UserPreferences $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('UserPreferences', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('UserPreferences', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
