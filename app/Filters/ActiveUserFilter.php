<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Immediately logs out a user whose account has been deactivated
 * (Myth\Auth `active` = 0), instead of only blocking new logins.
 */
class ActiveUserFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('authentication');

        // Nothing to check when nobody is logged in.
        if (! $auth->check()) {
            return;
        }

        $user = $auth->user();

        // Deactivated account: kick the session and send back to login.
        if ($user !== null && ! $user->isActivated()) {
            $auth->logout();

            return redirect()
                ->to(route_to('login'))
                ->with('error', lang('Global.accountDeactivated'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
