<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2019
 * @package   yii2-ipinfo
 * @version   2.0.1
 */

namespace kartik\ipinfo;

use Yii;
use kartik\base\Widget;
use kartik\icons\Icon;
use kartik\popover\PopoverX;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\Request;
use yii\widgets\DetailView;

/**
 * IP Info widget for Yii2 with ability to display country flag and
 * geo position info. Uses the API from ip-api.com to parse IP info.
 *
 * @see http://ip-api.com
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class IpInfo extends Widget
{
    /**
     * @var string the api to fetch IP information. Note the token `{ip}` will be replaced with the source ip address.
     */
    public $api = 'http://ip-api.com/json/{ip}';

    /**
     * @var string the ip address. If not set will default to current user IP address.
     */
    public $ip;

    /**
     * @var string the model class used for rendering the ip information
     */
    public $modelClass = "\\kartik\\ipinfo\\IpInfoModel";

    /**
     * @var string the model class used for rendering the ip information
     */
    public $detailViewClass = "\\yii\\widgets\\DetailView";

    /**
     * @var array the configuration for the [[DetailView]] widget.
     *
     * @see [[initOptions]] method for the default configuration
     */
    public $detailViewConfig = [];

    /**
     * @var array the HTML attributes for each detail view row
     */
    public $detailRowOptions = [];

    /**
     * @var array any additional query / request parameters to send
     */
    public $params = [];

    /**
     * @var array default HTTP Client request configuration
     */
    public $requestConfig;

    /**
     * @var bool whether to cache ip information in local client storage for the session. If set to `true`, the Yii2 
     * application cache component will be used for caching. If Yii2 caching component is not set or disabled, the
     * caching will be ignored silently without any exceptions. Using caching,  will optimize and reduce server api calls 
     * for ip addresses already parsed in the past.
     */
    public $cache = true;

    /**
     * @var bool whether to flush cache for this IP before fetching. This basically will clear the cached data every
     * time if set to `true`. It is recommended to use and configure this property as a conditional check cleaning 
     * of the cache.
     */
    public $flushCache = false;

    /**
     * @var array the template configuration for rendering the popover button, popover content, or inline content. This
     *     should be set as `$key => $value` pairs, where `$key` is one of:
     *     - `popoverButton`: this is the template for popover button label (applied when `showPopover` is `true`)
     *     - `popoverContent`: this is the template for popover content displayed on click of the button (applied when
     *     `showPopover` is `true`)
     *     - `inlineContent`: this is the template for inline content when `showPopover` is set to `false`
     *     The `$value` is the template setting and can contain tags in braces, which will represent value of each IP
     *     position field (set in [[fields]] property) fetched from the freegeoip.net API (for example `{country_code}`,
     *     `{country_name}` etc.).  The following additional special tags will be replaced:
     *     - '{flag}': Will be replaced with the flag icon rendered based on the `showFlag` setting.
     *     - '{table}': Will render all fields configured via `fields` in a tabular format of labels and values.
     */
    public $template = [];

    /**
     * @var bool whether to show the flag
     */
    public $showFlag = true;

    /**
     * @var bool whether to hide / skip display of fields with empty values
     */
    public $hideEmpty = true;

    /**
     * @var bool whether to show details in a popover on click of flag.
     * If set to false, the results will be rendered inline.
     */
    public $showPopover = true;

    /**
     * @var array the markup to be displayed when any exception is faced during processing by the API (e.g. no
     * connectivity). You can set this to a blank string to not display anything. The following tokens will
     * be replaced:
     * - `{errorIcon}` - with the icon markup set in [[errorIcon]]
     * - `{message}` - any error message returned by api
     */
    public $errorData = '{errorIcon} {noData}';

    /**
     * @var array the HTML attributes for error data container. Defaults to: `['title' => 'IP fetch error']`. The
     *     following special tags are recognized:
     *     - `tag`: string, the `tag` in which the content will be rendered. Defaults to `div`.
     */
    public $errorDataOptions = ['class' => 'kv-ipinfo-error'];

    /**
     * @var array the HTML attributes for container when rendering inline.
     */
    public $inlineContentOptions = [];

    /**
     * @var string the message to be displayed when no valid data is found. Defaults to:
     *
     * 'No data found for IP address {ip}.'
     *
     * This can be referred as {noData} token in any of the templates.
     */
    public $noData;

    /**
     * @var array the list of field names to be shown in display. If this is not set all attributes from [[modelClass]]
     * will be shown (the [[hideEmpty]] setting will control whether to hide attributes that have an empty value)
     */
    public $fields;

    /**
     * @var array the list of column fields to be skipped from display. Note that this setting will override the
     * [[fields]] setting.
     */
    public $skipFields = [];

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
     * @var array the error icon shown for any error in the IP fetch by API. Defaults to:
     *      `<i class="glyphicon glyphicon-exclamation-sign text-danger"></i>` for bootstrap 3.x
     *      `<i class="fas fa-exclamation-circle text-danger"></i>` for bootstrap 4.x
     */
    public $errorIcon;

    /**
     * @var string the icon shown before the header title for content in the popover. Defaults to:
     *      `<i class="glyphicon glyphicon-map-marker"></i>` for bootstrap 3.x
     *      `<i class="fas fa-map-marker-alt"></i>` for bootstrap 4.x
     */
    public $contentHeaderIcon;

    /**
     * @var string the icon shown when no flag is found. Defaults to:
     *      `<i class="glyphicon glyphicon-question-sign text-warning"></i>` for bootstrap 3.x
     *      `<i class="fas fa-question-circle text-warning"></i>` for bootstrap 4.x
     */
    public $noFlagIcon;

    /**
     * @var array the HTML attributes for the widget container. The following special tags are recognized:
     * - `tag`: string, the `tag` in which the content will be rendered. Defaults to `div`.
     */
    public $options = [];

    /**
     * @var array the default field keys and labels setting (@see `initOptions` method)
     */
    protected $_defaultIcons = [
        'errorIcon' => ['glyphicon glyphicon-exclamation-sign text-danger', 'fas fa-exclamation-circle text-danger'],
        'contentHeaderIcon' => ['glyphicon glyphicon-map-marker', 'fas fa-map-marker-alt'],
        'noFlagIcon' => ['glyphicon glyphicon-question-sign text-warning', 'fas fa-question-circle text-warning'],

    ];

    /**
     * List of default fields
     * @var array
     */
    protected $_defaultFields = [
        'ip',
        'continentCode',
        'continent',
        'countryCode',
        'country',
        'flag',
        'region',
        'regionName',
        'city',
        'district',
        'zip',
        'lat',
        'lon',
        'timezone',
        'currency',
        'isp',
        'org',
        'as',
        'asname',
        'reverse',
        'mobile',
        'proxy',
    ];

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
        $this->template += [
            'popoverButton' => '{flag} {countryCode}',
            'popoverContent' => '{table}',
            'inlineContent' => '{flag} {table}' // when showPopover is false
        ];
        if (!isset($this->noData)) {
            $this->noData = empty($this->ip) ? Yii::t('kvip', "No data found for the user's IP address.") :
                Yii::t('kvip', 'No data found for IP address {ip}.', ['ip' => '<kbd>' . $this->ip . '</kbd>']);
        }
        $this->initIcons();
        if (!isset($this->errorDataOptions['title'])) {
            $this->errorDataOptions['title'] = Yii::t('kvip', 'IP fetch error');
        }
        if (!isset($this->ip)) {
            $this->ip = Yii::$app->request->getUserIP();
        }
        $this->popoverOptions = ArrayHelper::merge([
            'size' => PopoverX::SIZE_MEDIUM,
            'placement' => PopoverX::ALIGN_AUTO_HORIZONTAL,
            'options' => ['class' => 'kv-ipinfo-popover'],
        ], $this->popoverOptions);
        if (isset($this->pjaxContainerId) && !isset($this->popoverOptions['pjaxContainerId'])) {
            $this->popoverOptions['pjaxContainerId'] = $this->pjaxContainerId;
        }
        if (!isset($this->requestConfig)) {
            $this->requestConfig = [
                'format' => Client::FORMAT_JSON,
                'method' => 'GET',
            ];
        }
        if ($this->hideEmpty && !isset($this->detailViewConfig['template'])) {
            $this->detailViewConfig['template'] = function ($attribute) {
                if (!empty($attribute['value'])) {
                    $captionOptions = ArrayHelper::getValue($attribute, 'captionOptions', []);
                    $contentOptions = ArrayHelper::getValue($attribute, 'contentOptions', []);
                    Html::addCssClass($captionOptions, 'kv-data-label');
                    Html::addCssClass($contentOptions, 'kv-data-value');
                    return Html::tag(
                        'tr',
                        Html::tag('th', $attribute['label'], $captionOptions) .
                        Html::tag('td', $attribute['value'], $contentOptions),
                        $this->detailRowOptions
                    );
                } else {
                    return '';
                }
            };
        }
        if (!isset($this->fields)) {
            $this->fields = $this->_defaultFields;
        }
        if (isset($this->skipFields)) {
            $this->fields = array_diff($this->fields, $this->skipFields);
        }
    }

    /**
     * Get default attributes configuration for the detail view
     * @param IpInfoModel $model
     * @return array
     */
    public function getDetailViewAttributes($model)
    {
        return [
            'ip',
            ['attribute' => 'continent', 'value' =>  empty($model->continent) && empty($model->continentCode) ? '' :
                $model->continentCode . ' - ' . $model->continent],
            ['attribute' => 'countryDetail', 'format' => 'raw'],
            ['attribute' => 'regionName', 'value' => $model->region . ' - ' . $model->regionName],
            'city',
            'district',
            'zip',
            'latitude',
            'longitude',
            'timezone',
            'currency',
            'isp',
            'org',
            'as',
            'mobile',
            'proxy',
        ];
    }

    /**
     * Initializes icons markup based on Bootstrap version
     * @throws InvalidConfigException
     */
    protected function initIcons()
    {
        $isBs4 = $this->isBs4();
        foreach ($this->_defaultIcons as $property => $setting) {
            if (!isset($this->$property)) {
                $css = $isBs4 ? $setting[1] : $setting[0];
                $this->$property = Html::tag('i', '', ['class' => $css]);
            }
        }
    }

    /**
     * Returns ip information details as an array
     * @return array|mixed
     * @throws InvalidConfigException
     * @throws Exception
     */
    protected function fetchIPDetails()
    {
        $key = "kvIp_{$this->ip}";
        $cache = ($this->cache && !empty(Yii::$app->cache)) ? Yii::$app->cache : false;
        if ($cache) {
            if ($this->flushCache) {
                $cache->delete($key);
            }
            $out = $cache->get($key);
            if ($out !== false) {
                return Json::decode($out);
            }
        }
        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
            'requestConfig' => $this->requestConfig,
        ]);
        $url = strtr($this->api, ['{ip}' => $this->ip]);
        /**
         * @var Request $request
         */
        $request = $client->createRequest()->setUrl($url);
        if (!empty($this->params)) {
            $request->setData($this->params);
        }
        $response = $request->send();
        if ($response->isOk) {
            $out = $response->getData();
            if ($cache) {
                $cache->set($key, Json::encode($out));
            }
            return $out;
        }
        return ['ip' => $this->ip, 'status' => 'error'];
    }

    /**
     * Renders the widget
     */
    protected function renderWidget()
    {
        if (empty($this->flagWrapperOptions['id'])) {
            $this->flagWrapperOptions['id'] = $this->options['id'] . '-flag';
        }
        $out = $this->fetchIPDetails() + ['ip' => $this->ip, 'noData' => $this->noData];
        $modelClass = $this->modelClass;
        /**
         * @var IpInfoModel $model
         */
        $model = new $modelClass(['ip' => $this->ip]);
        foreach ($this->fields as $field) {
            if ($field !== 'flag') {
                $model->$field = ArrayHelper::getValue($out, $field, '');
            }
        }
        $flag = $this->noFlagIcon;
        if ($this->showFlag) {
            Icon::map($this->getView(), Icon::FI);
            if (empty($this->flagOptions['class'])) {
                $this->flagOptions['class'] = 'flag-icon';
            }
            $flag = $model->getFlag($flag);
        }
        $flag = Html::tag('span', $flag, $this->flagWrapperOptions);
        $outData = '';
        if (!empty($out['status']) || !empty($out['error'])) {
            if (ArrayHelper::getValue($out, 'status', '') === 'success') {
                /**
                 * @var DetailView $detailViewClass
                 */
                $detailViewClass = $this->detailViewClass;
                $this->detailViewConfig['model'] = $model;
                if (!isset($this->detailViewConfig['attributes'])) {
                    $this->detailViewConfig['attributes'] = $this->getDetailViewAttributes($model);
                }
                $outData = $detailViewClass::widget($this->detailViewConfig);
            } else {
                $message = ArrayHelper::getValue($out, 'message', '');
                $errorOut = str_replace(['{errorIcon}', '{message}'], [$this->errorIcon, $message], $this->errorData);
                $outData = Html::tag('div', $errorOut, $this->errorDataOptions);
            }
        }
        $defaults = array_fill_keys($this->_defaultFields, '');
        $output = $out + ['flag' => $flag] + $defaults;
        $pairs = [];
        foreach ($output as $key => $value) {
            $pairs['{' . $key . '}'] = $value;
        }
        $pairs['{table}'] = strtr($outData, $pairs);
        if ($this->showPopover) {
            $popoverButton = strtr($this->template['popoverButton'], $pairs);
            $popoverContent = strtr($this->template['popoverContent'], $pairs);
            $header = isset($this->contentHeader) ? $this->contentHeader : Yii::t('kvip', 'IP Position Details');
            $this->popoverOptions['header'] = $this->contentHeaderIcon . ' ' . $header;
            $popOpts = $this->popoverOptions;
            if (!isset($popOpts['toggleButton']) && !isset($popOpts['toggleButton']['class'])) {
                $this->popoverOptions['toggleButton']['class'] = 'kv-ipinfo-button';
            }
            $this->popoverOptions['toggleButton']['label'] = $popoverButton;
            $this->popoverOptions['content'] = $popoverContent;
            $content = PopoverX::widget($this->popoverOptions);
        } else {
            $inlineContent = strtr($this->template['inlineContent'], $pairs);
            $content = Html::tag('div', $inlineContent, $this->inlineContentOptions);
        }
        IpInfoAsset::register($this->getView());
        echo $content;
    }
}
