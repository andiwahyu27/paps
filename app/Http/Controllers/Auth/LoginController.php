<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\StateHelper;
use App\Helpers\JwtHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Redirect to Google
    public function redirectToProvider()
    {
        return (Socialite::driver('google')->redirect());
    }

    // Callback from Google
    public function handleProviderCallback(Request $request)
    {
        try {
            $user_google = Socialite::driver('google')->user();
            $user = User::where('email', $user_google->getEmail())->first();
            // $pegawai = PusdiklatPegawai::where('gmail', $user_google->getEmail())->first();
            if ($user) {
                Auth::login($user, true);
                return redirect()->route('home');
            } else {
                return redirect()->route('login')
                    ->with('error', 'Akun ' . $user_google->getEmail() . ' belum terdaftar dalam sistem, harap hubungi admin.');;
            }
        } catch (\Exception $e) {
            Log::error('Google authentication failed.', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Login Google gagal. Silakan coba kembali.');
        }
    }

    // SSO Gojags
    public function redirectToGojags($type)
    {
        if (!config('services.gojags.url') || !config('services.gojags.client_uuid')) {
            Log::error('SSO GOJAGS: Missing required environment variables (APP_GOJAGS_URL or PAPS_UUID).');
            return redirect()->route('login')->with('error', 'Konfigurasi SSO tidak lengkap. Harap hubungi administrator.');
        }

        $state = StateHelper::generateState();
        $uuid = config('services.gojags.client_uuid');
        $gojagsUrl = rtrim(config('services.gojags.url'), '/');

        $query = http_build_query([
            'uuid' => $uuid,
            'state' => $state,
            'realm' => $type,
        ]);

        $redirectUrl = $gojagsUrl . '/auth/client?' . $query;

        Log::info('SSO GOJAGS: Redirecting to Gojags.', ['url' => $redirectUrl]);

        return redirect()->to($redirectUrl);
    }

    public function authenticateWithGojags(Request $request)
    {
        $token = $request->query('token');
        $state = $request->query('state');

        if (!$token) {
            Log::warning('SSO GOJAGS: Token missing from request.');
            return redirect()->route('login')->with('error', 'Login gagal: Token tidak ditemukan.');
        }

        // Verify state to prevent CSRF
        $sessionState = StateHelper::getState();
        if (!$state || $state !== $sessionState) {
            Log::warning('SSO GOJAGS: Invalid state.', ['request_state' => $state, 'session_state' => $sessionState]);
            return redirect()->route('login')->with('error', 'Login gagal: State tidak valid.');
        }

        try {
            $userData = JwtHelper::validateToken($token);

            if (!$userData) {
                            Log::warning('SSO GOJAGS: Invalid token.', [
                'ip' => $request->ip(),
            ]);
                return redirect()->route('login')->with('error', 'Login gagal: Token tidak valid.');
            }

            if (!isset($userData->email) || !isset($userData->name)) {
                Log::warning('SSO GOJAGS: Incomplete user data from token.', ['user_data' => $userData]);
                return redirect()->route('login')->with('error', 'Login gagal: Data pengguna tidak lengkap.');
            }

            $email = strtolower(trim($userData->email));

            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if (!$user) {
                // User tidak terdaftar: arahkan ke halaman informasi
                Log::warning('SSO GOJAGS: User not registered.', ['email' => $userData->email]);
                return redirect()->route('login.unregistered')
                    ->with('email', $userData->email);
            }

            // User lama: hanya update name, jangan sentuh role
            $user->update([
                'name' => $userData->name,
            ]);

            auth()->login($user, false);
            $sessionState = StateHelper::getState();
            StateHelper::clearState();

            if (
                !$state ||
                !$sessionState ||
                !hash_equals($sessionState, $state)
            ) {
                // gagal
            }

            Log::info('SSO GOJAGS: User authenticated successfully.', ['email' => $user->email]);

            return redirect()->intended($this->redirectTo);
        } catch (Exception $e) {
            Log::error('SSO GOJAGS: Authentication failed.', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
        }
    }

    public function loginError()
    {
        return view('auth.register');
    }

    public function unregistered()
    {
        return view('auth.unregistered');
    }
}
