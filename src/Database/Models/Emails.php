<?php

namespace NextDeveloper\Communication\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Communication\Database\Observers\EmailsObserver;

/**
 * Emails model.
 *
 * @package  NextDeveloper\Communication\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property string $from_email_address
 * @property array $to
 * @property array $cc
 * @property array $bcc
 * @property string $subject
 * @property string $body
 * @property array $attachments
 * @property array $headers
 * @property $delivery_results
 * @property integer $iam_user_id
 * @property integer $iam_account_id
 * @property boolean $is_marketing_email
 * @property \Carbon\Carbon $deliver_at
 * @property \Carbon\Carbon $delivered_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property integer $communication_channel_id
 */
class Emails extends Model
{
    use Filterable, CleanCache, Taggable;
    use UuidId;
    use SoftDeletes;


    public $timestamps = true;




    protected $table = 'communication_emails';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'from_email_address',
            'to',
            'cc',
            'bcc',
            'subject',
            'body',
            'attachments',
            'headers',
            'delivery_results',
            'iam_user_id',
            'iam_account_id',
            'is_marketing_email',
            'deliver_at',
            'delivered_at',
            'communication_channel_id',
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
    'from_email_address' => 'string',
    'to' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'cc' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'bcc' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'subject' => 'string',
    'body' => 'string',
    'attachments' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'headers' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'delivery_results' => 'array',
    'is_marketing_email' => 'boolean',
    'deliver_at' => 'datetime',
    'delivered_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    'communication_channel_id' => 'integer',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'deliver_at',
    'delivered_at',
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
        parent::observe(EmailsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('communication.scopes.global');
        $modelScopes = config('communication.scopes.communication_emails');

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
