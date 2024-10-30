<?php
/**
 *
 * ex : [av_team_member name='John Doe' job='job title' src='' attachment='' attachment_size='' description=' <h4>description</h4> ' font_color='' custom_title='' custom_content='' admin_preview_bg='']
 */

namespace Optimizme\Mazen\AviaFramework;

class av_team_member extends \Optimizme\Mazen\OptimizmeMazenPluginAviaFramework
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h3'];
        $result = $this->getSimpleAviaBuilderShortcode($tag, $allowedTags, 'name');

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
        $result = $this->setSimpleAviaBuilderShortcode($tag, $allowedTags, $newDatas[0], 'name');

        return $result;
    }
}
