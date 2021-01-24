<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseAPIController;
use App\Models\Flow;
use App\Http\Resources\FlowResource;
use Illuminate\Support\Facades\Log;

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
            $flow = Flow::findorFail($request->input('flow'));

            return FlowResource::collection($flow);

        } catch (\Throwable $exception) {
            Log::error($exception);

            return $this->sendError('Failed to retrieve bots.');
        }
    }
}
