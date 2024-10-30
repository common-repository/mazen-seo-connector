<?php
/**
 * Heading
 *
 * Default: générate h2
 *
 * ex:
 * [ffb_heading_0 unique_id="1k37e09k" data="%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22text-is-richtext%22%3A%220%22%2C%22tag%22%3A%22h2%22%2C%22align%22%3A%22text-center%22%2C%22align-sm%22%3A%22%22%2C%22align-md%22%3A%22%22%2C%22align-lg%22%3A%22%22%7D%7D%7D"][ffb_param route="o gen text"]Big Heading default[/ffb_param][/ffb_heading_0]
 */

namespace Optimizme\Mazen\FreshBuilder;

class ffb_team_1_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
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
                case 'ffb_team_3' :
                case 'ffb_team_7' :
                case 'ffb_team_8' :
                case 'ffb_team_9' :
                case 'ffb_team_10' :
                case 'ffb_team_11' :
                case 'ffb_team_13' :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['name']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                case 'ffb_team_6' :
                    $tagsHx = ['h2'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['name']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                case 'ffb_team_12' :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['name']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }

                    $tagsHx = ['h5'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['position']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }

                    break;

                default :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['personal-info', 'name']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
            }

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
            // this shortcode can act on Hx
            switch ($this->child) {
                case 'ffb_team_3' :
                case 'ffb_team_7' :
                case 'ffb_team_8' :
                case 'ffb_team_9' :
                case 'ffb_team_10' :
                case 'ffb_team_11' :
                case 'ffb_team_13' :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['name']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                case 'ffb_team_6' :
                    $tagsHx = ['h2'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['name']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                case 'ffb_team_12' :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['name']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }

                    $tagsHx = ['h5'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['position']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                default:
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['personal-info', 'name']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
            }

        }

        return $result;
    }
}

class ffb_team_1_2 extends ffb_team_1_0
{

}

class ffb_team_2_0 extends ffb_team_1_0
{

}

class ffb_team_2_4 extends ffb_team_1_0
{

}

class ffb_team_3_0 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_3';
    }
}

class ffb_team_4_0 extends ffb_team_1_0
{

}

class ffb_team_5_0 extends ffb_team_1_0
{

}

class ffb_team_6_0 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_6';
    }
}

class ffb_team_7_0 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_7';
    }
}

class ffb_team_7_2 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_7';
    }
}

class ffb_team_8_0 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_8';
    }
}

class ffb_team_8_2 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_8';
    }
}

class ffb_team_9_0 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_9';
    }
}

class ffb_team_10_0 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_10';
    }
}

class ffb_team_11_0 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_11';
    }
}

class ffb_team_12_0 extends ffb_team_1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_team_12';
    }
}

class ffb_team_13_0 extends ffb_team_1_0
{

}