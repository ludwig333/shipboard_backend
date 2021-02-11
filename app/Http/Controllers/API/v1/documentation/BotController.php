<?php


/**
 * @OA\Get(
 *     path = "/api/v1/bots",
 *     operationId = "retrieve_bot",
 *     tags = {"Bot"},
 *     summary = "Retrives all the bots belonging to user",
 *     security = {{"bearerAuth":{}}},
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType = "application/json",
 *         ),
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "Successfully retrieved.",
 *         @OA\JsonContent()
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path = "/api/v1/bots/{bot}",
 *     operationId = "retrieve_bot",
 *     tags = {"Bot"},
 *     summary = "Retrives all the bots belonging to user",
 *     security = {{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name = "bot",
 *          in = "path",
 *          required = true,
 *          description="Bot id",
 *          @OA\Schema (
 *              type = "string",
 *          ),
 *     ),
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType = "application/json",
 *         ),
 *     ),
 *     @OA\Response(
 *         response = 200,
 *         description = "Successfull operation.",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *          response = "404",
 *          description = "Not found",
 *          @OA\JsonContent()
 *      ),
 * )
 */

/**
 * @OA\Post(
 *      path = "/api/v1/bots",
 *      operationId = "create_bot",
 *      tags = {"Bot"},
 *      summary = "Create a bot",
 *      security = {{"bearerAuth":{}}},
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType = "application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property = "name",
 *                     type = "string"
 *                 ),
 *                 example={
 *                      "name": "Test Bot",
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response = 201,
 *         description = "Bot created Successfully.",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response = 422,
 *         description = "Validation Error.",
 *         @OA\JsonContent()
 *     ),
 * )
 */

/**
 * @OA\Patch(
 *      path = "/api/v1/bots/{bot}",
 *      operationId = "update_bot",
 *      tags = {"Bot"},
 *      summary = "Update a bot",
 *      security = {{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name = "bot",
 *          in = "path",
 *          required = true,
 *          description="Bot id",
 *          @OA\Schema (
 *              type = "string",
 *          ),
 *     ),
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType = "application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property = "name",
 *                     type = "string"
 *                 ),
 *                 example={
 *                      "name": "Test Bot",
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response = 202,
 *         description = "Successfully updated.",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *          response = "400",
 *          description = "Bad Request",
 *          @OA\JsonContent()
 *      ),
 *     @OA\Response(
 *         response = 422,
 *         description = "Validation error.",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *          response = "404",
 *          description = "Not found",
 *          @OA\JsonContent()
 *      ),
 * )
 */

/**
 * @OA\Delete(
 *      path = "/api/v1/bots/{bot}",
 *      operationId = "delete_bot",
 *      tags = {"Bot"},
 *      summary = "Delete a bot",
 *      security = {{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name = "bot",
 *          in = "path",
 *          required = true,
 *          description="Bot id",
 *          @OA\Schema (
 *              type = "string",
 *          ),
 *     ),
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType = "application/json",
 *         )
 *     ),
 *     @OA\Response(
 *         response = 204,
 *         description = "Successfully deleted.",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *          response = "404",
 *          description = "Not found",
 *          @OA\JsonContent()
 *      ),
 * )
 */
