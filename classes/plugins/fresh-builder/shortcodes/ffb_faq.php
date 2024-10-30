<?php
/**
 * Iconbox
 *
 * Default: générate h3
 */

namespace Optimizme\Mazen\FreshBuilder;

class ffb_faq_0 extends \Optimizme\Mazen\OptimizmeMazenPluginFreshBuilder
{
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
            $tagsHx = ['h3'];
            if (in_array($tag, $tagsHx)) {
                $blocks = $this->extractShortcodesInnerFfbParam(['title']);
                $result = $this->extractInnerFromShortcodes($blocks);
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
            // this shortcode can act on Hx
            $tagsHx = ['h3'];
            if (in_array($tag, $tagsHx)) {
                // get ffb_param shortcodes inside main shortcode
                $blocks = $this->extractShortcodesInnerFfbParam(['title']);
                $result = $this->setShortcodesInnerFfbParam($blocks, $newDatas);
            }

            // blocs html génériques
            //$result = parent::setHtmlGenerique($result, $newDatas, $tag);
        }


        return $result;
    }
}
