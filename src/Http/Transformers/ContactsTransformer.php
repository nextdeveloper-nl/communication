<?php

namespace NextDeveloper\Communication\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Communication\Database\Models\Contacts;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Communication\Http\Transformers\AbstractTransformers\AbstractContactsTransformer;

/**
 * Class ContactsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class ContactsTransformer extends AbstractContactsTransformer
{

    /**
     * @param Contacts $model
     *
     * @return array
     */
    public function transform(Contacts $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Contacts', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        // Source marker derived from the 'source:<name>' tag (null for contacts that were not imported)
        $source = collect($model->tags ?? [])->first(fn ($tag) => str_starts_with($tag, 'source:'));
        $transformed['source'] = $source ? substr($source, 7) : null;

        Cache::set(
            CacheHelper::getKey('Contacts', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
