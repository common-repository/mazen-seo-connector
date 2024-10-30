<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenUtils
 */
class OptimizmeMazenUtils
{
    /**
     * Affichage des derniers articles du blog
     * @param $feed
     * @param $nbElements
     */
    public static function mazenShowNewsRss($feed, $nbElements)
    {
        /* @var $item WP_Post */
        $maxitems = 0;
        $rss_items = []; ?>
        <h3><?php _e('Recent posts from Mazen blog: ', 'mazen-seo-connector'); ?></h3>

        <?php
        // Get a SimplePie feed object from the specified feed source.
        $rss = fetch_feed($feed);

        if (!is_wp_error($rss)) : // Checks that the object is created correctly
            // Figure out how many total items there are, but limit it to 5.
            $maxitems = $rss->get_item_quantity($nbElements);

            // Build an array of all the items, starting with element 0 (first element).
            $rss_items = $rss->get_items(0, $maxitems);
        else :
            echo $rss->get_error_message();
        endif; ?>

        <ul>
            <?php if ($maxitems == 0) : ?>
                <li><?php _e('No post found', 'mazen-seo-connector'); ?></li>
            <?php else : ?>
                <?php foreach ($rss_items as $item) : ?>
                    <li>
                        <a href="<?php echo strip_tags($item->get_permalink()); ?>"
                           title="<?php printf(__('Posted at %s', 'mazen-seo-connector'), $item->get_date('j F Y | g:i a')); ?>"
                           target="_blank">
                            <?php echo strip_tags($item->get_title()); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <?php

    }

    /**
     * If string is not in utf-8, encode it
     * @param $str
     * @return string
     */
    public static function safeUtf8Encode($str)
    {
        if (!(bool)preg_match('//u', $str)) {
            $str = utf8_encode($str);
        }

        // remove  \t \n...
        $str = OptimizmeMazenUtils::removeSpecialSeparatorChars($str);

        return $str;
    }

    /**
     * @param $str
     * @return mixed
     */
    public static function removeSpecialSeparatorChars($str)
    {
        // remove  \t \n...
        $str = preg_replace('/(\v|\s)+/', ' ', $str);

        return $str;
    }

    /**
     * @param $message
     * @param string $statut : updated / error
     */
    public static function mazenShowMessageBackoffice($message, $statut = 'updated')
    {
        ?>
        <div class="<?php echo strip_tags($statut) ?> notice">
            <p><?php echo strip_tags($message) ?></p>
        </div>
        <?php

    }

    /**
     * Check if media exists in media library (search by title)
     * @param $urlFile
     * @return bool
     */
    public static function mazenIsMediaInLibrary($urlFile)
    {
        $basenameFile = basename($urlFile);
        $media = get_page_by_title($basenameFile, 'OBJECT', 'attachment');

        if ($media && $media->ID != '') {
            return wp_get_attachment_url($media->ID);
        } else {
            return false;
        }
    }

    /**
     * Add media in library
     * @param $urlFile : URL where to download and copy file
     * @return false|string
     */
    public static function mazenAddMediaInLibrary($urlFile)
    {
        $nameFile = basename($urlFile);

        $uploaddir = wp_upload_dir();
        $uploadfile = $uploaddir['path'] . '/' . $nameFile;

        // write media file in media
        if (strstr($urlFile, 'passerelle.dev')) {
            // have a valid and existing remote image when using localhost server
            $urlFile = 'http://www.w3schools.com/css/img_fjords.jpg';
        }

        try {
            if (copy($urlFile, $uploadfile)) {
                // add media in database
                $wp_filetype = wp_check_filetype($nameFile, null);
                $attachment = [
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => $nameFile,
                    'post_content' => '',
                    'post_status' => 'inherit'
                ];
                $attach_id = wp_insert_attachment($attachment, $uploadfile);
                $urlMediaWordpress = wp_get_attachment_url($attach_id);

                // add metadata
                $imagenew = get_post($attach_id);
                $fullsizepath = get_attached_file($imagenew->ID);
                $attach_data = wp_generate_attachment_metadata($attach_id, $fullsizepath);
                wp_update_attachment_metadata($attach_id, $attach_data);

                return $urlMediaWordpress;
            } else {
                return 'error-copy';
            }
        } catch (\Exception $e) {
            return 'error-copy';
        }
    }

