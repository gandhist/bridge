<?php 


namespace App\Helpers;


class ResponseFormatter
{
    protected static $response = 
    [
        'meta' => [
            'code' => 200,
            'status' => 'success',
            'message' => null
        ],
        'data' => null
    ];

    // response success
    public static function success($data=null, $message = null, $code = 200){
        self::$response['data'] = $data;
        self::$response['meta']['message'] = $message;
        self::$response['meta']['code'] = $code;
        return response()->json(self::$response, self::$response['meta']['code']);
    }

    // response error
    public static function error($data=null, $message = null, $code = 400){
        self::$response['data'] = $data;
        self::$response['meta']['status'] = 'error';
        self::$response['meta']['code'] = $code;
        self::$response['meta']['message'] = $message;
        return response()->json(self::$response, self::$response['meta']['code']);
    }
}