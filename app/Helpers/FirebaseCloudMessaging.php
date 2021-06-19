<?php

namespace App\Helpers;

/**
 * Format response.
 */
class FirebaseCloudMessaging
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'meta' => [
            'code' => 200,
            'status' => 'success',
            'message' => null,
        ],
        'data' => null,
    ];

    public static function pushNotification($data, $topic)
    {
        $ch = curl_init();

        $data['click_action'] = 'FLUTTER_NOTIFICATION_CLICK';
        $message = array(
            'to' => '/topics/' . $topic,
            'data' => $data,
            'notification' => $data
        );

        curl_setopt_array($ch, array(
            CURLOPT_URL => env('FCM_REQUEST_URL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_HEADER => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('FCM_SERVER_KEY'),
            ),
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $message,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ));

        curl_exec($ch);

        if ($code = curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            throw new \Exception('oops terjadi kesalahan, ' . $code);
        }

        curl_close($ch);
    }
}
