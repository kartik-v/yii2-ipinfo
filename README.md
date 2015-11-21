yii2-ipinfo
===========

[![Latest Stable Version](https://poser.pugx.org/kartik-v/yii2-ipinfo/v/stable)](https://packagist.org/packages/kartik-v/yii2-ipinfo)
[![License](https://poser.pugx.org/kartik-v/yii2-ipinfo/license)](https://packagist.org/packages/kartik-v/yii2-ipinfo)
[![Total Downloads](https://poser.pugx.org/kartik-v/yii2-ipinfo/downloads)](https://packagist.org/packages/kartik-v/yii2-ipinfo)
[![Monthly Downloads](https://poser.pugx.org/kartik-v/yii2-ipinfo/d/monthly)](https://packagist.org/packages/kartik-v/yii2-ipinfo)
[![Daily Downloads](https://poser.pugx.org/kartik-v/yii2-ipinfo/d/daily)](https://packagist.org/packages/kartik-v/yii2-ipinfo)

An IP address information display widget for Yii framework 2.0 with ability to display country flag and geo position info. This is based on the [PHP API from hostip.info](http://www.hostip.info/use.html) to parse IP address details. The plugin also uses the [yii2-popover-x](http://demos.krajee.com/popover-x) extension by Krajee for displaying details of the IP in a popover. 

## Features  

- Ability to display the flag for a IP address.
- Ability to display geo position details for the IP address.
- Ability to render IP details inline instead of popover.
- Ability to return a raw json that return details for the IP address.
- Use `yii2-popover-x` extension features to control popover placements and styles.
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
    /**
     * optionally setup more options
     * refer docs for all options
     */
    // 'showFlag' => true,
    // 'showPosition' => true,
    // 'showPopover' => true,
    // 'showCredits' => true,
    // 'popoverOptions' => [],
    // 'flagOptions' => []
]);
```

## License

**yii2-ipinfo** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.