<?php
/**
 * Interaction with third party plugins
 *  All In One SEO Pack
 *  Yoast SEO
 */

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenPluginsInteraction
 * @package Optimizme\Mazen
 */
class OptimizmeMazenPluginsInteraction
{
    /**
     * If a compatible SEO plugin is installed
     * @return int
     */
    public static function isCompatibleSeoPluginInstalled()
    {
        if (defined('YOAST_ENVIRONMENT')) {
            return 1;
        }
        if (defined('AIOSEOP_VERSION')) {
            return 1;
        }
        return 0;
    }

    /**
     * @param $type
     * @return string
     */
    public static function mazenGetPostMetaKeyFromType($type)
    {
        $metakey = '';

        if (defined('YOAST_ENVIRONMENT')) {
            // YOAST
            if ($type == 'noindex') {
                $metakey = '_yoast_wpseo_meta-robots-noindex';
            } elseif ($type == 'nofollow') {
                $metakey = '_yoast_wpseo_meta-robots-nofollow';
            } elseif ($type == 'metatitle') {
                $metakey = '_yoast_wpseo_title';
            } elseif ($type == 'metadescription') {
                $metakey = '_yoast_wpseo_metadesc';
            } elseif ($type == 'canonical') {
                $metakey = '_yoast_wpseo_canonical';
            }
        } elseif (defined('AIOSEOP_VERSION')) {
            // All In One SEO Pack
            if ($type == 'noindex') {
                $metakey = '_aioseop_noindex';
            } elseif ($type == 'nofollow') {
                $metakey = '_aioseop_nofollow';
            } elseif ($type == 'metatitle') {
                $metakey = '_aioseop_title';
            } elseif ($type == 'metadescription') {
                $metakey = '_aioseop_description';
            } elseif ($type == 'canonical') {
                $metakey = '_aioseop_custom_link';
            }
        } else {
            // Mazen
            if ($type == 'noindex') {
                $metakey = 'optimizme_meta-robots-noindex';
            } elseif ($type == 'nofollow') {
                $metakey = 'optimizme_meta-robots-nofollow';
            } elseif ($type == 'metatitle') {
                $metakey = 'optimizme_metatitle';
            } elseif ($type == 'metadescription') {
                $metakey = 'optimizme_metadesc';
            } elseif ($type == 'canonical') {
                $metakey = 'optimizme_canonical';
            }
        }

        return $metakey;
    }

    /**
     * @param $plugin : plugin yoast/aiosp
     * @param $post
     * @param $metaTitle
     * @return mixed
     */
    public static function getMetaTitleByPlugin($plugin, $post, $metaTitle)
    {
        if ($plugin == 'yoast') {
            $optionTitle = \WPSEO_Option_Titles::get_instance();
            $sep_options = $optionTitle->get_separator_options();

            if ($metaTitle == '') {
                // nothing set: get default syntax
                $tabDefault = $optionTitle->get_defaults();
                $metaTitle = $tabDefault['title-' . $post->post_type];
            }

            if (strstr($metaTitle, '%%')) {
                $wpseo_titles = get_option('wpseo_titles');
                $title = get_the_title($post->ID);

                if (isset($wpseo_titles['separator']) && isset($sep_options[$wpseo_titles['separator']])) {
                    $sep = $sep_options[$wpseo_titles['separator']];
                } else {
                    $sep = '-'; //setting default separator if Admin didn't set it from backed
                }

                $site_title = get_bloginfo('name');

                $metaTitle = str_replace('%%title%%', $title, $metaTitle);
                $metaTitle = str_replace(' %%page%% ', ' ', $metaTitle);
                $metaTitle = str_replace('%%sep%%', $sep, $metaTitle);
                $metaTitle = str_replace('%%sitename%%', $site_title, $metaTitle);
            }

            return $metaTitle;
        } elseif ($plugin == 'aiosp') {
            $aiosp = new \All_in_One_SEO_Pack();
            $metaTitle = $aiosp->get_aioseop_title($post);
            return $metaTitle;
        }
    }

