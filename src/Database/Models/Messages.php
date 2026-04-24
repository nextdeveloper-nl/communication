<?php

namespace NextDeveloper\Communication\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Communication\Database\Observers\MessagesObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * Messages model.
 *
 * @package  NextDeveloper\Communication\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $communication_thread_id
 * @property integer $crm_campaign_id
 * @property integer $direction
 * @property string $content_type
 * @property string $body
 * @property array $attachments
 * @property integer $sent_by_user_id
 * @property integer $sent_by_bot_id
 * @property integer $reply_to_id
 * @property string $external_message_id
 * @property string $status
 * @property \Carbon\Carbon $deliver_at
 * @property \Carbon\Carbon $delivered_at
 * @property \Carbon\Carbon $read_at
 * @property \Carbon\Carbon $failed_at
 * @property string $failure_reason
 * @property boolean $is_internal
 * @property $metadata
 * @property integer $iam_account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Messages extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'communication_messages';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'communication_thread_id',
            'crm_campaign_id',
            'direction',
            'content_type',
            'body',
            'attachments',
            'sent_by_user_id',
            'sent_by_bot_id',
            'reply_to_id',
            'external_message_id',
            'status',
            'deliver_at',
            'delivered_at',
            'read_at',
            'failed_at',
            'failure_reason',
            'is_internal',
            'metadata',
            'iam_account_id',
    ];

    /**
      Here we have the fulltext fields. We can use these for fulltext search if enabled.
     */
    protected $fullTextFields = [

    ];

    /**
     @var array
     */
    protected $appends = [

    ];

    /**
     We are casting fields to objects so that we can work on them better
     *
     @var array
     */
    protected $casts = [
    'id' => 'integer',
    'communication_thread_id' => 'integer',
    'crm_campaign_id' => 'integer',
    'direction' => 'integer',
    'content_type' => 'string',
    'body' => 'string',
    'attachments' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'sent_by_user_id' => 'integer',
    'sent_by_bot_id' => 'integer',
    'reply_to_id' => 'integer',
    'external_message_id' => 'string',
    'status' => 'string',
    'deliver_at' => 'datetime',
    'delivered_at' => 'datetime',
    'read_at' => 'datetime',
    'failed_at' => 'datetime',
    'failure_reason' => 'string',
    'is_internal' => 'boolean',
    'metadata' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'deliver_at',
    'delivered_at',
    'read_at',
    'failed_at',
    'created_at',
    'updated_at',
    'deleted_at',
    ];

    /**
     @var array
     */
    protected $with = [

    ];

    /**
     @var int
     */
    protected $perPage = 20;

    /**
     @return void
     */
    public static function boot()
    {
        parent::boot();

        //  We create and add Observer even if we wont use it.
        parent::observe(MessagesObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('communication.scopes.global');
        $modelScopes = config('communication.scopes.communication_messages');

        if(!$modelScopes) { $modelScopes = [];
        }
        if (!$globalScopes) { $globalScopes = [];
        }

        $scopes = array_merge(
            $globalScopes,
            $modelScopes
        );

        if($scopes) {
            foreach ($scopes as $scope) {
                static::addGlobalScope(app($scope));
            }
        }
    }

    public function campaigns() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\CRM\Database\Models\Campaigns::class);
    }
    
    public function accounts() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\IAM\Database\Models\Accounts::class);
    }
    
    public function threads() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\Communication\Database\Models\Threads::class);
    }
    
    public function messageEvents() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\NextDeveloper\Communication\Database\Models\MessageEvents::class);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
