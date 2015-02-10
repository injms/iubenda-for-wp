Iubenda for WordPress
=====================

(A work in progress)

[iubenda](https://www.iubenda.com/) is a easy to use privacy and cookie policy service. This is a plugin which lets you use their non-JavaScript API to embed your privacy policy into your WordPress website.

**Important** - you will need to use iubenda's [paid Pro service](https://www.iubenda.com/en/pricing) to use this plugin; otherwise this plugin will default to the JavaScript embedding method.

The plugin requests the policy using Iubenda's API, then caches the policy for 24 hours to make things speedier. The policy is then displayed whereever the tag is placed in the theme.

If the policy isn't a pro version, then the fallback is Iubenda's JavaScript embed.

To use
------
Simply add the following to where you want the policy to appear:

`<?php echo injms_iubenda( 123456, true, 'black' ); ?>`

- first variable is the policy ID
- the second is true / false for whether a table of contents is generated with some jQuery
- the third is what colour the fallback JavaScript button should be - black,  white, or nostyle. (Note the nostyle needs a pro account.)

To do
-----

* Options in WordPress's admin area to set cache timings, table of contents, and which fallback button style should be used
* Add ability to choose which page the policy appears on, rather than needing to use a tag


Disclaimer
----------
And, of course, this plugin is in no way affiliated or endorsed by Iubenda, WordPress, or Automattic.