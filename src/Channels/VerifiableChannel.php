<?php

namespace NextDeveloper\Communication\Channels;

/**
 * A channel that can prove its stored credentials work without delivering anything to a customer.
 *
 * validateConfig() only says that the required fields are present; verify() asks the provider.
 */
interface VerifiableChannel
{
    /**
     * Checks the stored credentials against the provider.
     *
     * @return string Short human readable description of what was confirmed
     *
     * @throws \Throwable With the provider's own error text when the check fails
     */
    public function verify(): string;
}
