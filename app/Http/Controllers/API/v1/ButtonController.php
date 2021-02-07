<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseAPIController;
use App\Models\Button;
use App\Http\Requests\Button\CreateButtonRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TextResource;
use Illuminate\Http\Response;
use App\Http\Resources\ButtonResource;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Message;
use App\Http\Resources\MessageResource;
use App\Models\Flow;
use App\Http\Requests\Button\UpdateButtonRequest;

class ButtonController extends BaseAPIController
{
    public function store(CreateButtonRequest $request) {
        try {
            DB::beginTransaction();

            $button = Button::create($request->validatedData());

            DB::commit();

            return $this->sendResponse(new ButtonResource($button), 'Button created successfully.', Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to create button.');
        }
    }

    public function update(Button $button, UpdateButtonRequest $request) {
        try {
            DB::beginTransaction();
            $button->update($request->validatedData());
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to update button.');
        }
    }

    public function destroy(Button $button) {
        try {
            DB::beginTransaction();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to delete button.');
        }
    }

    public function createAndConnect(Button $button, CreateMessageRequest $request) {
        try {
            DB::beginTransaction();
            $newMessage = Message::create($request->validatedData());
            $button->update([
                'leads_to_message' => $newMessage->id
            ]);
            DB::commit();
            return $this->sendResponse(new MessageResource($newMessage), 'Message created and connected successfully.', Response::HTTP_CREATED);

        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to create and connect message.');
        }
    }

    public function connectFlow(Button $button,Request $request) {
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
            $button->update([
                'leads_to_message' => $nextMessageId
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
