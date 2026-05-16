<?php

namespace NextDeveloper\Communication\Common\Settings;

/**
 * Centralised configuration schema for every supported channel type.
 *
 * Each field definition carries enough metadata for the frontend to render
 * the correct form control without any additional knowledge of the channel:
 *
 *   key      – the configuration key stored in communication_channels.configuration
 *   label    – human-readable label for the form field
 *   type     – text | password | number | select | boolean | textarea
 *   required – whether the field must be supplied
 *   default  – suggested default value (null when none)
 *   options  – key/label pairs for select fields (empty otherwise)
 *   hint     – optional explanatory text shown below the field
 */
class ChannelSettings
{
    public const TYPE_SMTP             = 'smtp';
    public const TYPE_IMAP             = 'imap';
    public const TYPE_POP3             = 'pop3';
    public const TYPE_GMAIL            = 'gmail';
    public const TYPE_GOOGLE_WORKSPACE = 'google_workspace';
    public const TYPE_MATTERMOST       = 'mattermost';
    public const TYPE_SMS              = 'sms';

    /**
     * Returns the field schema for the given channel type, or null when the
     * type is not recognised.
     *
     * @return array<int, array<string, mixed>>|null
     */
    public static function forType(string $type): ?array
    {
        return self::all()[$type] ?? null;
    }

