<?php

namespace NextDeveloper\Communication\Http\Controllers\Tracking;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use NextDeveloper\Communication\Services\EmailTrackingService;

/**
 * Public, unauthenticated endpoints hit by email clients (leo4#1254). Identity comes from the signed token only.
 */
class EmailTrackingController extends Controller
{
    /** 1x1 transparent GIF */
    private const PIXEL = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    /** GET /public/communication/t/o/{token}.gif: always answers with the pixel, even for a bad token. */
    public function open(Request $request, string $token)
    {
        EmailTrackingService::recordOpen(preg_replace('/\.gif$/', '', $token), $request->userAgent(), $request->ip());

        return response(base64_decode(self::PIXEL), 200, [
            'Content-Type' => 'image/gif',
            'Content-Length' => strlen(base64_decode(self::PIXEL)),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /** GET /public/communication/t/c/{token}: redirects only to the URL signed into the token. */
    public function click(Request $request, string $token)
    {
        $url = EmailTrackingService::recordClick($token, $request->userAgent(), $request->ip());

        if (! $url) {
            abort(404);
        }

        return redirect()->away($url, 302, ['Cache-Control' => 'no-store']);
    }
}
