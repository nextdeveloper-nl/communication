<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractMessagesTransformer;

/**
 * Class MessagesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class MessagesTransformer extends AbstractMessagesTransformer
{

    /**
     * @param Messages $model
     *
     * @return array
     */
    public function transform(Messages $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Messages', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Messages', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
