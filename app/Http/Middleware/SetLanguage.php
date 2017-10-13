<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $domain = parse_url($request->url(), PHP_URL_HOST);
        $elements = explode('.', $domain);
        $tld = $elements[count($elements) - 1];

        $language = App::getLocale();

        switch ($tld) {

            case 'fr':
                $language = 'fr';
                break;

            case 'nl':
                $language = 'nl';
                break;
        }

        App::setLocale($language);

        return $next($request);
    }
}
