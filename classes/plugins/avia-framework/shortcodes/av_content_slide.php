<?php
namespace Optimizme\Mazen\AviaFramework;

class av_content_slide extends \Optimizme\Mazen\OptimizmeMazenPluginAviaFramework
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h3'];
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
        $allowedTags = ['h3'];
        $result = $this->setSimpleAviaBuilderShortcode($tag, $allowedTags, $newDatas[0], 'title');

        return $result;
    }
}
