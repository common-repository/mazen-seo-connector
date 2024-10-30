<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginsFilters
 * @package Optimizme\Mazen
 */
class OptimizmeMazenPluginsFilters
{
    /**
     * @param $tabTags
     * @param string $content
     * @param string $tag
     * @return array
     */
    public static function getGeneratedHtmlInShortcodes($tabTags, $post, $tag = '')
    {
        $arrayShortcode = OptimizmeMazenPluginsShortcode::getDataInShortcode($post, $tag);
        if (is_array($arrayShortcode) && !empty($arrayShortcode)) {
            $tabTags = array_merge($tabTags, $arrayShortcode);
        }
        return $tabTags;
    }

    /**
     * @return array
     */
    public static function getPageBuilder()
    {
        $tabPageBuilders = [];

        if (defined('WPB_VC_VERSION')) {
            $tabPageBuilders[] = 'VisualComposer';
        }

        if (class_exists('\Elementor\Db')) {
            $tabPageBuilders[] = 'Elementor';
        }

        if (defined('SITEORIGIN_PANELS_VERSION')) {
            $tabPageBuilders[] = 'SiteOriginPageBuilder';
        }

        if (defined('AV_FRAMEWORK_VERSION')) {
            $tabPageBuilders[] = 'AviaFramework';
        }

        if (class_exists('ffFrameworkVersionManager')) {
            $tabPageBuilders[] = 'FreshBuilder';
        }

        if (defined('FUSION_BUILDER_VERSION')) {
            $tabPageBuilders[] = 'FusionBuilder';
        }

        return $tabPageBuilders;
    }
}
