EncryptCookie Documentation
===

Warning
---
It's important to note that any sensitive information should **NEVER** be stored client side (cookies, local storage, WebSql, etc.)
but server side instead (session, database, cache, etc.).
Depending on usage, Encryption alone may not be sufficient and may need extra protection, such as signing. 

How does it work?
---
Two event listeners are added, one to the request and one for the response. The response listener encrypts the requested cookies and the request event decrypts the cookies. This way your application need not be changed.

Installation
---
Add the project to composer (`composer require lexiphanic/encrypted-cookie-bundle`) then add `Lexiphanic\EncrypedCookieBundle\EncrypedCookieBundle()` to your `AppKernel`.

Configuration
---
``` yaml
Lexiphanic_encrypted_cookie:
    cookies:
        - session
        - secret
    encryption:
        Service: service id
```
`cookies` is an array of cookies to encrypt/decrypt. `encryption.service` is a service id to a service that implements the `Lexiphanic\EncryptedCookieBundle\Encryption\EncryptionInterface`.

Usage with GuardAuthenticator
---
On the successful login event, a cookie can be set with your identifiers/options (such as user ID, IP address and expiry).
A GuardAuthenticator can be created to read the cookie and validate it against the current request.