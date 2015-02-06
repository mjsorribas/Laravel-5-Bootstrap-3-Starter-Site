<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;

use App\AssignedRoles;

class Admin implements Middleware {

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * The response factory implementation.
     *
     * @var ResponseFactory
     */
    protected $response;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @param  ResponseFactory  $response
     * @return void
     */
    public function __construct(Guard $auth,
                                ResponseFactory $response)
    {
        $this->auth = $auth;
        $this->response = $response;
    }
    /**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        if ($this->auth->check())
        {
            $admin = 0;
            $user_roles = AssignedRoles::join('roles','assigned_roles.role_id','=','roles.id')
            ->where('user_id', $this->auth->user()->id)->select('roles.is_admin')->get();
            foreach($user_roles as $item)
            {
                if($item->is_admin==1)
                {
                    $admin=1;
                }
            }
            if($admin==0){
                return $this->response->redirectTo('/');
            }
            return $next($request);
        }
        return $this->response->redirectTo('/');
	}

}
