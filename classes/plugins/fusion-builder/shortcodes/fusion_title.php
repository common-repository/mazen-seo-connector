<?php

namespace Optimizme\Mazen\FusionBuilder;

class fusion_title extends \Optimizme\Mazen\OptimizmeMazenPluginFusionBuilder
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        $elementTag = 'h'.$this->attributes['size'];
        if ($tag == $elementTag) {
            $result = $this->getSimpleInnerFusionBuilderShortcode($tag, $allowedTags);
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

        $allowedTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        $elementTag = 'h'.$this->attributes['size'];
        if ($tag == $elementTag) {
            $newData = $newDatas[0];
            $result = $this->setSimpleInnerFusionBuilderShortcode($tag, $allowedTags, $newData);
        }

        return $result;
    }
}
