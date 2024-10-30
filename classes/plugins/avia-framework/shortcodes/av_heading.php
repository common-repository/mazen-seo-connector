<?php
/**
 *
 * ex : [av_heading heading='Hello. je suis un contenu special heading' tag='h2' style='blockquote modern-quote' size='' subheading_active='subheading_above' subheading_size='10' padding='10' color='' custom_font='' admin_preview_bg=''] <h4>Sub heading</h4> [/av_heading]
 */

namespace Optimizme\Mazen\AviaFramework;

class av_heading extends \Optimizme\Mazen\OptimizmeMazenPluginAviaFramework
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        $result = $this->getSimpleAviaBuilderShortcode($tag, $allowedTags, 'heading', 'tag');

        return $result;
    }

    /**
     * @param $tag
     * @param $newDatas
     * @return array
     */
    public function set($tag, $newDatas)
    {
        $allowedTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        $result = $this->setSimpleAviaBuilderShortcode($tag, $allowedTags, $newDatas[0], 'heading', 'tag');

        return $result;
    }
}
