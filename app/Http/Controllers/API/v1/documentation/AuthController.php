<?php
/**
 * @OA\Post(
 *      path="/api/v1/login",
 *      operationId ="user_login",
 *      tags={"Authentication"},
 *      summary = "Login and return access token",
 *     security={},
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="email",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 *                 example={"email": "jane.doe@email.com", "password": "Password@123"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid Data",
 *         @OA\JsonContent()
 *     ),
 * )
 */


/**
 * @OA\Post(
 *      path="/api/v1/register",
 *      operationId ="user_register",
 *      tags={"Authentication"},
 *      summary = "Register and return access token",
 *      security={},
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="name",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="email",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password_confirmation",
 *                     type="string"
 *                 ),
 *                 example={
 *                      "name" : "Jane Doe",
 *                      "email": "jane.doe@email.com",
 *                      "password": "Password@123",
 *                      "password_confirmation": "Password@123"
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid Data",
 *         @OA\JsonContent()
 *     ),
 * )
 */

/**
 * @OA\Post(
 *      path="/api/v1/forgot-password",
 *      operationId ="forgot_password",
 *      tags={"Authentication"},
 *      summary = "Sends email to change password",
 *     security={},
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="email",
 *                     type="string"
 *                 ),
 *                 example={
 *                      "email": "jane.doe@email.com",
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid Data",
 *         @OA\JsonContent()
 *     ),
 * )
 */

/**
 * @OA\Post(
 *      path="/api/v1/reset-password",
 *      operationId ="rest_password",
 *      tags={"Authentication"},
 *      summary = "Reset user password",
 *      security={},
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="token",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="password_confirmation",
 *                     type="string"
 *                 ),
 *                 example={
 *                      "token": "xixoicuvlksd",
 *                      "password": "Password@123",
 *                      "password_confirmation": "Password@123"
 *                 }
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="successful operation",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid Data",
 *         @OA\JsonContent()
 *     ),
 * )
 */

/**
 * @OA\Post(
 *      path="/api/v1/logout",
 *      operationId ="user_logout",
 *      tags={"Authentication"},
 *      summary = "Revokes the access token of logged user",
 *      security={{"bearerAuth":{}}},
 *      @OA\Response(
 *          response="200",
 *          description="Everything is fine",
 *          @OA\JsonContent()
 *      )
 * )
 */


/**
 * @OA\Get(
 *      path="/api/v1/user",
 *      operationId ="auth_user",
 *      tags={"Authentication"},
 *      summary = "Returns the logged in user",
 *      security={{"bearerAuth":{}}},
 *      @OA\Response(
 *          response="200",
 *          description="Everything is fine",
 *          @OA\JsonContent()
 *      ),
 * )
 */
