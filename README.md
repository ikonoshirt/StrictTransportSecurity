# Strict Transport Security
STS is a [HTTP header which can be set](http://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security).

In short: use always SSL, if no SSL is available abort the connection.

## Changes for magento

* Check wether both secure and unsecure url are https.
* Add a Strict Transport Security Header to magento.

## Goal of this extention

You know what [sidejacking](http://en.wikipedia.org/wiki/Sidejacking#Methods) [and](http://en.wikipedia.org/wiki/Man-in-the-middle_attack) [SSLStrip](http://www.securitytube.net/video/193) [is](http://www.symantec.com/theme.jsp?themeid=always-on-ssl)? This should help a bit against it.