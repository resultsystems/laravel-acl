<?php

namespace ResultSystems\Acl\Middlewares;

use Closure;

/**
 * Class NeedsPermissionMiddleware.
 */
class NeedsPermissionMiddleware extends AbstractAclMiddleware
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param callable                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions = null, $any = true, $branch_id = null)
    {
        $anyPermission = $any;
        if (is_null($permissions)) {
            $permissions   = $this->getPermissions($request);
            $anyPermission = $this->getAny($request);
            $branch_id     = $this->getBranchId($request);
        } else {
            $permissions = explode('|', $permissions); // Laravel 5.1 - Using parameters
            if (isset($permissions[1])) {
                $anyPermission = $this->getBool($permissions[1]);
            }
            if (isset($permissions[2])) {
                $branch_id = (int) $permissions[2];
            }
        }
        if (is_null($this->user)) {
            return $this->forbiddenResponse();
        }

        $hasPermission = $this->user->hasPermission($permissions, $anyPermission, $branch_id);
        if (!$hasPermission) {
            return $this->forbiddenResponse();
        }

        return $next($request);
    }

    public function getBool($value)
    {
        return ($value !== "false" and $value !== 0 and $value !== "0");
    }
}
