<?php

namespace Optimizme\Mazen\FusionBuilder;

class fusion_tagline_box extends \Optimizme\Mazen\OptimizmeMazenPluginFusionBuilder
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h2'];
        $result = $this->getSimpleInnerFusionBuilderShortcode($tag, $allowedTags, 'title', 1);

        return $result;
    }

    /**
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $allowedTags = ['h2'];
        $result = $this->setSimpleInnerFusionBuilderShortcode($tag, $allowedTags, $newDatas[0], 'title', 1);

        return $result;
    }
}
