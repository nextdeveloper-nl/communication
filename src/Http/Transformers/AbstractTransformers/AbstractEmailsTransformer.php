<?php

namespace NextDeveloper\Communication\Http\Transformers\AbstractTransformers;

use NextDeveloper\Communication\Database\Models\Emails;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class EmailsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class AbstractEmailsTransformer extends AbstractTransformer
{

    /**
     * @param Emails $model
     *
     * @return array
     */
    public function transform(Emails $model)
    {
                        $iamUserId = \NextDeveloper\IAM\Database\Models\Users::where('id', $model->iam_user_id)->first();
                    $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'email_address'  =>  $model->email_address,
            'recipient_name'  =>  $model->recipient_name,
            'subject'  =>  $model->subject,
            'body'  =>  $model->body,
            'delivery_results'  =>  $model->delivery_results,
            'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'is_marketing_email'  =>  $model->is_marketing_email == 1 ? true : false,
            'deliver_at'  =>  $model->deliver_at ? $model->deliver_at->toIso8601String() : null,
            'delivered_at'  =>  $model->delivered_at ? $model->delivered_at->toIso8601String() : null,
            'created_at'  =>  $model->created_at ? $model->created_at->toIso8601String() : null,
            'updated_at'  =>  $model->updated_at ? $model->updated_at->toIso8601String() : null,
            'deleted_at'  =>  $model->deleted_at ? $model->deleted_at->toIso8601String() : null,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE



}
