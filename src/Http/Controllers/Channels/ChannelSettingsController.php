<?php

namespace NextDeveloper\Communication\Http\Controllers\Channels;

use NextDeveloper\Communication\Common\Settings\ChannelSettings;
use NextDeveloper\Communication\Http\Controllers\AbstractController;

class ChannelSettingsController extends AbstractController
{
    /**
     * Returns field schemas for every supported channel type so the frontend
     * can render the correct configuration form dynamically.
     *
     * GET /communication/channel-settings
     *
     * Response shape:
     *   {
     *     "smtp":             [ { key, label, type, required, default, options, hint }, ... ],
     *     "imap":             [ ... ],
     *     "pop3":             [ ... ],
     *     "gmail":            [ ... ],
     *     "google_workspace": [ ... ],
     *     "mattermost":       [ ... ],
     *     "sms":              [ ... ]
     *   }
     */
    public function index()
    {
        return $this->withArray(ChannelSettings::all());
    }

    /**
     * Returns the field schema for a single channel type.
     *
     * GET /communication/channel-settings/{type}
     */
    public function show(string $type)
    {
        $settings = ChannelSettings::forType($type);

        if ($settings === null) {
            return $this->withArray(['error' => "Unknown channel type: {$type}"], 404);
        }

        return $this->withArray($settings);
    }
}