    /**
     * Returns field schemas for every registered channel type, keyed by type.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public static function all(): array
    {
        return [
            self::TYPE_SMTP             => self::smtp(),
            self::TYPE_IMAP             => self::imap(),
            self::TYPE_POP3             => self::pop3(),
            self::TYPE_GMAIL            => self::gmail(),
            self::TYPE_GOOGLE_WORKSPACE => self::googleWorkspace(),
            self::TYPE_MATTERMOST       => self::mattermost(),
            self::TYPE_SMS              => self::sms(),
        ];
    }

    // -------------------------------------------------------------------------
    // Channel schemas
    // -------------------------------------------------------------------------

    private static function smtp(): array
    {
        return [
            self::field('host',         'SMTP Host',       'text',     true,  null,    [],                              'Hostname or IP of your outgoing mail server.'),
            self::field('port',         'Port',            'number',   true,  587,     [],                              'Common values: 25 (plain), 465 (SSL), 587 (STARTTLS).'),
            self::field('encryption',   'Encryption',      'select',   true,  'tls',   self::encryptionOptions(),        ''),
            self::field('username',     'Username',        'text',     true,  null,    [],                              'Usually the full email address used to authenticate.'),
            self::field('password',     'Password',        'password', true,  null,    [],                              ''),
            self::field('from_address', 'From Address',    'text',     false, null,    [],                              'Sender address shown in the From header.'),
            self::field('from_name',    'From Name',       'text',     false, null,    [],                              'Sender name shown in the From header.'),
            self::field('timeout',      'Timeout (s)',     'number',   false, 30,      [],                              'Connection timeout in seconds.'),
        ];
    }

    private static function imap(): array
    {
        return [
            self::field('host',       'IMAP Host',        'text',     true,  null,    [],                              'Hostname of your incoming mail server.'),
            self::field('port',       'Port',             'number',   true,  993,     [],                              'Common values: 143 (plain/STARTTLS), 993 (SSL).'),
            self::field('encryption', 'Encryption',       'select',   true,  'ssl',   self::encryptionOptions(),        ''),
            self::field('username',   'Username',         'text',     true,  null,    [],                              'Usually the full email address.'),
            self::field('password',   'Password',         'password', true,  null,    [],                              ''),
            self::field('folder',     'Mailbox Folder',   'text',     false, 'INBOX', [],                              'Folder to watch for incoming messages.'),
            self::field('timeout',    'Timeout (s)',       'number',   false, 30,      [],                              'Connection timeout in seconds.'),
        ];
    }

    private static function pop3(): array
    {
        return [
            self::field('host',            'POP3 Host',           'text',     true,  null,    [],                       'Hostname of your incoming mail server.'),
            self::field('port',            'Port',                'number',   true,  995,     [],                       'Common values: 110 (plain), 995 (SSL).'),
            self::field('encryption',      'Encryption',          'select',   true,  'ssl',   self::encryptionOptions(), ''),
            self::field('username',        'Username',            'text',     true,  null,    [],                       'Usually the full email address.'),
            self::field('password',        'Password',            'password', true,  null,    [],                       ''),
            self::field('leave_on_server', 'Leave on Server',     'boolean',  false, false,   [],                       'Keep messages on the server after retrieval.'),
            self::field('timeout',         'Timeout (s)',          'number',   false, 30,      [],                       'Connection timeout in seconds.'),
        ];
    }

    private static function gmail(): array
    {
        return [
            self::field('client_id',          'OAuth Client ID',     'text',     true,  null, [], 'From Google Cloud Console → Credentials.'),
            self::field('client_secret',       'OAuth Client Secret', 'password', true,  null, [], ''),
            self::field('redirect_uri',        'Redirect URI',        'text',     true,  null, [], 'Must match the URI registered in Google Cloud Console.'),
            self::field('access_token',        'Access Token',        'password', true,  null, [], 'OAuth 2.0 access token obtained after user consent.'),
            self::field('refresh_token',       'Refresh Token',       'password', true,  null, [], 'Used to obtain a new access token when the current one expires.'),
            self::field('max_emails_per_day',  'Max Emails / Day',    'number',   false, 50,   [], 'Gmail free accounts are limited to ~500/day; stay conservative.'),
        ];
    }

    private static function googleWorkspace(): array
    {
        return [
            self::field('client_id',          'OAuth Client ID',      'text',     true,  null, [], 'From Google Cloud Console → Credentials.'),
            self::field('client_secret',       'OAuth Client Secret',  'password', true,  null, [], ''),
            self::field('redirect_uri',        'Redirect URI',         'text',     true,  null, [], 'Must match the URI registered in Google Cloud Console.'),
            self::field('access_token',        'Access Token',         'password', true,  null, [], 'OAuth 2.0 access token obtained after user consent.'),
            self::field('refresh_token',       'Refresh Token',        'password', true,  null, [], 'Used to obtain a new access token when the current one expires.'),
            self::field('admin_email',         'Admin Email',          'text',     false, null, [], 'Domain admin address for service-account impersonation (optional).'),
            self::field('max_emails_per_day',  'Max Emails / Day',     'number',   false, 50,   [], 'Google Workspace accounts are limited to ~2000/day; cap at a safe value.'),
        ];
    }

    private static function mattermost(): array
    {
        return [
            self::field('webhook_url', 'Incoming Webhook URL', 'text',     true,  null, [], 'Created under Mattermost → Integrations → Incoming Webhooks.'),
            self::field('channel',     'Channel',              'text',     false, null, [], 'Override the default channel the webhook posts to.'),
            self::field('username',    'Bot Username',         'text',     false, null, [], 'Override the default bot display name.'),
            self::field('icon_url',    'Bot Icon URL',         'text',     false, null, [], 'Override the default bot icon.'),
        ];
    }

    private static function sms(): array
    {
        return [
            self::field('provider',      'SMS Provider',     'select',   true,  'twilio', self::smsProviderOptions(), ''),
            self::field('account_sid',   'Account SID',      'text',     true,  null,     [],                         'Twilio Account SID (or equivalent for other providers).'),
            self::field('auth_token',    'Auth Token',       'password', true,  null,     [],                         'Twilio Auth Token (or API key for other providers).'),
            self::field('phone_number',  'From Number',      'text',     true,  null,     [],                         'E.164 format, e.g. +12025551234.'),
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Builds a single normalised field definition.
     *
     * @param array<string, string> $options
     */
    private static function field(
        string $key,
        string $label,
        string $type,
        bool   $required,
        mixed  $default,
        array  $options,
        string $hint
    ): array {
        return compact('key', 'label', 'type', 'required', 'default', 'options', 'hint');
    }

    /** @return array<string, string> */
    private static function encryptionOptions(): array
    {
        return [
            'tls'  => 'STARTTLS',
            'ssl'  => 'SSL / TLS',
            'none' => 'None (not recommended)',
        ];
    }

    /** @return array<string, string> */
    private static function smsProviderOptions(): array
    {
        return [
            'twilio' => 'Twilio',
        ];
    }
}
