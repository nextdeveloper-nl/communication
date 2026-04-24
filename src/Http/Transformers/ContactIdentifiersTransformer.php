<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\ContactIdentifiers;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractContactIdentifiersTransformer;

/**
 * Class ContactIdentifiersTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class ContactIdentifiersTransformer extends AbstractContactIdentifiersTransformer
{

    /**
     * @param ContactIdentifiers $model
     *
     * @return array
     */
    public function transform(ContactIdentifiers $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('ContactIdentifiers', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('ContactIdentifiers', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
