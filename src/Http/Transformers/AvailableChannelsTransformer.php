<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\AvailableChannels;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractAvailableChannelsTransformer;

/**
 * Class AvailableChannelsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class AvailableChannelsTransformer extends AbstractAvailableChannelsTransformer
{

    /**
     * @param AvailableChannels $model
     *
     * @return array
     */
    public function transform(AvailableChannels $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('AvailableChannels', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('AvailableChannels', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
