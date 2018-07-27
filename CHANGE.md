Change Log: `yii2-ipinfo`
=========================

## Version 1.0.1

**Date**: 27-Jul-2018

- (enh #14): Implement client level caching for optimal performance.
- (enh #13): Implement ability to skip access key if NOT needed.

## Version 1.0.0

**Date**: 18-Jul-2018

- Default current ip address if `IpInfo::ip` is not set.
- Add github contribution and change log templates.
- (enh #12): Revamp widget to use updated api from `api.ipstack.com`.
    - new property `IpInfo::access_key` added
    - new configuration option `Yii::$app->params['ipInfoAccessKey']` that can be used to globally default the API access key
- (enh #11): Add Ukranian translations.
- (enh #10): Update `freegeoip.net` url.
- (enh #9): Add Spanish translations.
- (enh #7): Add Dutch translations.
- (enh #6): New `template` property for controlling layout and fields rendered.
- (enh #5): Use scalable flag icons from `kartik-v/yii2-icons`.
- (enh #4): Revamp widget to use `freegeoip.net` API.
- (enh #3): Enhance extension to use AJAX based jQuery plugin to refresh IP info.
- (enh #1): Added Portugese translations.