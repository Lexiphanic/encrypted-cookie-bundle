<?php

namespace Lexiphanic\EncryptedCookieBundle\Encryption;

interface EncryptionInterface {
    public function encrypt ();
    public function decrypt ();
}