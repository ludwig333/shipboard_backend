<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseAPIController;
use App\Http\Requests\Image\CreateImageRequest;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Models\Image;
use App\Models\Content;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Image\UpdateImageRequest;
use App\Models\ImageStore;
use Symfony\Component\Console\Input\Input;
use App\Http\Requests\Image\UploadImageRequest;

class ImageController extends BaseAPIController
{
    /**
     * No method for image
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|mixed
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Add image to  a message
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function store(CreateImageRequest $request)
    {
        try {
            DB::beginTransaction();
            $message = Message::where('uuid', $request->input('message'))->first();

            $image = Image::create();

            Content::create([
                'message_id' => $message->id,
                'content_type' => Image::class,
                'content_id' => $image->id,
                'index' => $request->input('position')
            ]);
            DB::commit();

            return $this->sendResponse(new ImageResource($image), 'Image created successfully.', Response::HTTP_CREATED);

        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to create image.');
        }
    }

    /**
     * Delete message
     *
     * @param Image $image
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function destroy(Image $image)
    {
        try {
            DB::beginTransaction();
            //We delete the content which will automatically delete the image and also the image store if exists
            $image->content->delete();
            DB::commit();
            return $this->sendResponse([], 'Image deleted successfully.', Response::HTTP_NO_CONTENT);

        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to delete image.');
        }
    }

    public function uploadImage(Image $image, UploadImageRequest $request)
    {
        try {
            DB::beginTransaction();

            if($request->hasFile("image")) {

                $imageName = time().'.'.$request->file('image')->extension();

                $path = $request->file('image')->storeAs(
                    ''.auth()->user()->id, $imageName,
                    'public'
                );

                $storeId = ImageStore::create([
                    'name' => $request->input('name'),
                    'path' => $path
                ]);

                $image->update([
                    'image_store_id' => $storeId->id
                ]);
            }
            DB::commit();

            return $this->sendResponse(new ImageResource($image), 'Image updated successfully.', Response::HTTP_ACCEPTED);

        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to upload image.');
        }

    }
}
