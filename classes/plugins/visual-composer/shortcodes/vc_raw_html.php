<?php

namespace Optimizme\Mazen\VisualComposer;

use Optimizme\Mazen\OptimizmeMazenDomParsing;

class vc_raw_html extends \Optimizme\Mazen\OptimizmeMazenPluginVisualComposer
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
            $htmlContent = rawurldecode(base64_decode($this->inner));
            $mazenDom = new OptimizmeMazenDomParsing();
            $mazenDom->getNodesInContent($htmlContent, $tag);
            if ($mazenDom->nbNodes > 0) {
                foreach ($mazenDom->nodes as $node) {
                    $result[] = $mazenDom->getNodeValue($node);
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
    public function set($tag, $newDatas)
    {
        $result = [];

        // this shortcode can act on Hx
        $tagsHx = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        if (in_array($tag, $tagsHx)) {
            $htmlContent = rawurldecode(base64_decode($this->inner));
            $mazenDom = new OptimizmeMazenDomParsing();
            $mazenDom->getNodesInContent($htmlContent, $tag);
            $mazenDom->changeNodesValues($newDatas);
            $newContent = base64_encode($mazenDom->getHtml());
        }

        if (!empty($mazenDom->tabSuccess)) {
            // data changed
            $this->inner = $newContent;
            $result = $this->returnDiffShortcodes($mazenDom->tabSuccess);
        }

        return $result;
    }
}
