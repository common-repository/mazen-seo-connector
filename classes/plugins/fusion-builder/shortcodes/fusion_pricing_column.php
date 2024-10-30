<?php

namespace Optimizme\Mazen\FusionBuilder;

class fusion_pricing_column extends \Optimizme\Mazen\OptimizmeMazenPluginFusionBuilder
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h3'];
        $result = $this->getSimpleInnerFusionBuilderShortcode($tag, $allowedTags, 'title');

        return $result;
    }

    /**
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $allowedTags = ['h3'];
        $result = $this->setSimpleInnerFusionBuilderShortcode($tag, $allowedTags, $newDatas[0], 'title');

        return $result;
    }
}
