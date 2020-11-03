<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class BaseAPIController extends Controller
{

    /**
     * Returns the success reponse
     *
     * @param $result
     * @param $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message, $statusCode = Response::HTTP_OK)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $result
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Returns error response
     *
     * @param $error
     * @param array $errorMessages
     * @param int $statusCode
     * @return mixed
     */
    public function sendError($error, $errorMessages = [], $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];

        if(!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $statusCode);
    }
}
