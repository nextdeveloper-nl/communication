<?php

namespace NextDeveloper\Communication\Channels;

use InvalidArgumentException;

/**
 * Interface for communication channels
 *
 * Defines the contract that all communication channels must follow.
 */
interface ChannelAbstract
{
    /**
     * Name of the channel
     */
    public const NAME = '';

    /**
     * Required fields for the channel configuration
     *
     * @var array<string, string>
     */
    public const FIELDS = [];

    /**
     * Sends the message through the channel
     *
     * @param mixed $message
     * @throws \Exception If the message cannot be sent
     */
    public function send(mixed $message): void;

    /**
     * Validates the configuration
     *
     * @param array<string, mixed> $config
     * @return bool
     */
    public function validateConfig(array $config): bool;
}
