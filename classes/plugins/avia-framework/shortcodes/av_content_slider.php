<?php
/**
 * ex : [av_content_slider heading='BIG HEADING' columns='1' animation='slide' navigation='arrows' autoplay='false' interval='5' font_color='' color=''] [av_content_slide title='Slide 1' link='' linktarget=''] <h4>h4 in slide content</h4> [/av_content_slide] [av_content_slide title='Slide 2' tags='' link='' linktarget=''] Slide Content goes here [/av_content_slide] [/av_content_slider]
 */

namespace Optimizme\Mazen\AviaFramework;

class av_content_slider extends \Optimizme\Mazen\OptimizmeMazenPluginAviaFramework
{
    /**
     * Extract generated HTML
     * @param $tag
     * @return array
     */
    public function get($tag)
    {
        $allowedTags = ['h3'];
        $result = $this->getSimpleAviaBuilderShortcode($tag, $allowedTags, 'heading');

        // sub shortcodes
        $args = ['content' => $this->inner, 'tag' => $tag];
        $dataSub = $this->extractHtmlFromShortcodes($args);
        if (is_array($dataSub) && !empty($dataSub)) {
            $result = array_merge($result, $dataSub);
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
        $allowedTags = ['h3'];
        $result = $this->setSimpleAviaBuilderShortcode($tag, $allowedTags, $newDatas[0], 'heading');
        if (!empty($result)) {
            array_shift($newDatas);
        }

        if (!empty($newDatas)) {
            // sub shortcodes
            $args = ['content' => $this->inner, 'tag' => $tag, 'newDatas' => $newDatas];
            $dataSub = $this->editShortcodesForHtmlTags($args);
            if (is_array($dataSub) && !empty($dataSub)) {
                //$result = array_merge($result, $dataSub);
            }
        }

        print_r($result); die;
        return $result;
    }
}
