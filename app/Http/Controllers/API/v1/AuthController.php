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
use Illuminate\Support\Facades\Log;
use App\Models\MessengerConfiguration;
use App\Models\TelegramConfiguration;
use App\Models\SlackConfiguration;
use App\Models\Bot;

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
                return $this->sendError('Invalid Credentials', [], Response::HTTP_UNPROCESSABLE_ENTITY);
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
            'name' => $user->name,
            'email' => $user->email,
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


    public function getOverview() {
        try {
            $user = auth()->user();
            $inactiveBots = 0;
            $activeBots = 0;
            $totalBots = 0;
            $data = [
                'doughnut' => [],
                'colors' =>  ['#00C6FF', '#0088CC', '#4A154B'],
                'active' => 0,
                'inactive' => 0,
                'total' => 0
            ];
            $botConnections = DB::table('bot_connections')
                ->select(DB::raw("COUNT(*) as total"), 'connectable_type')
                ->leftJoin('bots', 'bots.id', '=', 'bot_connections.bot_id')
                ->where('bots.user_id', $user->id)
                ->groupBy(['connectable_type'])
                ->get();
            $bots = Bot::where('user_id', $user->id)->get();
            foreach($bots as $bot) {
                $totalBots++;
                $configurations = $bot->configurations;
                if($configurations) {
                    $statuses = $configurations->pluck('connectable.connect_status');
                    foreach($statuses as $status) {
                        if ($status == 1) {
                            $activeBots++;
                        } else {
                            $inactiveBots++;
                        }
                    }

                }
            }
            foreach($botConnections as $connection) {
              if ($connection->connectable_type == MessengerConfiguration::class) {
                  array_push($data['doughnut'], [
                      'name' => 'Messenger',
                      'value' =>  $connection->total
                  ]);
                } else if ($connection->connectable_type == TelegramConfiguration::class) {
                  array_push($data['doughnut'], [
                      'name' => 'Telegram',
                      'value' =>  $connection->total
                  ]);
                } else if ($connection->connectable_type == SlackConfiguration::class) {
                    array_push($data['doughnut'], [
                      'name' => 'Slack',
                      'value' => $connection->total
                  ]);
                }
            }
           $data['active'] = $activeBots;
           $data['inactive'] = $inactiveBots;
           $data['total'] = $totalBots;
            return $this->sendResponse([(object)$data], 'Bot Overview retrieved successfully.', Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->sendError('Failed to retrieve overview.');
        }
    }
}
