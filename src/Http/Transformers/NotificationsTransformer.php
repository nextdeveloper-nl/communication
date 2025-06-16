<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Notifications;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractNotificationsTransformer;

/**
 * Class NotificationsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class NotificationsTransformer extends AbstractNotificationsTransformer
{

    /**
     * @param Notifications $model
     *
     * @return array
     */
    public function transform(Notifications $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Notifications', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Notifications', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
