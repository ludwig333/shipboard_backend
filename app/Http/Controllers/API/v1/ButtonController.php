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

    public function update(Button $button) {
        try {
            DB::beginTransaction();

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
}
