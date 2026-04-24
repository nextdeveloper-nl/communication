<?php

namespace NextDeveloper\Communication\Http\Transformers\AbstractTransformers;

use NextDeveloper\Commons\Database\Models\Addresses;
use NextDeveloper\Commons\Database\Models\Comments;
use NextDeveloper\Commons\Database\Models\Meta;
use NextDeveloper\Commons\Database\Models\PhoneNumbers;
use NextDeveloper\Commons\Database\Models\SocialMedia;
use NextDeveloper\Commons\Database\Models\Votes;
use NextDeveloper\Commons\Database\Models\Media;
use NextDeveloper\Commons\Http\Transformers\MediaTransformer;
use NextDeveloper\Commons\Database\Models\AvailableActions;
use NextDeveloper\Commons\Http\Transformers\AvailableActionsTransformer;
use NextDeveloper\Commons\Database\Models\States;
use NextDeveloper\Commons\Http\Transformers\StatesTransformer;
use NextDeveloper\Commons\Http\Transformers\CommentsTransformer;
use NextDeveloper\Commons\Http\Transformers\SocialMediaTransformer;
use NextDeveloper\Commons\Http\Transformers\MetaTransformer;
use NextDeveloper\Commons\Http\Transformers\VotesTransformer;
use NextDeveloper\Commons\Http\Transformers\AddressesTransformer;
use NextDeveloper\Commons\Http\Transformers\PhoneNumbersTransformer;
use NextDeveloper\Communication\Database\Models\Messages;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * Class MessagesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Communication\Http\Transformers
 */
class AbstractMessagesTransformer extends AbstractTransformer
{

    /**
     * @var array
     */
    protected array $availableIncludes = [
        'states',
        'actions',
        'media',
        'comments',
        'votes',
        'socialMedia',
        'phoneNumbers',
        'addresses',
        'meta'
    ];

    /**
     * @param Messages $model
     *
     * @return array
     */
    public function transform(Messages $model)
    {
                                                $communicationThreadId = \NextDeveloper\Communication\Database\Models\Threads::where('id', $model->communication_thread_id)->first();
                                                            $crmCampaignId = \NextDeveloper\CRM\Database\Models\Campaigns::where('id', $model->crm_campaign_id)->first();
                                                            $sentByUserId = \NextDeveloper\\Database\Models\SentByUsers::where('id', $model->sent_by_user_id)->first();
                                                            $sentByBotId = \NextDeveloper\\Database\Models\SentByBots::where('id', $model->sent_by_bot_id)->first();
                                                            $replyToId = \NextDeveloper\\Database\Models\ReplyTos::where('id', $model->reply_to_id)->first();
                                                            $externalMessageId = \NextDeveloper\\Database\Models\ExternalMessages::where('id', $model->external_message_id)->first();
                                                            $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
                        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'communication_thread_id'  =>  $communicationThreadId ? $communicationThreadId->uuid : null,
            'crm_campaign_id'  =>  $crmCampaignId ? $crmCampaignId->uuid : null,
            'direction'  =>  $model->direction,
            'content_type'  =>  $model->content_type,
            'body'  =>  $model->body,
            'attachments'  =>  $model->attachments,
            'sent_by_user_id'  =>  $sentByUserId ? $sentByUserId->uuid : null,
            'sent_by_bot_id'  =>  $sentByBotId ? $sentByBotId->uuid : null,
            'reply_to_id'  =>  $replyToId ? $replyToId->uuid : null,
            'external_message_id'  =>  $externalMessageId ? $externalMessageId->uuid : null,
            'status'  =>  $model->status,
            'deliver_at'  =>  $model->deliver_at,
            'delivered_at'  =>  $model->delivered_at,
            'read_at'  =>  $model->read_at,
            'failed_at'  =>  $model->failed_at,
            'failure_reason'  =>  $model->failure_reason,
            'is_internal'  =>  $model->is_internal,
            'metadata'  =>  $model->metadata,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            ]
        );
    }

    public function includeStates(Messages $model)
    {
        $states = States::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($states, new StatesTransformer());
    }

    public function includeActions(Messages $model)
    {
        $input = get_class($model);
        $input = str_replace('\\Database\\Models', '', $input);

        $actions = AvailableActions::withoutGlobalScope(AuthorizationScope::class)
            ->where('input', $input)
            ->get();

        return $this->collection($actions, new AvailableActionsTransformer());
    }

    public function includeMedia(Messages $model)
    {
        $media = Media::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($media, new MediaTransformer());
    }

    public function includeSocialMedia(Messages $model)
    {
        $socialMedia = SocialMedia::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($socialMedia, new SocialMediaTransformer());
    }

    public function includeComments(Messages $model)
    {
        $comments = Comments::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($comments, new CommentsTransformer());
    }

    public function includeVotes(Messages $model)
    {
        $votes = Votes::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($votes, new VotesTransformer());
    }

    public function includeMeta(Messages $model)
    {
        $meta = Meta::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($meta, new MetaTransformer());
    }

    public function includePhoneNumbers(Messages $model)
    {
        $phoneNumbers = PhoneNumbers::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($phoneNumbers, new PhoneNumbersTransformer());
    }

    public function includeAddresses(Messages $model)
    {
        $addresses = Addresses::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($addresses, new AddressesTransformer());
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
