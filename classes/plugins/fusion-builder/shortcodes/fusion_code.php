<?php

namespace Optimizme\Mazen\FusionBuilder;

use Optimizme\Mazen\OptimizmeMazenDomParsing;

class fusion_code extends \Optimizme\Mazen\OptimizmeMazenPluginFusionBuilder
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $result = [];

        // this shortcode can act on Hx
        $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        if (in_array($tag, $tagsHx)) {
            $htmlContent = html_entity_decode($this->inner);
            $mazenDom = new OptimizmeMazenDomParsing();
            $mazenDom->getNodesInContent($htmlContent, $tag);
            $result = $mazenDom->getNodesValues();
        }
        return $result;
    }

    /**
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $result = [];

        // this shortcode can act on Hx
        $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        if (in_array($tag, $tagsHx)) {
            $htmlContent = html_entity_decode($this->inner);
            $mazenDom = new OptimizmeMazenDomParsing();
            $mazenDom->getNodesInContent($htmlContent, $tag);
            $mazenDom->changeNodesValues($newDatas);
            $newContent = htmlentities($mazenDom->getHtml());

            if (!empty($mazenDom->tabSuccess)) {
                // data changed
                $this->inner = $newContent;
                $result = $this->returnDiffShortcodes($mazenDom->tabSuccess);
            }
        }

        return $result;
    }
}
