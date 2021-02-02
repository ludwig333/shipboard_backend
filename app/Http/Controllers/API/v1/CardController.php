<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseAPIController;
use App\Models\CardGroup;
use App\Models\Message;
use App\Models\Content;
use App\Http\Requests\CardGroup\CreateCardGroupRequest;
use App\Models\Card;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ImageResource;
use Illuminate\Http\Response;
use App\Http\Resources\CardGroupResource;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdateCardRequest;
use App\Http\Resources\CardResource;
use App\Http\Requests\Image\UploadImageRequest;
use App\Models\ImageStore;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CardController extends BaseAPIController
{
    /**
     * Store a newly created card group with a single card
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function createCardGroup(CreateCardGroupRequest $request)
    {
        try {
            DB::beginTransaction();
            $message = Message::where('uuid', $request->input('message'))->first();
            $cardGroup = CardGroup::create();
            Content::create([
                'message_id' => $message->id,
                'content_type' => CardGroup::class,
                'content_id' => $cardGroup->id,
                'index' => $request->input('position')
            ]);
            Card::create([
                'title' => 'Subtitle #1',
                'body' => 'This is body paragraph.',
                'group_id' => $cardGroup->id
            ]);
            DB::commit();
            return $this->sendResponse(new CardGroupResource($cardGroup), 'Card Group created successfully.', Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to create card group.');
        }
    }


    /**
     * Delete Card Group.
     * @param CardGroup $cardGroup
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function destroyCardGroup($group) {
        try {
            $cardGroup = CardGroup::where('uuid', $group)->first();
            if(!$cardGroup) {
                throw new NotFoundHttpException();
            }
            DB::beginTransaction();
            //We delete the content which will automatically delete the card group also cards and image store if exists
            $cardGroup->content->delete();
            DB::commit();
            return $this->sendResponse([], 'Card Group deleted successfully.', Response::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to delete card group.');
        }
    }

    /**
     * Add card to a card group
     * @param $group
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function addCard($group) {
        try {
            $cardGroup = CardGroup::where('uuid', $group)->firstOrFail();
            $cardCount = $cardGroup->cards->count();
            $card = Card::create([
                'title' => 'Subtitle #'. ($cardCount + 1),
                'body' => 'This is body paragraph.',
                'group_id' => $cardGroup->id
            ]);
            return $this->sendResponse( new CardResource($card, 1), 'Card created successfully.', Response::HTTP_CREATED);

        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Failed to create card.');
        }
    }

    /**
     * Update title or body of card
     * @param Card $card
     * @param UpdateCardRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function updateCard(Card $card, UpdateCardRequest $request) {
        try {
            DB::beginTransaction();
            $card->update($request->validatedData());
            DB::commit();
            return $this->sendResponse(new CardResource($card, 1), 'Card updated successfully.', Response::HTTP_CREATED);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return $this->sendError('Failed to update card.');
        }
    }

    /**
     * Upload image to the card in a card group
     * @param Card $card
     * @param UploadImageRequest $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function uploadImage(Card $card, UploadImageRequest $request)
    {
        try{
            DB::beginTransaction();

            if ($request->hasFile("image"))
            {
                $imageName = time() . '.' . $request->file('image')->extension();

                $path = $request->file('image')->storeAs(
                    'images/' . auth()->user()->id, $imageName,
                    'public'
                );

                $storeId = ImageStore::create([
                    'name' => $request->input('name'),
                    'path' => $path
                ]);

                $card->update([
                    'image_store_id' => $storeId->id
                ]);
            }
            DB::commit();

            return $this->sendResponse(new CardResource($card, 1), 'Image updated successfully.', Response::HTTP_ACCEPTED);

        } catch (\Throwable $exception)
        {
            DB::rollBack();
            Log::error($exception);

            return $this->sendError('Failed to upload image.');
        }
    }

}
