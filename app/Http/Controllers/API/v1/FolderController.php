<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseAPIController;
use App\Models\Flow;
use App\Http\Requests\Folder\CreateFolderRequest;
use App\Models\Folder;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\FolderResource;
use App\Http\Requests\Folder\UpdateFolderRequest;
use Illuminate\Http\Response;

class FolderController extends BaseAPIController
{

    public function index(Request $request)
    {
        try {
            $folders = Folder::where('flow_id', $request->input('flow_id'))->get();

            return $this->sendResponse($folders, 'Folders fetched successfully.', Response::HTTP_OK);

        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Failed to fetch folders.');
        }
    }

    public function store(CreateFolderRequest $request)
    {
        try {
            $folder = Folder::create($request->validatedData());

            return $this->sendResponse(new FolderResource($folder), 'Folder created successfully.', Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Failed to create folder');
        }
    }

    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        try {
            $folder->update($request->validatedData());

            return $this->sendResponse(new FolderResource($folder->refresh()), 'Folder updated successfully.', Response::HTTP_ACCEPTED);

        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Failed to update folder');
        }
    }

    public function destroy(Folder $folder)
    {
        try {
            $folder->delete();

            return $this->sendResponse([], 'Folder deleted successfully.', Response::HTTP_NO_CONTENT);

        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Failed to delete folder');
        }
    }
}
