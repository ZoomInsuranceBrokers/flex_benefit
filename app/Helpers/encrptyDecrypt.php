<?php
if (! function_exists('decryptAES')) {
    function decryptAES($encryptedData) {
        if(strlen($encryptedData['data'])) {
            $data = $encryptedData['data'];
            $encryptionKey = env('AES_ENCRYPTION_KEY');
            $initializationVector = env('AES_INITIALIZATION_VECTOR');

            // Decrypt the data
            $cipher = env('AES_CIPHER');
            $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

            $decrptedData = openssl_decrypt(
                        base64_decode($data),
                        $cipher,
                        base64_decode($encryptionKey),
                        $options,
                        base64_decode($initializationVector)
                    );

            if ($decrptedData === false) {
                echo "Error during decryption: " . openssl_error_string() . PHP_EOL;
            } else {
                return floatval(rtrim($decrptedData, "\0"));
            }
        } else {
            die('Invalid Encrypted Data, no further processsing possible. Error Type: ' . $encryptedData['type'] . ';');
        }
    }
}

?>