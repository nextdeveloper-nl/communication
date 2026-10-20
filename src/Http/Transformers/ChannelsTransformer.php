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

        if(!$transformed) {
            $transformed = parent::transform($model);

            Cache::set(
                CacheHelper::getKey('Channels', $model->uuid, 'Transformed'),
                $transformed
            );
        }

        // Applied after the cache on purpose: entries cached before this existed (or by another code path) still hold the
        // real tokens, and they must never leave the API.
        return self::maskCredentials($model, $transformed);
    }

    /**
     * WhatsApp and Facebook channels keep a long lived Meta access token in credentials. The token is write-only: it is
     * accepted when the channel is connected and used server side, but never returned (it would end up in browser
     * network logs, screenshots and support tickets). The keys stay so a client can still see that a token is stored.
     */
    private static function maskCredentials(Channels $model, array $transformed): array
    {
        if (in_array($model->type, [Channels::TYPE_WHATSAPP, Channels::TYPE_FACEBOOK], true)
            && !empty($transformed['credentials']) && is_array($transformed['credentials'])) {
            $transformed['credentials'] = array_map(fn () => '********', $transformed['credentials']);
        }

        return $transformed;
    }
}
