Change Log: `yii2-ipinfo`
=========================

## Version 2.0.0

**Date**: 01-Oct-2019

### BC Breaking Updates (v2.0)

- Replace to use Free API from http://ip-api.com (free for non commercial use)
- Eliminates use of API access key
- One can however reconfigure the widget to use any other IP API sources and parameters
- Revamp translations
- Validate automatically Bootstrap 3.x or Bootstrap 4.x format using Krajee Bootstrap settings and render the relevant icons
- Implement **Yii2 HTTP Client** to process IP API info fetch. The 1.0 AJAX based API query will be eliminated and replaced with the Yii2 server based HTTP Client. This will ensure it works both for HTTPS and HTTP and also use yii2 caching.
- Implement **Yii2 Cache** component to cache/store IP data. If Cache component is not defined no caching will be performed.
- Implement **Yii2 DetailView** widget to render the IP Information Details table. The layout of attributes and formats can therefore be customized as per user need
- Implements `IpInfoModel` to manage the IP information attributes. In case you wish to use a different IP Info API - then you can extend this class to have your own attributes.
- Various properties have been eliminated and replaced. The new version now includes these properties:
    - `api`
    - `ip`
    - `modelClass`
    - `detailViewClass`
    - `detailViewConfig`
    - `detailRowOptions`
    - `params`
    - `requestConfig`
    - `cache`
    - `flushCache`
    - `template`
    - `showFlag`
    - `hideEmpty`
    - `showPopover`
    - `errorData`
    - `errorDataOptions`
    - `fields`
    - `skipFields`
    - `popoverOptions`
    - `flagWrapperOptions`
    - `flagOptions`
    - `contentHeader`
    - `errorIcon`
    - `contentHeaderIcon`
    - `noFlagIcon`
    - `errorIcon`
    - `options`

## Version 1.0.2

**Date**: 09-Oct-2019

- Update composer dependencies

## Version 1.0.1

**Date**: 27-Jul-2019

- (enh #14): Implement client level caching for optimal performance.
- (enh #13): Implement ability to skip access key if NOT needed.

## Version 1.0.0

**Date**: 18-Jul-2019

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