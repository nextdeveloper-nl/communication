<?php

namespace NextDeveloper\Communication\Http\Transformers;

use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Helpers\ObjectHelper;
use NextDeveloper\Communication\Database\Models\Notifications;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
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
        return CacheHelper::rememberTransformed(
            'Notifications',
            $model->uuid,
            function () use ($model) {
                $transformed = parent::transform($model);

                //  The record the notification is about, by its uuid rather than the internal id.
                $transformed['object_id'] = ObjectHelper::getObjectUuid($model->object_type, $model->object_id);

                return $transformed;
            }
        );
    }
}
