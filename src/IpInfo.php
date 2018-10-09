<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2018
 * @package   yii2-ipinfo
 * @version   1.0.2
 */

namespace kartik\ipinfo;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\base\Widget;
use kartik\icons\Icon;
use kartik\popover\PopoverX;
use yii\base\InvalidConfigException;

/**
 * IP Info widget for Yii2 with ability to display country flag and
 * geo position info. Uses the API from freegeoip.net to parse IP info.
 *
 * @see http://freegeoip.net
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class IpInfo extends Widget
{
    /**
     * @var string the api to fetch IP information
     */
    public $api = 'http://api.ipstack.com/';

    /**
     * @var string the ip address
     */
    public $ip;

     /**
     * @var string api access key. If not set this will default from `Yii::$app->params['ipInfoAccessKey']`.
     */
    public $access_key;
    
    /**
     * @var bool whether access key is required. You can set this to `false` if using your own [[api]] endpoint.
     */
    public $needsAccessKey = true;
    
    /**
     * @var array the template configuration for rendering the popover button, popover content, or inline content. This
     *     should be set as `$key => $value` pairs, where `$key` is one of:
     *     - `popoverButton`: this is the template for popover button label (applied when `showPopover` is `true`)
     *     - `popoverContent`: this is the template for popover content displayed on click of the button (applied when
     *     `showPopover` is `true`)
     *     - `contentInline`: this is the template for inline content when `showPopover` is set to `false`
     *     The `$value` is the template setting and can contain tags in braces, which will represent value of each IP
     *     position field (set in `fields` property) fetched from the freegeoip.net API (for example `{country_code}`,
     *     `{country_name}` etc.).  The following additional special tags will be replaced:
     *     - '{flag}': Will be replaced with the flag icon rendered based on the `showFlag` setting.
     *     - '{table}': Will render all fields configured via `fields` in a tabular format of labels and values.
     */
    public $template = [];

    /**
     * @var string the template for rendering the content inline when `showPopover` is `false`. The tags in braces
     *     represent value of each IP position field (set in `fields` property) fetched from the freegeoip.net API (for
     *     example `{country_code}`, `{country_name}` etc.).  The following additional special tags will be replaced:
     *     - '{flag}': will be replaced with the flag icon rendered based on the `showFlag` setting.
     *     - '{table}':will render all fields configured via `fields` in a tabular format of labels and values.
     */
    public $templateContent = '{table}';

    /**
     * @var bool whether to show flag
     */
    public $showFlag = true;

    /**
     * @var bool whether to display position coordinates
     */
    public $showPosition = true;

    /**
     * @var bool whether to show details in a popover on click of flag.
     * If set to false, the results will be rendered inline.
     */
    public $showPopover = true;

    /**
     * @var array the HTML attributes for the loading container. The following special tags are recognized:
     *      - `tag`: string, the `tag` in which the content will be rendered. Defaults to `div`.
     *      - `message`: string, the loading message to be shown. Defaults to `Fetching location info...`.
     */
    public $loadingOptions = ['class' => 'kv-ip-loading'];

    /**
     * @var array the default initial values for the field tags used in the `template` property before they are
     *     fetched from the API. Defaults to:
     * ```
     * $defaultFieldValues = [
     *      'flag' => '<i class="glyphicon glyphicon-question-sign text-warning"></i>',
     *      'country_code' => 'N.A.'
     *      'country_name' => 'Unknown'
     *  ];
     *
     * ```
     */
    public $defaultFieldValues = [];

    /**
     * @var array the message to be shown when no data is found. Defaults to: `No data found for IP address {ip}`.
     */
    public $noData;

    /**
     * @var array the HTML attributes for the no data container. The following special tags are recognized:
     *      - `tag`: string, the `tag` in which the content will be rendered. Defaults to `div`.
     */
    public $noDataOptions = ['class' => 'alert alert-danger text-center'];

    /**
     * @var array the markup to be displayed when any exception is faced during processing by the API (e.g. no
     *     connectivity). You can set this to a blank string to not display anything. Defaults to:
     *      `<i class="glyphicon glyphicon-exclamation-sign text-danger"></i>`.
     */
    public $errorData = '<i class="glyphicon glyphicon-exclamation-sign text-danger"></i>';

    /**
     * @var array the HTML attributes for error data container. Defaults to: `['title' => 'IP fetch error']`. The
     *     following special tags are recognized:
     *     - `tag`: string, the `tag` in which the content will be rendered. Defaults to `div`.
     */
    public $errorDataOptions = ['class' => 'img-thumbnail btn-default', 'style' => 'padding:0 6px'];

    /**
     * @var array the list of column fields to be display as details. Each item in this array must correspond to the
     *     field `key` for each record in the JSON output. Note that the fields will be displayed in the same order as
     *     you set it here. If not set, the translated names are autogenerated (see [[_defaultFields]]).
     */
    public $fields = [];

    /**
     * @var array the widget configuration settings for `kartik\popover\PopoverX` widget that will show the details on
     *     hover.
     */
    public $popoverOptions = [];

    /**
     * @var array the HTML attributes for the flag wrapper container.
     */
    public $flagWrapperOptions = [];

    /**
     * @var array the HTML attributes for the flag image (rendered via `flag-icon-css` in `kartik-v/yii2-icons`).
     */
    public $flagOptions = [];

    /**
     * @var array the header title for content shown in the popover. Defaults to `IP Position Details`
     */
    public $contentHeader;

    /**
     * @var array the icon shown before the header title for content in the popover.
     */
    public $contentHeaderIcon = '<i class="glyphicon glyphicon-map-marker"></i> ';

    /**
     * @var array the HTML attributes for the ip info content table container.
     */
    public $contentOptions = ['class' => 'table'];

    /**
     * @var array the HTML attributes for the widget container. The following special tags are recognized:
     * - `tag`: string, the `tag` in which the content will be rendered. Defaults to `div`.
     */
    public $options = [];
    
    /**
     * @var bool whether to cache ip information in local client storage for the session. 
     * This will optimize and reduce server api calls for ip addresses already parsed in 
     * the past.
     */
    public $cache = true;

    /**
     * @var int the cache timeout in milli seconds when the client cache will expire.
     */
    public $cacheTimeout = 30000;

    /**
     * @var array the default field keys and labels setting (@see `initOptions` method)
     */
    protected $_defaultFields = [];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->initOptions();
        $this->renderWidget();
    }

    /**
     * Initialize widget options
     */
    protected function initOptions()
    {
        $this->_msgCat = 'kvip';
        $this->initI18N();
        $this->_defaultFields = [
            'ip' => Yii::t('kvip', 'IP Address'),
            'country_code' => Yii::t('kvip', 'Country Code'),
            'country_name' => Yii::t('kvip', 'Country Name'),
            'region_code' => Yii::t('kvip', 'Region Code'),
            'region_name' => Yii::t('kvip', 'Region Name'),
            'city' => Yii::t('kvip', 'City'),
            'zip_code' => Yii::t('kvip', 'Zip Code'),
            'time_zone' => Yii::t('kvip', 'Time Zone'),
            'latitude' => Yii::t('kvip', 'Latitude'),
            'longitude' => Yii::t('kvip', 'Longitude'),
            'metro_code' => Yii::t('kvip', 'Metro Code'),
        ];
        $this->template += [
            'popoverButton' => '{flag} {country_code}',
            'popoverContent' => '{table}',
            'inlineContent' => '{flag} {table}' // when showPopover is false
        ];
        if (!isset($this->errorDataOptions['title'])) {
            $this->errorDataOptions['title'] = Yii::t('kvip', 'IP fetch error');
        }
        if ($this->needsAccessKey && !isset($this->access_key)) {
            if (!isset(Yii::$app->params['ipInfoAccessKey'])) {
                throw new InvalidConfigException('The API Access Key must be set within `IpInfo::access_key` OR `Yii:$app->params["ipInfoAccessKey"]`.');
            }
            $this->access_key = Yii::$app->params['ipInfoAccessKey'];
        }
        if (!isset($this->ip)) {
            $this->ip = Yii::$app->request->getUserIP();
        }
        if (isset($this->pjaxContainerId) && !isset($this->popoverOptions['pjaxContainerId'])) {
            $this->popoverOptions['pjaxContainerId'] = $this->pjaxContainerId;
        }
    }

    /**
     * Parses template tags for replace
     *
     * @param string $template
     * @param string $flag
     * @param string $tag
     * @param string $value
     * @param string $type
     *
     * @return string
     */
    protected function parseTag($template, $tag, $value, $flag, $type = 'p')
    {
        if ($tag === 'table') {
            Html::addCssClass($this->contentOptions, $this->options['id'] . '-table-' . $type);
            $field = Html::tag('table', '', $this->contentOptions);
        } elseif ($tag === 'flag') {
            $field = $flag;
        } else {
            $field = Html::tag('span', $value, ['id' => $this->options['id'] . '-' . $tag . '-' . $type]);
        }
        return str_replace('{' . $tag . '}', $field, $template);
    }

    /**
     * Renders the widget
     */
    protected function renderWidget()
    {
        $this->api .= $this->ip;
        if (empty($this->flagWrapperOptions['id'])) {
            $this->flagWrapperOptions['id'] = $this->options['id'] . '-flag';
        }
        $loadData = ArrayHelper::remove($this->loadingOptions, 'message', Yii::t('kvip', 'Fetching location info...'));
        $this->defaultFieldValues += [
            'flag' => '<i class="glyphicon glyphicon-question-sign text-warning"></i>',
            'table' => '',
            'country_code' => Yii::t('kvip', 'N.A.'),
            'country_name' => Yii::t('kvip', 'Unknown'),
            'ip' => '',
            'region_code' => '',
            'region_name' => '',
            'city' => '',
            'zip_code' => '',
            'time_zone' => '',
            'latitude' => '',
            'longitude' => '',
            'metro_code' => ''
        ];
        $popoverButton = $popoverContent = $inlineContent = $flag = '';
        extract($this->template);
        if ($this->showFlag) {
            Icon::map($this->getView(), Icon::FI);
            if (empty($this->flagOptions['class'])) {
                $this->flagOptions['class'] = 'flag-icon';
            }
            $flag = Html::tag('span', $this->defaultFieldValues['flag'], $this->flagWrapperOptions);
        }
        foreach ($this->defaultFieldValues as $tag => $value) {
            if ($this->showPopover) {
                $popoverButton = $this->parseTag($popoverButton, $tag, $value, $flag, 'p');
                $popoverContent = $this->parseTag($popoverContent, $tag, $value, $flag, 'i');
            } else {
                $inlineContent = $this->parseTag($inlineContent, $tag, $value, $flag, 'i');
            }
        }
        $content = self::renderTag($loadData, $this->loadingOptions, 'div');
        if ($this->showPopover) {
            $header = isset($this->contentHeader) ? $this->contentHeader : Yii::t('kvip', 'IP Position Details');
            $this->popoverOptions['header'] = $this->contentHeaderIcon . $header;
            $popOpts = $this->popoverOptions;
            if (!isset($popOpts['toggleButton']) && !isset($popOpts['toggleButton']['class'])) {
                $this->popoverOptions['toggleButton']['class'] = 'kv-ipinfo-button';
            }
            $this->popoverOptions['toggleButton']['label'] = $popoverButton;
            $this->popoverOptions['content'] = self::renderTag(
                $content . '<div class="kv-hide">' . $popoverContent . '</div>',
                $this->options
            );
            $content = PopoverX::widget($this->popoverOptions);
        } else {
            $content = self::renderTag(
                $content . '<div class="kv-hide">' . $inlineContent . '</div>',
                $this->options
            );
        }
        $this->registerAssets();
        echo $content;
    }

    /**
     * Register plugin assets. Uses `kvIpInfo` jQuery plugin created by Krajee to refresh the IP information.
     */
    protected function registerAssets()
    {
        if (empty($this->noData)) {
            $noData = empty($this->ip) ? Yii::t('kvip', "No data found for the user's IP address.") :
                Yii::t('kvip', 'No data found for IP address {ip}.', ['ip' => '<kbd>' . $this->ip . '</kbd>']);
        } else {
            $noData = $this->noData;
        }
        $api = $this->api;
        if ($this->needsAccessKey) {
            $sep = strpos($api, '?') === false ? '?' : '&';
            $api .= "{$sep}access_key={$this->access_key}";
        }
        $this->pluginOptions = [
            'flagWrapper' => $this->showFlag ? $this->flagWrapperOptions['id'] : false,
            'flagOptions' => $this->flagOptions,
            'fields' => empty($this->fields) ? array_keys($this->_defaultFields) : $this->fields,
            'defaultFields' => $this->_defaultFields,
            'url' => $api,
            'noData' => self::renderTag($noData, $this->noDataOptions, 'div'),
            'errorData' => empty($this->errorData) ? '' : self::renderTag($this->errorData, $this->errorDataOptions),
            'cache' => $this->cache,
            'cacheTimeout' => $this->cacheTimeout
        ];
        $this->registerPlugin('kvIpInfo');
        IpInfoAsset::register($this->getView());
    }

    /**
     * Renders a tag based on content and options, in which the tag is set within options.
     *
     * @param string $content the content to render
     * @param array  $options the HTML attributes for the content container
     * @param string $tag the default HTML tag to use if `$options['tag']` is not set.
     *
     * @return string
     */
    protected static function renderTag($content, &$options = [], $tag = 'div')
    {
        $tag = ArrayHelper::remove($options, 'tag', $tag);
        return Html::tag($tag, $content, $options);
    }
}
