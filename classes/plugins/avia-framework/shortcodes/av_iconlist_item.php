<?php

namespace Optimizme\Mazen\AviaFramework;

class av_iconlist_item extends \Optimizme\Mazen\OptimizmeMazenPluginAviaFramework
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h4'];
        $result = $this->getSimpleAviaBuilderShortcode($tag, $allowedTags, 'title');

        return $result;
    }

    /**
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $allowedTags = ['h4'];
        $result = $this->setSimpleAviaBuilderShortcode($tag, $allowedTags, $newDatas[0], 'title');

        return $result;
    }
}
