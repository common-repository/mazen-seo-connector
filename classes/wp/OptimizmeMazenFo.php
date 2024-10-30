<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenFo
 * Front-office
 * @package Optimizme\Mazen
 */
class OptimizmeMazenFo
{
    /**
     * OptimizmeMazenFo constructor.
     */
    public function __construct()
    {
        // add meta in <head>
        add_action('wp_head', array( $this, 'mazenAddHeader' ), 6);

        // add css and js
        add_action('wp_enqueue_scripts', array( $this, 'mazenFoEnqueueScripts'));

        // wrap content with div
        add_filter('the_content', array($this, 'mazenWrapContent'));

        // filter SEO title
        add_filter('pre_get_document_title', array($this, 'mazenSeoTitle'), 10);
    }

    /**
     * Add meta in <head>
     */
    public function mazenAddHeader()
    {
        if (!OptimizmeMazenPluginsInteraction::isCompatibleSeoPluginInstalled()) {
            global $post;
            
            echo "\n".'<!-- Mazen SEO Connector - https://mazen-app.com -->' . "\n";

            // load meta description
            if (isset($post->ID) && $post->ID != '') {
                $metaKey = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('metadescription');
                $metaDescription = get_post_meta($post->ID, $metaKey, true);
            } else {
                $metaDescription = '';
            }

            if ($metaDescription != '') {
                echo '<meta name="description" content="'. strip_tags($metaDescription) .'" />' . "\n";
            }

            // canonical url: remove Wordpress default and add custom canonical
            remove_action('wp_head', 'rel_canonical');
            $urlCanonique = OptimizmeMazenUtils::mazenGetCanonicalUrl($post);
            echo '<link rel="canonical" href="'. strip_tags($urlCanonique) .'" />'. "\n";

            // meta robots : meta robots ONLY if "blog_public" option is 1, else Wordress do the job with "index,nofollow"
            if (get_option('blog_public') == 1) {
                $isNoIndex = OptimizmeMazenUtils::mazenGetMetaRobot($post, 'noindex');
                if ($isNoIndex == 1) {
                    $libelleMetaIndex = 'noindex';
                } else {
                    $libelleMetaIndex = 'index';
                }

                $isNoFollow = OptimizmeMazenUtils::mazenGetMetaRobot($post, 'nofollow');
                if ($isNoFollow == 1) {
                    $libelleMetaFollow = 'nofollow';
                } else {
                    $libelleMetaFollow = 'follow';
                }

                echo '<meta name="robots" content="'. strip_tags($libelleMetaIndex .','. $libelleMetaFollow) .'" />'. "\n";
            }

            echo '<!-- / Mazen SEO Connector -->' ."\n\n";
        }
    }

    /**
     * Wrap content with class, for custom post_content display
     * @param $content
     * @return string
     */
    public function mazenWrapContent($content)
    {
        // preview from MAZEN ? load all post_content by posted content
        if (isset($_POST['preview_content']) && $_POST['preview_content'] != '') {
            $content = '<div class="alert alert-info">'. __('Preview for this post:', 'mazen-seo-connector') .'</div>';
            $content .= stripslashes(urldecode($_POST['preview_content']));
        }

        return '<div class="optimizme_wrap_content">'. $content . '</div>';
    }

    /**
     * Add css in front-office
     */
    public function mazenFoEnqueueScripts()
    {
        //wp_enqueue_style("optimizme_css_fo", OPTIMIZME_MAZEN_FOR_WP_URL .'assets/css/optimizme_fo.css');
    }


    /**
     * Manage custom SEO title
     * @param $title
     * @return mixed
     */
    public function mazenSeoTitle($title)
    {
        global $post;
        if (is_object($post) && isset($post->ID) && is_numeric($post->ID)) {
            if (!OptimizmeMazenPluginsInteraction::isCompatibleSeoPluginInstalled()) {
                // no compatible SEO plugin - we have to manage SEO title
                $seoTitle = get_post_meta($post->ID, 'optimizme_metatitle', true);
                if ($seoTitle != '') {
                    $title = $seoTitle;
                }
            }
        }

        return $title;
    }
}
