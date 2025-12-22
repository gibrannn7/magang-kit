<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_client {

    protected $ci;
    private $api_url = 'http://localhost:3000/send-message'; 
    private $api_key = 'BPS_SECRET_KEY_123456'; 

    public function __construct() {
        $this->ci =& get_instance();
    }

    // Tambahkan parameter $file_url agar bisa mengirim attachment
    public function send_message($number, $message, $file_url = null) {
        $data = [
            'number' => $number,
            'message' => $message,
            'file_url' => $file_url // Kirim URL file ke Node.js
        ];

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->api_key,
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            return json_decode($response, true);
        } else {
            return ['status' => false, 'message' => 'Gagal menghubungi server WA'];
        }
    }
}
