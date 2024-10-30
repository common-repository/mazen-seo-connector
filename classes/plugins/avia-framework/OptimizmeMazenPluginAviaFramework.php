<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginAviaFramework
 * @package Optimizme\Mazen
 */
class OptimizmeMazenPluginAviaFramework extends OptimizmeMazenPluginsShortcode
{
    public $saveInWpContent = 1;
    public $namespace = "\Optimizme\Mazen\AviaFramework\\";
    public $postmetaKeyAviaContent = '_aviaLayoutBuilderCleanData';

    /**
     * @param $post
     * @param $tabData
     */
    public function save($post, $tabData)
    {
        // builder active ?
        $status = get_post_meta($post->ID, '_aviaLayoutBuilder_active', true);
        $isAviaActive = apply_filters('avf_builder_active', $status, $post->ID);

        if (isset($isAviaActive) && $isAviaActive == 'active') {
            update_post_meta($post->ID, $this->postmetaKeyAviaContent, $tabData['content']);
        }
    }

    /**
     * @param $tag
     * @return array
     */
    public function getShortcodesGeneratingTag($tag)
    {
        $tabShortcodes = [];
        if ($tag == 'h1' || $tag == 'h2' || $tag == 'h3' || $tag == 'h4' || $tag == 'h5' || $tag == 'h6') {
            $tabShortcodes = [
                'av_heading',
                'av_icon_box',
                'av_iconlist_item',
                'av_content_slide',
                //'av_content_slider',
                'av_contact',
                'av_team_member',
                'av_social_share',
            ];
        }
        return $tabShortcodes;
    }

    /**
     * @param $tag : requested tag
     * @param array $tagsAllowed : tags allowed in this shortcode
     * @param string $contentAttr
     * @param string $compareAttr
     * @return array
     */
    public function getSimpleAviaBuilderShortcode($tag, array $tagsAllowed, $contentAttr = '', $compareAttr = '')
    {
        $result = [];
        if (in_array($tag, $tagsAllowed)) {
            if ($contentAttr != '' && $this->attributes[$contentAttr] != '') {
                if ($compareAttr != '') {
                    if ($tag == $this->attributes[$compareAttr]) {
                        $result[] = $this->attributes[$contentAttr];
                    }
                } else {
                    $result[] = $this->attributes[$contentAttr];
                }
            }
        }

        return $result;
    }

    /**
     * @param $tag
     * @param array $tagsAllowed
     * @param $newData
     * @param string $attr
     * @param string $compareAttr
     * @return array
     */
    public function setSimpleAviaBuilderShortcode($tag, array $tagsAllowed, $newData, $attr = '', $compareAttr = '')
    {
        $result = [];
        $boolChange = 0;
        if (in_array($tag, $tagsAllowed)) {

            if ($this->attributes[$attr] != '') {
                if ($compareAttr != '') {
                    if ($compareAttr != '' && $this->attributes[$compareAttr] == $tag) {
                        $boolChange = 1;
                        $this->attributes[$attr] = $newData;
                    }
                } else {
                    $boolChange = 1;
                    if ($attr != '') {
                        $this->attributes[$attr] = $newData;
                    } else {
                        $this->inner = $newData;
                    }
                }

                if ($boolChange == 1) {
                    $result = $this->returnDiffShortcodes([$newData]);
                }
            }
        }

        return $result;
    }

}
