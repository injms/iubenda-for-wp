Iubenda for WordPress
=====================

[iubenda](https://www.iubenda.com/) is a easy to use privacy and cookie policy service. This is a plugin which lets you use their non-JavaScript API to embed your privacy policy into your WordPress website.

**Important** - you will need to use iubenda's [paid Pro service](https://www.iubenda.com/en/pricing) to use this plugin; otherwise this plugin will default to the JavaScript embedding method.

The plugin requests the policy using Iubenda's API, then caches the policy for 24 hours to make things speedier. The policy is then displayed whereever the tag is placed in the theme.

How To use
------
Simply add the following within your theme or where you want the policy to appear:

```php
[injms_iubenda policy_id="123456" theme="white" text_only=0 cache=86400]
```

```php
<?php

echo injms_iubenda([
  'policy_id' => '123456',
  'type'      => 'privacy-policy', // privacy-policy | cookie-policy | terms-and-conditions.
  'theme'     => 'white', // black | white | nostyle.
  'text_only' => true,
  'iframe'    => false,
  'cache'     => 86400, // 24 hours in seconds ( 60 x 60 x 24 ).
]);
```

Disclaimer
----------
And, of course, this plugin is in no way affiliated or endorsed by Iubenda, WordPress, or Automattic.
