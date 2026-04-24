<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\SmtpServers;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractSmtpServersTransformer;

/**
 * Class SmtpServersTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class SmtpServersTransformer extends AbstractSmtpServersTransformer
{

    /**
     * @param SmtpServers $model
     *
     * @return array
     */
    public function transform(SmtpServers $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('SmtpServers', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('SmtpServers', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
