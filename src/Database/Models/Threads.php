<?php

namespace NextDeveloper\Communication\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Communication\Database\Observers\ThreadsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * Threads model.
 *
 * @package  NextDeveloper\Communication\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property string $subject
 * @property string $status
 * @property integer $communication_channel_id
 * @property integer $communication_contact_id
 * @property integer $communication_bot_id
 * @property integer $assigned_to_user_id
 * @property \Carbon\Carbon $assigned_at
 * @property \Carbon\Carbon $resolved_at
 * @property \Carbon\Carbon $last_message_at
 * @property integer $iam_account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Threads extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'communication_threads';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'subject',
            'status',
            'communication_channel_id',
            'communication_contact_id',
            'communication_bot_id',
            'assigned_to_user_id',
            'assigned_at',
            'resolved_at',
            'last_message_at',
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
    'subject' => 'string',
    'status' => 'string',
    'communication_channel_id' => 'integer',
    'communication_contact_id' => 'integer',
    'communication_bot_id' => 'integer',
    'assigned_to_user_id' => 'integer',
    'assigned_at' => 'datetime',
    'resolved_at' => 'datetime',
    'last_message_at' => 'datetime',
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
    'assigned_at',
    'resolved_at',
    'last_message_at',
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
        parent::observe(ThreadsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('communication.scopes.global');
        $modelScopes = config('communication.scopes.communication_threads');

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

    public function channels() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\Communication\Database\Models\Channels::class);
    }
    
    public function contacts() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\Communication\Database\Models\Contacts::class);
    }
    
    public function bots() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\Communication\Database\Models\Bots::class);
    }
    
    public function accounts() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\IAM\Database\Models\Accounts::class);
    }
    
    public function threadAssignments() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\NextDeveloper\Communication\Database\Models\ThreadAssignments::class);
    }

    public function messages() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\NextDeveloper\Communication\Database\Models\Messages::class);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
