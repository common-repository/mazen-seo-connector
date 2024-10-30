<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenAutoUpdator
 * @package Optimizme\Mazen
 */
class OptimizmeMazenAutoUpdator
{
    /**
     * @param $update
     * @param $item
     * @return bool
     */
    public static function mazenAutomaticPluginUpdate($update, $item)
    {
        // array of plugin slugs to always auto-update
        $plugins = ['mazen-seo-connector'];
        if (in_array($item->slug, $plugins)) {
            return true; // always update plugins in this array
        } else {
            return $update; // else, use the normal API response to decide whether to update or not
        }
    }
}
