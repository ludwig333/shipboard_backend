<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseAPIController;
use App\Http\Requests\Flow\CreateFlowRequest;
use App\Models\Flow;
use App\Http\Resources\FlowResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Models\Bot;
use App\Http\Requests\Flow\UpdateFlowRequest;
use Illuminate\Support\Facades\Artisan;


class FlowController extends BaseAPIController
{
    /**
     * Return flows collection of given bot
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|mixed
     */
    public function index(Request $request)
    {
        try {
            $bot = Bot::where('uuid', $request->input('bot'))->firstOrFail();
            $flows = Flow::where('bot_id', $bot->id)->paginate(10);

            return FlowResource::collection($flows);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to retrieve bots.');
        }
    }

    /**
     * Return detail of flow
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|mixed
     */
    public function show(Flow $flow)
    {
        try {
            return $this->sendResponse(new FlowResource($flow, true), 'Flow retrieved successfully.', Response::HTTP_ACCEPTED);
        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to retrieve bot.');
        }
    }

    /**
     * Create a flow under bot
     *
     * @param CreateFlowRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(CreateFlowRequest $request)
    {
        try {
            $flow = Flow::create($request->validatedData());

            return $this->sendResponse(new FlowResource($flow), 'Flow created successfully.', Response::HTTP_CREATED);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to create flow.');
        }
    }

    /**
     * Delete flow
     *
     * @param Flow $flow
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function destroy(Flow $flow)
    {
        try {
            $flow->delete();

            return $this->sendResponse([], 'Flow deleted successfully.', Response::HTTP_NO_CONTENT);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to delete flow.');
        }
    }

    public function update(Flow $flow, UpdateFlowRequest $request)
    {
        try {
            $flow->update($request->validatedData());

            return $this->sendResponse(new FlowResource($flow), 'Flow updated successfully.', Response::HTTP_ACCEPTED);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to update flow.');
        }

    }

    public function publish(Flow $flow) {
        try {
            Artisan::call('publish:flow', [
                'flow' => $flow->uuid
            ]);
            return $this->sendResponse([], 'Flow published successfully.', Response::HTTP_ACCEPTED);
        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Failed to update flow.');
        }
    }
}
