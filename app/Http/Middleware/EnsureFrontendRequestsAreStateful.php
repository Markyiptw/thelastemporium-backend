<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Pipeline;

class EnsureFrontendRequestsAreStateful
{
    /**
     * Handle the incoming requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        $this->configureSecureCookieSessions();

        return (new Pipeline(app()))->send($request)->through(static::fromFrontend($request) ? [
            function ($request, $next) {
                $request->attributes->set('sanctum', true);

                return $next($request);
            },
            config('sanctum.middleware.encrypt_cookies', \Illuminate\Cookie\Middleware\EncryptCookies::class),
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            config('sanctum.middleware.verify_csrf_token', \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class),
        ] : [])->then(function ($request) use ($next) {
            return $next($request);
        });
    }

    /**
     * Configure secure cookie sessions.
     *
     * @return void
     */
    protected function configureSecureCookieSessions()
    {
        config([
            'session.http_only' => true,
            'session.same_site' => 'lax',
        ]);
    }

    /**
     * Determine if the given request is from the first-party application frontend.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function fromFrontend($request)
    {
        return true;
    }
}
