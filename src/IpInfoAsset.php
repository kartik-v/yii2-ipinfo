<?php
/**
 * @package   yii2-ipinfo
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2019
 * @version   2.0.1
 */

namespace kartik\ipinfo;

use Yii;
use kartik\base\AssetBundle;

/**
 * Asset bundle for IpInfo widget.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class IpInfoAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/ipinfo']);
        parent::init();
    }
}