    /**
     * Format meta robots data before save (all in one seo pack needs "on", yoast and mazen need "1")
     * @param $keyMeta
     * @return int|string
     */
    public static function formatMetaRobotBeforeSave($keyMeta)
    {
        if ($keyMeta == '_aioseop_noindex' || $keyMeta == '_aioseop_nofollow') {
            $saveValue = 'on';
        } else {
            $saveValue = 1;
        }
        return $saveValue;
    }

    /**
     * No specified meta robot value for a post: try to get a default setting which override
     * @param $post
     * @param string $type : noindex/nofollow
     * @return int
     */
    public static function getMetaRobotDefaultValue($post, $type)
    {
        // no value was found: allow the type (index/follow) unless a particular setting is set to no (noindex/nofollow)
        $value = 0;

        if (defined('YOAST_ENVIRONMENT')) {
            $pluginOptions = OptimizmeMazenPluginsInteraction::pluginGetOptions();
            if ($type == 'noindex') {
                $val = $pluginOptions['noindex-' . $post->post_type];
                if (isset($val) && $val == true) {
                    $value = 1;
                }
            }
        }

        if (defined('AIOSEOP_VERSION')) {
            $pluginOptions = OptimizmeMazenPluginsInteraction::pluginGetOptions();
            if ($type == 'noindex') {
                $key = 'aiosp_cpostnoindex';
            } else {
                $key = 'aiosp_cpostnofollow';
            }

            // for each noindex/nofollow set for post type
            if (is_array($pluginOptions[$key]) && !empty($pluginOptions[$key])) {
                foreach ($pluginOptions[$key] as $postTypeNoRobot) {
                    if ($postTypeNoRobot == $post->post_type) {
                        $value = 1;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * Get options from SEO plugin
     * @return array|mixed
     */
    public static function pluginGetOptions()
    {
        if (defined('YOAST_ENVIRONMENT')) {
            $pluginOptions = get_option('wpseo_titles');
        } elseif (defined('AIOSEOP_VERSION')) {
            $pluginOptions = get_option('aioseop_options');
        } else {
            $pluginOptions = [];
        }

        return $pluginOptions;
    }

    /**
     * @return array
     */
    public static function getPageBuildersActivated()
    {
        $tabPageBuilders = [];
        $tabPageBuilders = apply_filters('mazen_get_page_builder', $tabPageBuilders);

        return $tabPageBuilders;
    }

    /**
     * @param $post
     * @param $tabData
     * @return bool
     */
    public static function saveInThirdPartyPageBuilder($post, $tabData)
    {
        $saveToWordpress = false;
        $pageBuilders = OptimizmeMazenPluginsInteraction::getPageBuildersActivated();

        if (is_array($pageBuilders) && !empty($pageBuilders)) {
            foreach ($pageBuilders as $pageBuilder) {
                $classPluginPageBuilder = '\Optimizme\Mazen\OptimizmeMazenPlugin' . $pageBuilder;
                $pluginPageBuilder = new $classPluginPageBuilder();
                if (method_exists($classPluginPageBuilder, 'save')) {
                    $pluginPageBuilder->save($post, $tabData);
                }

                if ($pluginPageBuilder->saveInWpContent == 1) {
                    $saveToWordpress = true;
                }
            }
        }

        return $saveToWordpress;
    }

    /**
     * @param $pageBuilderClass
     * @param $method
     * @param array $args
     * @return array
     */
    public static function exectuteMethodFromPluginClass($pageBuilderClass, $method, $args = [])
    {
        $data = [];
        $classPageBuilder = '\Optimizme\Mazen\OptimizmeMazenPlugin' . $pageBuilderClass;
        if (class_exists($classPageBuilder)) {
            $pluginBuilder = new $classPageBuilder();
            if (method_exists($pluginBuilder, $method)) {
                $data = $pluginBuilder->$method($args);
            }
        }

        return $data;
    }

    /**
     * @return bool
     */
    public static function disableWpSpamshieldForMazen() {
        if (isset($_GET['mazenseoconnectorsearch']) && $_GET['mazenseoconnectorsearch'] != '') {
            return TRUE;
        } elseif (isset($_REQUEST['data_optme'])) {
            return TRUE;
        } else {
            $phpInput = file_get_contents('php://input');
            if ($phpInput) {
                return TRUE;
            }
        }
    }
}
