<?php
/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     type="http",
 *     description="Oauth2 security",
 *     name="oauth2",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *   @OA\Flow(
 *      authorizationUrl="/api/login",
 *      tokenUrl= "/api/login",
 *      flow="password",
 *      scopes={
 *      }
 *   )
 * )
 */
