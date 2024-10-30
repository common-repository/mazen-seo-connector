<?php

namespace Optimizme\Mazen\FusionBuilder;

class fusion_content_box extends \Optimizme\Mazen\OptimizmeMazenPluginFusionBuilder
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        if ($tag == 'h2') {
            $result = $this->getSimpleInnerFusionBuilderShortcode($tag, ['h2'], 'title', 1);
        } else {
            $result = [];
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

        $allowedTags = ['h2'];
        if ($tag == 'h2') {
            $newData = $newDatas[0];
            $result = $this->setSimpleInnerFusionBuilderShortcode($tag, $allowedTags, $newData, 'title', 1);
        }

        return $result;
    }
}
