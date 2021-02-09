<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseAPIController;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Bot\CreateBotRequest;
use App\Models\Bot;
use Illuminate\Http\Response;
use App\Http\Requests\Bot\UpdateBotRequest;
use App\Http\Resources\BotResource;
use App\Http\Requests\Bot\UpdatePlatformConfiguration;
use Illuminate\Support\Facades\DB;
use App\Constants\PlatformType;
use App\Models\TelegramConfiguration;
use App\Services\TelegramServices;
use App\Models\SlackConfiguration;
use App\Models\MessengerConfiguration;
use App\Http\Resources\BotConnectionResource;

class BotController extends BaseAPIController
{

    /**
     * Return bot collection of logged in user
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|mixed
     */
    public function index()
    {
        try {
            $bots = Bot::where('user_id',auth()->user()->id)->paginate(8);

            return BotResource::collection($bots);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to retrieve bots.');
        }
    }

    /**
     * Create a bot
     *
     * @param CreateBotRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(CreateBotRequest $request)
    {
        try {
            $bot = Bot::create($request->validatedData());

            return $this->sendResponse(new BotResource($bot), 'Bot created successfully.', Response::HTTP_CREATED);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to create bot.');
        }
    }

    /**
     * Delete bot
     *
     * @param Bot $bot
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function destroy(Bot $bot)
    {
        try {
            $bot->delete();

            return $this->sendResponse([], 'Bot created successfully.', Response::HTTP_NO_CONTENT);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to delete bot.');
        }
    }

    public function update(Bot $bot, UpdateBotRequest $request)
    {
        try {
            $bot->update($request->validatedData());

            return $this->sendResponse(new BotResource($bot), 'Bot updated successfully.', Response::HTTP_ACCEPTED);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to update bot.');
        }

    }

    public function show(Bot $bot)
    {
        return $this->sendResponse(new BotResource($bot), 'Bot retrieved successfully.', Response::HTTP_OK);
    }

    public function getConfigurations(Bot $bot) {
        try {
            return $this->sendResponse(BotConnectionResource::collection($bot->configurations), 'Bot Configuration updated successfully.', Response::HTTP_ACCEPTED);
        } catch (\Exception $exception) {
            Log::error($exception);
            return $this->sendError('Failed to update configuration.');
        }
    }

    public function updateConfiguration(Bot $bot, UpdatePlatformConfiguration $request) {
        try {
            DB::beginTransaction();
            if($request->get('platform') == PlatformType::TELEGRAM) {
                $config = TelegramConfiguration::updateOrCreate(['bot_id' => $bot->id], $request->validatedData());
                $config->connections()->updateOrCreate(['bot_id' => $bot->id, 'connectable_type' => TelegramConfiguration::class]);
                (new TelegramServices())->register($config->access_token, $bot->uuid);
            } else if($request->get('platform') == PlatformType::SLACK) {
                $config = SlackConfiguration::updateOrCreate(['bot_id' => $bot->id], $request->validatedData());
                $config->connections()->updateOrCreate(['bot_id' => $bot->id, 'connectable_type' => SlackConfiguration::class]);
            } else if($request->get('platform') == PlatformType::MESSENGER) {
                $config = MessengerConfiguration::updateOrCreate(['bot_id' => $bot->id], $request->validatedData());
                $config->connections()->updateOrCreate(['bot_id' => $bot->id, 'connectable_type' => MessengerConfiguration::class]);
            }
            DB::commit();
            return $this->sendResponse([], 'Bot Configuration updated successfully.', Response::HTTP_ACCEPTED);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return $this->sendError('Failed to update configuration.');
        }
    }
}
