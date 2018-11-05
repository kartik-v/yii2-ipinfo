<h1 align="center">
    <a href="http://demos.krajee.com" title="Krajee Demos" target="_blank">
        <img src="http://kartik-v.github.io/bootstrap-fileinput-samples/samples/krajee-logo-b.png" alt="Krajee Logo"/>
    </a>
    <br>
    yii2-ipinfo
    <hr>
    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DTP3NZQ6G2AYU"
       title="Donate via Paypal" target="_blank">
        <img src="http://kartik-v.github.io/bootstrap-fileinput-samples/samples/donate.png" alt="Donate"/>
    </a>
</h1>

[![Stable Version](https://poser.pugx.org/kartik-v/yii2-widget-ipinfo/v/stable)](https://packagist.org/packages/kartik-v/yii2-widget-ipinfo)
[![Unstable Version](https://poser.pugx.org/kartik-v/yii2-widget-ipinfo/v/unstable)](https://packagist.org/packages/kartik-v/yii2-widget-ipinfo)
[![License](https://poser.pugx.org/kartik-v/yii2-ipinfo/license)](https://packagist.org/packages/kartik-v/yii2-ipinfo)
[![Total Downloads](https://poser.pugx.org/kartik-v/yii2-ipinfo/downloads)](https://packagist.org/packages/kartik-v/yii2-ipinfo)
[![Monthly Downloads](https://poser.pugx.org/kartik-v/yii2-ipinfo/d/monthly)](https://packagist.org/packages/kartik-v/yii2-ipinfo)
[![Daily Downloads](https://poser.pugx.org/kartik-v/yii2-ipinfo/d/daily)](https://packagist.org/packages/kartik-v/yii2-ipinfo)

An IP address information display widget for Yii framework 2.0 with ability to display country flag and geo position info. This is based on the [HTTP API from ipstack.com (earlier freegeoip.net)](https://ipstack.com/) to parse IP address details. The plugin also uses the [yii2-popover-x](http://demos.krajee.com/popover-x) extension by Krajee for displaying details of the IP in a popover, and the <a href="http://demos.krajee.com/icons">yii2-icons</a> extension by Krajee for displaying the flag icons. 

## Features  

- Ability to display the flag for a IP address.
- Ability to display geo position details for the IP address.
- Ability to use your own API if needed
- Implements a client parsing plugin and client level local storage caching for optimized performance.
- Ability to render IP details inline instead of popover.
- Ability to configure fields rendered and also control the layout with templates.
- Use `yii2-popover-x` extension features to control popover placements and styles.
- Use flag icons from `yii2-icons` to render country wise high resolution flags of any size.
- Uses Yii i18N translations to generate locale specific data.


> Note: Check the [composer.json](https://github.com/kartik-v/yii2-ipinfo/blob/master/composer.json) for this extension's requirements and dependencies. 
Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

## Demo
You can see detailed [documentation and examples](http://demos.krajee.com/ipinfo) on usage of the extension.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

> Note: Check the [composer.json](https://github.com/kartik-v/yii2-ipinfo/blob/master/composer.json) for this extension's requirements and dependencies. 
Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

Either run

```
$ php composer.phar require kartik-v/yii2-ipinfo "@dev"
```

or add

```
"kartik-v/yii2-ipinfo": "@dev"
```

to the ```require``` section of your `composer.json` file.

## Usage

### IpInfo

```php
use kartik\ipinfo\IpInfo;

echo IpInfo::widget([
    'ip' => '12.23.155.123',
    'access_key'=>'ACCESS_KEY'
    /**
     * optionally setup more options
     * refer docs for all options
     */
    // 'showFlag' => true,
    // 'showPopover' => true,
    // 'popoverOptions' => [],
    // 'flagWrapperOptions' => []
    // 'flagOptions' => []
]);
```

## License

**yii2-ipinfo** is released under the BSD-3-Clause License. See the bundled `LICENSE.md` for details.