<?php
/**
 * Text/Paragraph
 *
 * ex:
 * [ffb_paragraph_0 unique_id=\"1mvk57sf\" data=\"%7B%22o%22%3A%7B%22gen%22%3A%7B%22ffsys-disabled%22%3A%220%22%2C%22ffsys-info%22%3A%22%7B%7D%22%2C%22text-is-richtext%22%3A%221%22%2C%22align%22%3A%22text-left%22%2C%22align-sm%22%3A%22%22%2C%22align-md%22%3A%22%22%2C%22align-lg%22%3A%22%22%7D%7D%7D\"][ffb_param route=\"o gen text\"]<h2>TEST h2</h2>[/ffb_param][/ffb_paragraph_0]
 */

namespace Optimizme\Mazen\FreshBuilder;

class ffb_paragraph_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
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


class ffb_paragraph_2 extends ffb_paragraph_0
{

}