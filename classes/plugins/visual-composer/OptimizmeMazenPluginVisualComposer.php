<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginVisualComposer
 * @package Optimizme\Mazen
 */
class OptimizmeMazenPluginVisualComposer extends OptimizmeMazenPluginsShortcode
{
    public $saveInWpContent = 1;
    public $namespace = "\Optimizme\Mazen\VisualComposer\\";

    /**
     * List of shortcodes that could generate this specified tag
     * @param $tag
     * @return array
     */
    public function getShortcodesGeneratingTag($tag)
    {
        $tabShortcodes = [];
        if ($tag == 'h1' || $tag == 'h2' || $tag == 'h3' || $tag == 'h4' || $tag == 'h5' || $tag == 'h6') {
            $tabShortcodes = [
                'vc_custom_heading',    // custom heading
                'vc_raw_html',          // raw html
                'vc_toggle',            // faq
            ];

            if ($tag == 'h2') {
                $tabShortcodes[] = 'vc_hoverbox';           // hover box
                $tabShortcodes[] = 'vc_single_image';       // single image
                $tabShortcodes[] = 'vc_gallery';            // single image
                $tabShortcodes[] = 'vc_images_carousel';    // image carousel
                $tabShortcodes[] = 'vc_tta_tabs';           // tabs
                $tabShortcodes[] = 'vc_tta_tour';           // tour
                $tabShortcodes[] = 'vc_tta_accordion';      // accordion
                $tabShortcodes[] = 'vc_tta_pageable';       // pageable container
                $tabShortcodes[] = 'vc_cta';                // pageable container
                $tabShortcodes[] = 'vc_widget_sidebar';     // widgetised sidebar
                $tabShortcodes[] = 'vc_posts_slider';       // posts sidebar
                $tabShortcodes[] = 'vc_video';              // video player
                $tabShortcodes[] = 'vc_gmaps';              // google maps
                $tabShortcodes[] = 'vc_flickr';             // flickr widget
                $tabShortcodes[] = 'vc_progress_bar';       // progress bar
                $tabShortcodes[] = 'vc_line_chart';         // line chart
                $tabShortcodes[] = 'vc_wp_search';          // wp search
                $tabShortcodes[] = 'vc_round_chart';        // round chart
            }
            if ($tag == 'h4') {
                $tabShortcodes[] = 'vc_text_separator';     // separator with text
                $tabShortcodes[] = 'vc_tta_section';        // tab (from tabs)
                $tabShortcodes[] = 'vc_cta';                // pageable container
                $tabShortcodes[] = 'vc_pie';                // pie chart
            }
        }

        return $tabShortcodes;
    }

    /**
     * @param $tag
     * @param array $tagsAllowed
     * @param $attr
     * @return array
     */
    public function getSimpleAttrInVcShortcode($tag, array $tagsAllowed, $attr)
    {
        $result = [];
        if (in_array($tag, $tagsAllowed)) {
            if (isset($this->attributes[$attr])) {
                $result[] = $this->attributes[$attr];
            }
        }

        return $result;
    }

    /**
     * @param $tag
     * @param array $tagsAllowed
     * @param $attr
     * @param $newData
     * @return array
     */
    public function setSimpleAttrInVcShortcode($tag, array $tagsAllowed, $attr, $newData, $force = 0)
    {
        $result = [];
        if (in_array($tag, $tagsAllowed)) {
            if (isset($this->attributes[$attr]) || $force == 1) {
                $this->attributes[$attr] = $newData;
                $result = $this->returnDiffShortcodes([$newData]);
            }
        }

        return $result;
    }
}
