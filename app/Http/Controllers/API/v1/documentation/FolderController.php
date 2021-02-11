<?php

/**
 * @OA\Get(
 *      path="/api/v1/folders",
 *      operationId ="get_folders",
 *      tags={"Folder"},
 *      summary = "Return list of folder inside flow or folder",
 *      security={{"bearerAuth":{}}},
 *      @OA\Response(
 *          response="200",
 *          description="Everything is fine",
 *          @OA\JsonContent()
 *      ),
 * )
 */
