<?php

namespace Lexiphanic\EncryptedCookieBundle\Encryption;

interface EncryptionInterface {
    public function encrypt ($data);
    public function decrypt ($data);
}