    /**
     * Check if file is a valid media
     * @param $url
     * @return bool
     */
    public static function mazenIsFileMedia($url)
    {
        $infos = pathinfo($url);
        $extensionMediaAutorized = OptimizmeMazenUtils::mazenGetAuthorizedMediaExtension();
        if (is_array($infos) && isset($infos['extension']) && $infos['extension'] != '') {
            // extension found: is it authorized?
            if (in_array($infos['extension'], $extensionMediaAutorized)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public static function mazenGetAuthorizedMediaExtension()
    {
        $tabExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg', //Images
            'doc', 'docx', 'rtf', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ots', 'ott', 'odb', 'odg', 'otp', 'otg', 'odf', 'ods', 'odp' // files
        ];
        return $tabExtensions;
    }

    /**
     * @param $post
     * @return mixed
     */
    public static function mazenGetMetaTitle($post)
    {
        // get saved meta title
        $fieldMeta = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('metatitle');
        $metaTitle = get_post_meta($post->ID, $fieldMeta, true);

        // special
        if (defined('YOAST_ENVIRONMENT')) {
            $metaTitle = OptimizmeMazenPluginsInteraction::getMetaTitleByPlugin('yoast', $post, $metaTitle);
        } elseif (defined('AIOSEOP_VERSION')) {
            $metaTitle = OptimizmeMazenPluginsInteraction::getMetaTitleByPlugin('aiosp', $post, $metaTitle);
        } else {
            // no plugin
            if ($metaTitle == '') {
                $metaTitle = wp_get_document_title();
            }
        }

        return $metaTitle;
    }

    /**
     * Get meta description
     * @param $post
     * @return mixed
     */
    public static function mazenGetMetaDescription($post)
    {
        $fieldMeta = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('metadescription');
        $metaDescription = get_post_meta($post->ID, $fieldMeta, true);

        return $metaDescription;
    }

    /**
     * @param $newMetaValue
     * @param $idPost
     * @param $metaKey
     * @return bool
     */
    public static function mazenDoUpdatePostMeta($newMetaValue, $idPost, $metaKey)
    {
        $currentMetaDescription = get_post_meta($idPost, $metaKey, true);
        if ($currentMetaDescription == $newMetaValue) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get canonical url
     * @param string $post
     * @return string
     */
    public static function mazenGetCanonicalUrl($post = '')
    {
        if ($post == '') {
            global $post;
        }

        if (OptimizmeMazenPluginsInteraction::isCompatibleSeoPluginInstalled()) {
            $metaCanonical = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('canonical');
            $canonical = get_post_meta($post->ID, $metaCanonical, true);
        } else {
            // handle by Mazen SEO Connector
            $canonical = '';
            $canonical_override = '';

            //if (isset($post->ID) && $post->ID != '' && ($post->post_type == 'post' || $post->post_type == 'page')) {
            if (isset($post->ID) && $post->ID != '') {
                //$obj       = get_queried_object();
                $obj = get_post($post->ID);
                $canonical = get_permalink($obj->ID);

                // get canonical if defined
                $canonical_override = get_post_meta($post->ID, 'optimizme_canonical', true);
                if ($canonical_override == '') {
                    // Fix paginated pages canonical, but only if the page is truly paginated.
                    if (get_query_var('page') > 1) {
                        $num_pages = (substr_count($obj->post_content, '<!--nextpage-->') + 1);
                        if ($num_pages && get_query_var('page') <= $num_pages) {
                            if (!$GLOBALS['wp_rewrite']->using_permalinks()) {
                                $canonical = add_query_arg('page', get_query_var('page'), $canonical);
                            } else {
                                $canonical = user_trailingslashit(trailingslashit($canonical) . get_query_var('page'));
                            }
                        }
                    }
                }

                if ($canonical == '') {
                    $canonical = get_permalink($post->ID);
                }
            } else {
                if (is_search()) {
                    $search_query = get_search_query();

                    // Regex catches case when /search/page/N without search term is itself mistaken for search term. R.
                    if (!empty($search_query) && !preg_match('|^page/\d+$|', $search_query)) {
                        $canonical = get_search_link();
                    }
                } elseif (is_front_page()) {
                    $canonical = home_url();
                } elseif (is_tax() || is_tag() || is_category()) {
                    // TODO
                } elseif (is_post_type_archive()) {
                    $post_type = get_query_var('post_type');
                    if (is_array($post_type)) {
                        $post_type = reset($post_type);
                    }
                    $canonical = get_post_type_archive_link($post_type);
                } elseif (is_author()) {
                    $canonical = get_author_posts_url(get_query_var('author'), get_query_var('author_name'));
                } elseif (is_archive()) {
                    if (is_date()) {
                        if (is_day()) {
                            $canonical = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
                        } elseif (is_month()) {
                            $canonical = get_month_link(get_query_var('year'), get_query_var('monthnum'));
                        } elseif (is_year()) {
                            $canonical = get_year_link(get_query_var('year'));
                        }
                    }
                }
            }

            // defined canonical
            if ($canonical_override != '') {
                $canonical = $canonical_override;
            }
        }

        return $canonical;
    }

    /**
     * @param $post
     * @return mixed
     */
    public static function mazenGetMetaRobot($post, $type)
    {
        if (is_object($post)) {
            $keyMetaNoIndex = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType($type);
            $noIndexOrNoFollow = get_post_meta($post->ID, $keyMetaNoIndex, true);

            if ($noIndexOrNoFollow == '') {
                // nothing specified for this post: default setting defined?
                $noIndexOrNoFollow = OptimizmeMazenPluginsInteraction::getMetaRobotDefaultValue($post, $type);
            } else {
                // specified for this post: convert values according to other SEO plugins
                // all in one seo pack: on/off => 1/0
                // yoast: 2 if noindex by default and manually set to index
                if ($noIndexOrNoFollow == 'on') {
                    $noIndexOrNoFollow = 1;
                } elseif ($noIndexOrNoFollow == 'off' || $noIndexOrNoFollow == 2) {
                    $noIndexOrNoFollow = 0;
                }
            }
            return $noIndexOrNoFollow;
        }
    }

    /**
     * Get Dom from html
     *  and add a "<span>" tag in top
     * @param $doc
     * @param $tag
     * @param $content
     * @return DOMNodeList
     */
    public static function mazenGetNodesInDom($doc, $tag, $content)
    {
        // load post content in DOM
        libxml_use_internal_errors(true);
        $doc->loadHTML('<span>' . $content . '</span>');
        libxml_clear_errors();

        // get all images in post content
        $xp = new \DOMXPath($doc);
        $nodes = $xp->query('//' . $tag);
        return $nodes;
    }

    /**
     * Get HTML from dom document
     *  and remove "<span>" tag in top
     * @param $doc
     * @return string
     */
    public static function mazenGetHtmlFromDom($doc)
    {
        $racine = $doc->getElementsByTagName('span')->item(0);
        $newContent = '';
        if ($racine->hasChildNodes()) {
            foreach ($racine->childNodes as $node) {
                $newContent .= utf8_decode($doc->saveHTML($node));
            }
        }
        return $newContent;
    }

    /**
     * Clean content before saving
     * @param $content
     * @return mixed
     */
    public static function mazenCleanHtmlFromMazen($content)
    {
        $content = str_replace('style=""', '', $content);
        $content = str_replace('class=""', '', $content);

        return trim($content);
    }

    /**
     * @param $oldUrl
     * @param $newUrl
     */
    public static function mazenChangeAllLinksInPostContent($oldUrl, $newUrl)
    {
        global $wpdb;
        if ($oldUrl != '' && $newUrl != '') {
            $sql = 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_content LIKE "%' . $oldUrl . '%"';
            $posts = $wpdb->get_results($sql);
            if (is_array($posts) && !empty($posts)) {
                foreach ($posts as $postBoucle) {
                    $newContent = str_replace($oldUrl, $newUrl, $postBoucle->post_content);
                    $args = ['ID' => $postBoucle->ID, 'post_content' => $newContent];
                    $res = wp_update_post($args, true);
                }
            }
        }
    }

    /**
     * Send data to MAZEN with curl
     * @param $url
     * @param $data
     * @param $toJWT
     */
    public static function mazenSendDataWithCurl($url, $data, $toJWT)
    {
        // todo
    }

    /**
     * Is an object is visible or not
     * @param string $statut
     * @return int value
     */
    public static function mazenGetStatutBinary($statut)
    {
        if ($statut == 'publish') {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @param $login
     * @param $password
     * @return bool
     */
    public static function checkCredentials($login, $password)
    {
        // resert filters for authentification
        remove_all_filters('authenticate');
        add_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
        add_filter('authenticate', 'wp_authenticate_email_password', 20, 3);
        add_filter('authenticate', 'wp_authenticate_spam_check', 99);

        if ($login == '' || $password == '') {
            return false;
        }

        // try login
        $creds = [
            'user_login' => $login,
            'user_password' => $password,
            'remember' => false
        ];
        $user = wp_signon($creds, false);

        if (isset($user->ID) && is_numeric($user->ID)) {
            return true;
        } else {
            return false;
        }
    }
}
