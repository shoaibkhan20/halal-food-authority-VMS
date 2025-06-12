<?php

namespace App\Http\Middleware;

use Closure;
use Detection\MobileDetect; // ✅ Correct namespace

class BlockMobileDevices
{
    public function handle($request, Closure $next)
    {
        $detect = new MobileDetect;

        if ($detect->isMobile() || $detect->isTablet()) {
            abort(403, 'Mobile and tablet access is not allowed.');
        }

        return $next($request);
    }
}
