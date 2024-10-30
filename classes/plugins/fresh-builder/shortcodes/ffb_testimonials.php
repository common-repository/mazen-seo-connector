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

class ffb_testimonials1_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
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
                case 'ffb_testimonials2_0' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['title']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                case 'ffb_testimonials3_0' :
                    $tagsHx = ['h6'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['subtitle text']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }

                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['author']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }

                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['title']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                case 'ffb_testimonials4_0' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['one-title']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }

                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['review', 'author']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                case 'ffb_testimonials6_0' :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['name']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                case 'ffb_testimonials7_0' :
                    $tagsHx = ['h2'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['title text']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }

                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['author full-name']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                case 'ffb_testimonials8_0' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['title text']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }

                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['author-name author']);
                        $result = $this->extractInnerFromShortcodes($blocks);
                    }
                    break;

                default :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        $blocks = $this->extractShortcodesInnerFfbParam(['author']);
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
                case 'ffb_testimonials2_0' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['title']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                case 'ffb_testimonials3_0' :
                    $tagsHx = ['h6'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['subtitle text']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }

                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['author']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }

                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['title']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                case 'ffb_testimonials4_0' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['one-title']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }

                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['review', 'author']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                case 'ffb_testimonials6_0' :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['name']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                case 'ffb_testimonials7_0' :
                    $tagsHx = ['h2'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['title text']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }

                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['author full-name']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                case 'ffb_testimonials8_0' :
                    $tagsHx = ['h3'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['title text']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }

                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['author-name author']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
                    break;

                default :
                    $tagsHx = ['h4'];
                    if (in_array($tag, $tagsHx)) {
                        // get ffb_param shortcodes inside main shortcode
                        $blocks = $this->extractShortcodesInnerFfbParam(['author']);
                        $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
                    }
            }

        }

        return $result;
    }
}

class ffb_testimonials2_0 extends ffb_testimonials1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_testimonials2_0';
    }
}

class ffb_testimonials3_0 extends ffb_testimonials1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_testimonials3_0';
    }
}

class ffb_testimonials4_0 extends ffb_testimonials1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_testimonials4_0';
    }
}

class ffb_testimonials5_0 extends ffb_testimonials1_0
{

}

class ffb_testimonials6_0 extends ffb_testimonials1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_testimonials6_0';
    }
}

class ffb_testimonials7_0 extends ffb_testimonials1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_testimonials7_0';
    }
}

class ffb_testimonials_8_0 extends ffb_testimonials1_0
{
    public function __construct($shortcode)
    {
        parent::__construct($shortcode);
        $this->child = 'ffb_testimonials8_0';
    }
}

class ffb_testimonials9_0 extends ffb_testimonials1_0
{

}