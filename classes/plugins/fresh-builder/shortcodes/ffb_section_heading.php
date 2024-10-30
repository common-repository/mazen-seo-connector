<?php

namespace Optimizme\Mazen\FreshBuilder;

class ffb_section_heading_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $result = [];

        if (!parent::isBlockDisabled()) {
            // this shortcode can act on Hx
            $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            if (in_array($tag, $tagsHx)) {
                // get ffb_param shortcodes inside main shortcode
                $blocks = $this->extractShortcodesInnerFfbParam([$tag]);
                $result = $this->extractInnerFromShortcodes($blocks);
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
            $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
            if (in_array($tag, $tagsHx)) {
                // get ffb_param shortcodes inside main shortcode
                $blocks = $this->extractShortcodesInnerFfbParam([$tag]);
                $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
            }
        }

        return $result;
    }
}

class ffb_section_heading_2 extends ffb_section_heading_0
{

}
