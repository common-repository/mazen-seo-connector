<?php
/**
 *
 * ex : [av_social_share title='Share this entry' style='minimal' buttons='' admin_preview_bg='']
 */

namespace Optimizme\Mazen\AviaFramework;

class av_social_share extends \Optimizme\Mazen\OptimizmeMazenPluginAviaFramework
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h5'];
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
        $allowedTags = ['h5'];
        $result = $this->setSimpleAviaBuilderShortcode($tag, $allowedTags, $newDatas[0], 'title');

        return $result;
    }
}
