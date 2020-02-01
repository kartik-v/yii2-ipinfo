<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2020
 * @package   yii2-ipinfo
 * @version   2.0.3
 */

namespace kartik\ipinfo;

use yii\base\Model;
use yii\helpers\Html;
use Yii;

/**
 * The model used for IP Information and used by the IP Info widget.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class IpInfoModel extends Model
{
    public $ip;
    public $continentCode;
    public $continent;
    public $countryCode;
    public $country;
    public $region;
    public $regionName;
    public $city;
    public $district;
    public $zip;
    public $lat;
    public $lon;
    public $timezone;
    public $currency;
    public $isp;
    public $org;
    public $as;
    public $asname;
    public $reverse;
    public $mobile;
    public $proxy;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip', 'continentCode', 'continent', 'countryCode', 'country', 'region', 'regionName', 'city'], 'safe'],
            [['district', 'zip', 'lat', 'lon', 'timezone', 'currency', 'isp', 'org', 'as', 'asname'], 'safe'],
            [['reverse', 'mobile', 'proxy'], 'safe'],
        ];
    }

    /**
     * Gets continent detail
     * @return string
     */
    public function getContinentDetail()
    {
        return empty($this->continentCode) ? $this->continent :
            ($this->continentCode . (empty($this->continent) ? '' :  ' - ' . $this->continent));
    }

    /**
     * Gets region detail
     * @return string
     */
    public function getRegionDetail()
    {
        return empty($this->region) ? $this->regionName :
            ($this->region . (empty($this->regionName) ? '' :  ' - ' . $this->regionName));
    }

    /**
     * Gets country detail
     * @param string $default
     * @return string
     */
    public function getCountryDetail($default = '')
    {
        return $this->getFlag($default) . ' ' . $this->countryCode . ' - ' . $this->country;
    }

    /**
     * @param string $default the default flag
     * @return string
     */
    public function getFlag($default = '')
    {
        if (empty($this->countryCode)) {
            return $default;
        }
        $country = strtolower($this->countryCode);
        return Html::tag('span', '', ['class' => "flag-icon flag-icon-{$country}"]);
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return static::getCoordinate($this->lat);
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return static::getCoordinate($this->lon);
    }

    /**
     * Return location lat / lon coordinate as degree - minutes - seconds format
     * @param float|string $value
     * @return string
     */
    protected static function getCoordinate($value)
    {
        $vars = explode(".", $value);
        if (count($vars) <= 1) {
            return $value . '°';
        }
        $deg = (int) $vars[0];
        $fract = (float) ('0.' . $vars[1]);

        $seconds = $fract * 3600;
        $min = floor($seconds / 60);
        $sec = $seconds - ($min * 60);

        $deg = $deg > 0 ? $deg . '° ' : '';
        $min = $min > 0 ? $min . "' " : '';
        $sec = $sec > 0 ? round($sec) . "'" . "'" : '';

        return $deg . $min . $sec;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ip' => Yii::t('kvip', 'IP Address'),
            'flag' => Yii::t('kvip', 'Flag'),
            'continentCode' => Yii::t('kvip', 'Continent Code'),
            'continent' => Yii::t('kvip', 'Continent'),
            'continentDetail' => Yii::t('kvip', 'Continent'),
            'countryCode' => Yii::t('kvip', 'Country Code'),
            'country' => Yii::t('kvip', 'Country'),
            'countryDetail' => Yii::t('kvip', 'Country'),
            'region' => Yii::t('kvip', 'Region Code'),
            'regionName' => Yii::t('kvip', 'Region'),
            'regionDetail' => Yii::t('kvip', 'Region'),
            'city' => Yii::t('kvip', 'City'),
            'district' => Yii::t('kvip', 'District'),
            'zip' => Yii::t('kvip', 'Zip'),
            'lat' => Yii::t('kvip', 'Latitude'),
            'latitude' => Yii::t('kvip', 'Latitude'),
            'lon' => Yii::t('kvip', 'Longitude'),
            'longitude' => Yii::t('kvip', 'Longitude'),
            'timezone' => Yii::t('kvip', 'Time Zone'),
            'currency' => Yii::t('kvip', 'Currency'),
            'isp' => Yii::t('kvip', 'ISP'),
            'org' => Yii::t('kvip', 'Organization'),
            'as' => Yii::t('kvip', 'AS'),
            'asname' => Yii::t('kvip', 'AS'),
            'reverse' => Yii::t('kvip', 'Reverse DNS'),
            'mobile' => Yii::t('kvip', 'Mobile Connnection'),
            'proxy' => Yii::t('kvip', 'Proxy'),
        ];
    }

}
