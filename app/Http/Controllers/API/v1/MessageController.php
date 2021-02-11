<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseAPIController;
use App\Models\Flow;
use App\Http\Resources\FlowResource;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Message;
use Illuminate\Http\Response;
use App\Http\Resources\MessageResource;
use App\Http\Requests\UpdateMessageRequest;
use Illuminate\Support\Facades\DB;

class MessageController extends BaseAPIController
{
    /**
     * Return messages collection of given flow
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|mixed
     */
    public function index(Request $request)
    {
        try {
            $flow = Flow::where('uuid', $request->input('flow'))->first();
            if($flow) {
                $messages = Message::where('flow_id', $flow->id)->get();
                return MessageResource::collection($messages);
            }
        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to retrieve messages.');
        }
    }

    /**
     * Create a message under flow
     *
     * @param CreateMessageRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(CreateMessageRequest $request)
    {
        try {
            $message = Message::create($request->validatedData());

            return $this->sendResponse(new MessageResource($message), 'Message created successfully.', Response::HTTP_CREATED);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to create message.');
        }
    }

    /**
     * Delete message
     *
     * @param Flow $flow
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function destroy(Message $message)
    {
        try {
            $message->delete();

            return $this->sendResponse([], 'Message deleted successfully.', Response::HTTP_NO_CONTENT);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to delete message.');
        }
    }

    public function update(Message $message, UpdateMessageRequest $request)
    {
        try {
            $message->update($request->validatedData());

            return $this->sendResponse(new MessageResource($message), 'Message updated successfully.', Response::HTTP_ACCEPTED);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to update message.');
        }

    }

    /**
     * Create a new message and connect this message to the requested message edge
     * @param Message $message
     */
    public function createAndConnect(Message $message, CreateMessageRequest $request) {
        try {
            DB::beginTransaction();
            $newMessage = Message::create($request->validatedData());
            $message->update([
                'next_message_id' => $newMessage->id
            ]);
            DB::commit();
            return $this->sendResponse(new MessageResource($newMessage), 'Message created and connected successfully.', Response::HTTP_CREATED);

        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to create and connect message.');
        }
    }

    public function connectFlow(Message $message, Request $request) {
        try {
            //Same function is used to update flow and remove flow connection
            $this->validate($request, [
                'flow' => 'sometimes|exists:flows,uuid'
            ]);
            DB::beginTransaction();
            $nextMessageId = 0;
            if($request->has('flow')) {
                $flow = Flow::where('uuid', $request->input('flow'))->first();
                $nextMessage = $flow->messages->first();
                $nextMessageId = $nextMessage->id;
            }
            $message->update([
                'next_message_id' => $nextMessageId
            ]);
            DB::commit();
            return $this->sendResponse([], 'Flow connected to message successfully.', Response::HTTP_ACCEPTED);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to connect flow to message.');
        }
    }
}
