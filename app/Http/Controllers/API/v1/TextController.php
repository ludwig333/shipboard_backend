<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseAPIController;
use App\Http\Requests\Text\CreateTextRequest;
use App\Models\Text;
use App\Models\Content;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TextResource;
use Illuminate\Http\Response;

class TextController extends BaseAPIController
{
    /**
     * Return messages collection of given flow
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|mixed
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Add text to  a message
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(CreateTextRequest $request)
    {
        try {
            DB::beginTransaction();

            $message = Message::where('uuid', $request->input('message'))->first();

            $text = Text::create([
                'body' => $request->input('body')
            ]);

            Content::create([
                'message_id' => $message->id,
                'content_type' => Text::class,
                'content_id' => $text->id,
                'index' => $request->input('index')
            ]);
            DB::commit();

            return $this->sendResponse(new TextResource($message), 'Message created successfully.', Response::HTTP_CREATED);

        } catch (\Throwable $exception) {
            DB::rollBack();
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
            $message->update($request->all());

            return $this->sendResponse(new MessageResource($message), 'Message updated successfully.', Response::HTTP_ACCEPTED);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to update message.');
        }

    }
}
