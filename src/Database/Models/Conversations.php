<?php

namespace NextDeveloper\Communication\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Communication\Database\Observers\ConversationsObserver;

/**
 * Conversations model.
 *
 * @package  NextDeveloper\Communication\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $iam_user_id
 * @property integer $communication_bot_id
 * @property string $message
 * @property integer $direction
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property integer $communicaton_session_id
 * @property string $update_id
 */
class Conversations extends Model
{
    use Filterable, CleanCache, Taggable;
    use UuidId;
    use SoftDeletes;


    public $timestamps = true;




    protected $table = 'communication_conversations';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'iam_user_id',
            'communication_bot_id',
            'message',
            'direction',
            'communicaton_session_id',
            'update_id',
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
    'communication_bot_id' => 'integer',
    'message' => 'string',
    'direction' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    'communicaton_session_id' => 'integer',
    'update_id' => 'string',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
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
        parent::observe(ConversationsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('communication.scopes.global');
        $modelScopes = config('communication.scopes.communication_conversations');

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

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}
