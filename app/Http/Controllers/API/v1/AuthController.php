<?php


namespace App\Http\Controllers\API\v1;


use App\Http\Controllers\API\BaseAPIController;
use App\Models\User;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\UserRegistrationRequest;
use App\Http\Requests\Auth\UserLoginRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Http\Request;

class AuthController extends BaseAPIController
{

    /**
     * Register a new user in the application
     *
     * @param UserRegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegistrationRequest $request)
    {
       try {
           return $request->getRegistrationData();

           $user = User::create($request->getRegistrationData());

           return $this->sendResponse($this->getUserWithToken($user), 'User registered successfully.', Response::HTTP_CREATED);
       } catch (\Exception $exception) {
           return $this->sendError('Invalid data provided', $exception->getMessage(), Response::HTTP_BAD_REQUEST);
       }
    }

    /**
     * Logs in the users with valid credentials.
     *
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request)
    {
        try {
            if(auth()->attempt($request->getLoginCredentials())) {
                return $this->sendResponse($this->getUserWithToken(auth()->user()), 'Logged in successfully.', Response::HTTP_OK);
            } else {
                return $this->sendError('Invalid Credentials', [], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $exception) {
            return $this->sendError('Invalid data provided', $exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function logout()
    {
        $user = auth()->user()->token();
        $user->revoke();

        return $this->sendResponse([], 'Logged out successfully.', Response::HTTP_OK);
    }

    /**
     * Returns the data resource of logged in user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo(Request $request)
    {
        return $this->sendResponse(new UserResource($request->user()), 'Logged in user returned successfully');
    }

    /**
     * Return the valid user with access token and name
     *
     * @return array
     */
    private function getUserWithToken($user)
    {
        return [
            'token' => $user->createToken('ShipboardBotMaker')->accessToken,
            'fname' => $user->first_name,
            'lname' => $user->last_name,
        ];
    }

    public function forgot(ForgotPasswordRequest $request)
    {
        try {
            $email = $request->input('email');
            $token = Str::random(10);

            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token
            ]);

            //Send email
            Mail::send('emails.forgot-password', ['token' => $token], function(Message $message) use($email) {
               $message->to($email);
               $message->subject('Reset your password');
            });

            return $this->sendResponse(['email' => $email],'Check your email');

        } catch (\Exception $exception) {
            return $this->sendError('Something went wrong', $exception->getMessage());
        }
    }

    public function reset(ResetPasswordRequest $request)
    {
        $token = $request->input('token');

        if(!$passwordResets = DB::table('password_resets')->where('token', $token)->first()) {
            return $this->sendError('Invalid Token');
        }

        if(!$user = User::where('email', $passwordResets->email)->first()) {
            return $this->sendError('User doesn\'t exist!!');
        }

        $user->password = bcrypt($request->input('password'));
        $user->save();

        return $this->sendResponse( new UserResource($user), 'Successfully changed the password');
    }
}
