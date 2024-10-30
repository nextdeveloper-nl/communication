<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractChannelsTransformer;

/**
 * Class ChannelsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class ChannelsTransformer extends AbstractChannelsTransformer
{

    /**
     * @param Channels $model
     *
     * @return array
     */
    public function transform(Channels $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Channels', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Channels', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
