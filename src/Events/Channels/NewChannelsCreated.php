<?php

namespace NextDeveloper\Communication\Events\Channels;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use NextDeveloper\Communication\Database\Models\Channels;
use NextDeveloper\Communication\Services\ChannelsService;

class NewChannelsCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Channels
     */
    public Channels $_model;

    /**
     * @var int|null
     */
    protected ?int $timestamp = null;

    public function __construct(Channels $model)
    {
        $this->_model = $model;
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        // Send verification code to the user
        ChannelsService::sendCode($this->_model->uuid);
    }
    
}