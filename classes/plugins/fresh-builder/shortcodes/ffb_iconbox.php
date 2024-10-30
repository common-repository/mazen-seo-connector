<?php
/**
 * Iconbox
 *
 * Default: générate h3
 */

namespace Optimizme\Mazen\FreshBuilder;

class ffb_iconbox_1_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
{
    public $child = '';

    /**
     * Extract generated HTML
     * @param $tag
     * @return string
     */
    public function get($tag)
    {
        $result = '';

        if (!parent::isBlockDisabled()) {
            // this shortcode can act on Hx
            switch ($this->child) {
                case 'ffb_iconbox_2' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['sub-title', 'title']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                case 'ffb_iconbox_12' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['description']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                default:
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['title']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
            }

            // blocs html génériques
            //$result = parent::getHtmlGenerique($result, $tag);
        }

        return $result;
    }

    /**
     * @param $tag
     * @param array $newDatas
     * @return array
     */
    public function set($tag, array $newDatas)
    {
        $result = [];

        if (!parent::isBlockDisabled()) {
            // this shortcode can act on Hx.
            switch ($this->child) {
                case 'ffb_iconbox_2' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['sub-title', 'title']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                case 'ffb_iconbox_12' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['description']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                default:
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['title']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
            }

            // blocs html génériques
            //$result = parent::setHtmlGenerique($result, $newDatas, $tag);
        }

        return $result;
    }
}

class ffb_iconbox_2_0 extends ffb_iconbox_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_iconbox_2';
    }
}

class ffb_iconbox_2_3 extends ffb_iconbox_2_0
{

}

class ffb_iconbox_3_0 extends ffb_iconbox_1_0
{

}

class ffb_iconbox_4_0 extends ffb_iconbox_1_0
{

}

class ffb_iconbox_5_0 extends ffb_iconbox_1_0
{

}

class ffb_iconbox_6_0 extends ffb_iconbox_1_0
{

}

class ffb_iconbox_7_0 extends ffb_iconbox_1_0
{

}

class ffb_iconbox_8_0 extends ffb_iconbox_1_0
{

}

class ffb_iconbox_10_0 extends ffb_iconbox_1_0
{

}

class ffb_iconbox_12_0 extends ffb_iconbox_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_iconbox_12';
    }
}