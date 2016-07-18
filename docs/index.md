Encrypted Cookie Bundle Documentation
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
Add the project to composer (`composer require lexiphanic/encrypted-cookie-bundle`) then add `Lexiphanic\EncryptedCookieBundle\EncrypedCookieBundle()` to your `AppKernel`.

Configuration
---
``` yaml
lexiphanic_encrypted_cookie:
    cookies:
        - session
        - secret
    encryption:
        service: service id
```
`cookies` is an array of cookies to encrypt/decrypt. `encryption.service` is a service id to a service that implements the `Lexiphanic\EncryptedCookieBundle\Encryption\EncryptionInterface`.

Usage with GuardAuthenticator
---
On the successful login event, a cookie can be set with your identifiers/options (such as user ID, IP address and expiry).
A GuardAuthenticator can then be created to read the cookie and validate it against the current request. (Ensure you add the cookie name to the config so that it gets encrypted!)

Notes/Future Improvements
---
**Notes**
 - Consider having a `key to value` store where the `key` is crypto-secure and used as the cookie value and the `value` the data that otherwise would be in the cookie. This way the data never reaches users' machines.

**Future Improvements**
 - Add specifications for error handling (what if encrypter fails, the user may be blocked as the cookie will persist)
 - Add a base64 encryption service for use in development (which should never be used in production).
 - Add a mcrypt encryption service for quicker usage/deployments
 - Add a chain encryption service (this can then be used for encryption then signing of cookies as mentioned in the warning)