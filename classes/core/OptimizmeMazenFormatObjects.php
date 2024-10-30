<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenFormatObjects
 * @package Optimizme\Mazen
 */
class OptimizmeMazenFormatObjects
{
    /**
     * @param $postLoad
     * @param int $loadAll
     * @param array $fieldsFilter
     * @param array $linksSearched: links requested by Mazen
     * @return array
     */
    public static function loadPostForMazen($postLoad, $loadAll = 0, $fieldsFilter = [], $linksSearched = [])
    {
        // minimum viable
        $urlPost = get_permalink($postLoad->ID);
        if (is_array($linksSearched) && !empty($linksSearched)) {
            foreach ($linksSearched as $link) {
                if ($link .'/' == $urlPost)
                    $urlPost = $link;
            }
        }

        $tabPost =  [
            'id' => $postLoad->ID,
            'title' => OptimizmeMazenUtils::safeUtf8Encode($postLoad->post_title),
            'url' => $urlPost,
            'publish' => OptimizmeMazenUtils::mazenGetStatutBinary($postLoad->post_status),
            'post_type' => $postLoad->post_type
        ];

        $mazenDom = new OptimizmeMazenDomParsing();

        if ($loadAll == 1 || !empty($fieldsFilter)) {
            // add non required fields
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('content', $fieldsFilter))) {
                $tabPost['content'] = OptimizmeMazenUtils::safeUtf8Encode($postLoad->post_content);
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('short_description', $fieldsFilter))) {
                $tabPost['short_description'] = OptimizmeMazenUtils::safeUtf8Encode($postLoad->post_excerpt);
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('slug', $fieldsFilter))) {
                $tabPost['slug'] = $postLoad->post_name;
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('meta_title', $fieldsFilter))) {
                $tabPost['meta_title'] = OptimizmeMazenUtils::mazenGetMetaTitle($postLoad);
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('meta_description', $fieldsFilter))) {
                $tabPost['meta_description'] = OptimizmeMazenUtils::mazenGetMetaDescription($postLoad);
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('url_canonical', $fieldsFilter))) {
                $tabPost['url_canonical'] = OptimizmeMazenUtils::mazenGetCanonicalUrl($postLoad);
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('noindex', $fieldsFilter))) {
                $tabPost['noindex'] = OptimizmeMazenUtils::mazenGetMetaRobot($postLoad, 'noindex');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('nofollow', $fieldsFilter))) {
                $tabPost['nofollow'] = OptimizmeMazenUtils::mazenGetMetaRobot($postLoad, 'nofollow');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('a', $fieldsFilter))) {
                $tabPost['a'] = $mazenDom->extractValuesFromNodes($postLoad, 'a');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('img', $fieldsFilter))) {
                $tabPost['img'] = $mazenDom->extractValuesFromNodes($postLoad, 'img');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('h1', $fieldsFilter))) {
                $tabPost['h1'] = $mazenDom->extractValuesFromNodes($postLoad, 'h1');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('h2', $fieldsFilter))) {
                $tabPost['h2'] = $mazenDom->extractValuesFromNodes($postLoad, 'h2');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('h3', $fieldsFilter))) {
                $tabPost['h3'] = $mazenDom->extractValuesFromNodes($postLoad, 'h3');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('h4', $fieldsFilter))) {
                $tabPost['h4'] = $mazenDom->extractValuesFromNodes($postLoad, 'h4');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('h5', $fieldsFilter))) {
                $tabPost['h5'] = $mazenDom->extractValuesFromNodes($postLoad, 'h5');
            }
            if ($loadAll == 1 || (!empty($fieldsFilter) && in_array('h6', $fieldsFilter))) {
                $tabPost['h6'] = $mazenDom->extractValuesFromNodes($postLoad, 'h6');
            }
        }

        return $tabPost;
    }

    /**
     * @param $category
     * @return array
     */
    public static function loadCategoryForMazen($category)
    {
        $categoryInfos = [
            'id' => $category->term_id,
            'name' => $category->name,
            'description' => $category->description,
            'slug' => $category->slug,
            'parent' => $category->parent
        ];
        return $categoryInfos;
    }
}
