<?php

namespace Lexiphanic\EncryptedCookieBundle\Encryption;

class Base64 implements EncryptionInterface {
    public function encrypt ($data) {
        return base64_encode($data);
    }
    
    public function decrypt ($data) {
        return base64_decode($data);
    }
}