<?php

namespace NextDeveloper\Communication\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Communication\Database\Observers\AccountsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * Accounts model.
 *
 * @package  NextDeveloper\Communication\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $iam_account_id
 * @property string $plan
 * @property integer $max_contacts
 * @property integer $max_emails_per_month
 * @property integer $max_sms_per_month
 * @property integer $max_channels
 * @property integer $emails_sent_this_period
 * @property integer $sms_sent_this_period
 * @property \Carbon\Carbon $current_period_start
 * @property \Carbon\Carbon $current_period_end
 * @property boolean $is_suspended
 * @property string $suspension_reason
 * @property $reputation_score
 * @property array $enabled_channel_types
 * @property boolean $is_ai_bots_enabled
 * @property boolean $is_dedicated_ip_enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Accounts extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'communication_accounts';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'iam_account_id',
            'plan',
            'max_contacts',
            'max_emails_per_month',
            'max_sms_per_month',
            'max_channels',
            'emails_sent_this_period',
            'sms_sent_this_period',
            'current_period_start',
            'current_period_end',
            'is_suspended',
            'suspension_reason',
            'reputation_score',
            'enabled_channel_types',
            'is_ai_bots_enabled',
            'is_dedicated_ip_enabled',
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
    'plan' => 'string',
    'max_contacts' => 'integer',
    'max_emails_per_month' => 'integer',
    'max_sms_per_month' => 'integer',
    'max_channels' => 'integer',
    'emails_sent_this_period' => 'integer',
    'sms_sent_this_period' => 'integer',
    'current_period_start' => 'datetime',
    'current_period_end' => 'datetime',
    'is_suspended' => 'boolean',
    'suspension_reason' => 'string',
    'enabled_channel_types' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'is_ai_bots_enabled' => 'boolean',
    'is_dedicated_ip_enabled' => 'boolean',
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
    'current_period_start',
    'current_period_end',
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
        parent::observe(AccountsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('communication.scopes.global');
        $modelScopes = config('communication.scopes.communication_accounts');

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

    public function accounts() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\IAM\Database\Models\Accounts::class);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
