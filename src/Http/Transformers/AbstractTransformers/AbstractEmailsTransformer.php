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
            'from_email_address'  =>  $model->from_email_address,
            'subject'  =>  $model->subject,
            'body'  =>  $model->body,
            'delivery_results'  =>  $model->delivery_results,
            'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'is_marketing_email'  =>  $model->is_marketing_email,
            'deliver_at'  =>  $model->deliver_at,
            'delivered_at'  =>  $model->delivered_at,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            'to'  =>  $model->to,
            'cc'  =>  $model->cc,
            'bcc'  =>  $model->bcc,
            'attachments'  =>  $model->attachments,
            'headers'  =>  $model->headers,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE










}
