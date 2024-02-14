<?php

namespace NextDeveloper\Communication\Http\Transformers\AbstractTransformers;

use NextDeveloper\Communication\Database\Models\UserPreferences;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class UserPreferencesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class AbstractUserPreferencesTransformer extends AbstractTransformer
{

    /**
     * @param UserPreferences $model
     *
     * @return array
     */
    public function transform(UserPreferences $model)
    {
                        $iamUserId = \NextDeveloper\IAM\Database\Models\Users::where('id', $model->iam_user_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'is_system_email_optout'  =>  $model->is_system_email_optout,
            'is_phone_optout'  =>  $model->is_phone_optout,
            'is_marketing_email_optout'  =>  $model->is_marketing_email_optout,
            'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE



















}
