<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Traits\LogsActivity;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, LogsActivity;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Handle redirection after authentication.
     */
    public function login(\Illuminate\Http\Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // Attempt to log in as User (Web Guard)
        if ($this->guard()->attempt($this->credentials($request), $request->filled('remember'))) {
            $user = $this->guard()->user();
            $this->logActivity('Login', "User {$user->name} logged in.");
            return $this->sendLoginResponse($request);
        }

        // Attempt to log in as Client (Client Guard)
        if (\Illuminate\Support\Facades\Auth::guard('client')->attempt($this->credentials($request), $request->filled('remember'))) {
            $client = \Illuminate\Support\Facades\Auth::guard('client')->user();
            $this->logActivity('Login', "Client {$client->first_name} {$client->last_name} logged in.");
            
            // Regenerate session for client
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            
            // Redirect to client order page
            return redirect()->route('orders.create');
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Handle redirection after authentication.
     */
    protected function authenticated($request, $user)
    {
        // This is primarily for the 'web' guard success
        // Clients are handled directly in the login method above
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        if (\Illuminate\Support\Facades\Auth::guard('web')->check()) {
            $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
            $this->logActivity('Logout', "User {$user->name} logged out.");
            $this->guard('web')->logout();
        }
        
        if (\Illuminate\Support\Facades\Auth::guard('client')->check()) {
            $client = \Illuminate\Support\Facades\Auth::guard('client')->user();
            $this->logActivity('Logout', "Client {$client->first_name} {$client->last_name} logged out.");
            $this->guard('client')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:client')->except('logout');
    }
}
