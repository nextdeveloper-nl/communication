<?php

namespace NextDeveloper\Communication\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Communication\Database\Observers\ChannelsObserver;

/**
 * Channels model.
 *
 * @package  NextDeveloper\Communication\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property $config
 * @property boolean $is_active
 * @property boolean $is_verified
 * @property integer $communication_available_channel_id
 * @property integer $iam_user_id
 * @property integer $iam_account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class Channels extends Model
{
    use Filterable, CleanCache, Taggable;
    use UuidId;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'communication_channels';


    /**
     * @var array
     */
    protected $guarded = [];

    protected $fillable = [
        'config',
        'is_active',
        'is_verified',
        'communication_available_channel_id',
        'iam_user_id',
        'iam_account_id',
    ];

    /**
     * Here we have the fulltext fields. We can use these for fulltext search if enabled.
     */
    protected $fullTextFields = [

    ];

    /**
     * @var array
     */
    protected $appends = [

    ];

    /**
     * We are casting fields to objects so that we can work on them better
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'config' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'communication_available_channel_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * We are casting data fields.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array
     */
    protected $with = [

    ];

    /**
     * @var int
     */
    protected $perPage = 20;

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        //  We create and add Observer even if we wont use it.
        parent::observe(ChannelsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('communication.scopes.global');
        $modelScopes = config('communication.scopes.communication_channels');

        if (!$modelScopes) {
            $modelScopes = [];
        }
        if (!$globalScopes) {
            $globalScopes = [];
        }

        $scopes = array_merge(
            $globalScopes,
            $modelScopes
        );

        if ($scopes) {
            foreach ($scopes as $scope) {
                static::addGlobalScope(app($scope));
            }
        }
    }

    public function emailTemplates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\NextDeveloper\CRM\Database\Models\EmailTemplates::class);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
