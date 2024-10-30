<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenHooks
 * @package Optimizme\Mazen
 */
class OptimizmeMazenHooks
{
    /**
     * Send data back to MAZEN when a post is saved
     * @param $post_id
     */
    public static function mazenHookOnSavePost($post_id)
    {
        //OptimizmeMazenHooks::mazenPrepareInformationsSendToMazen($post_id, 'saved_post');
    }

    /**
     * On deleted post in Wordpress
     * @param $post_id
     */
    public static function mazenHookOnDeletePost($post_id)
    {
        //OptimizmeMazenHooks::mazenPrepareInformationsSendToMazen($post_id, 'deleted_post');
    }

    /**
     * Prepare informations to send
     * @param $post_id
     * @param $action
     */
    public static function mazenPrepareInformationsSendToMazen($post_id, $action)
    {
        if (defined('OPTIMIZME_MAZEN_JWT_SECRET') && OPTIMIZME_MAZEN_JWT_SECRET != '') {
            // If this is just a revision, don't do anything
            if (wp_is_post_revision($post_id)) {
                return;
            }

            // load post
            $postReturn = get_post($post_id);
            $postUrl = get_permalink($postReturn->ID);

            // removed "__trashed" if in trash
            if (strstr($postReturn->post_name, '__trashed')) {
                $postReturn->post_name = str_replace('__trashed', '', $postReturn->post_name);
                $postUrl = str_replace('__trashed', '', $postUrl);
            }

            // send informations to MAZEN about changes happened
            $data = [
                'data_optme' => [
                    'url' => $postUrl,
                    'action' => $action,
                    'title' => $postReturn->post_title,
                    'slug' => $postReturn->post_name,
                    'content' => $postReturn->post_content,
                    'publish' => OptimizmeMazenUtils::mazenGetStatutBinary($postReturn->post_status)
                ]
            ];

            // send data about modified post, using JWT
            //OptimizmeMazenUtils::mazenSendDataWithCurl(OPTIMIZME_MAZEN_URL_HOOK, $data, 1);
        }
    }
}
