<?php

namespace Optimizme\Mazen;

use Sunra\PhpSimple\HtmlDomParser;

/**
 * Class OptimizmeMazenActions
 * @package Optimizme\Mazen
 */
class OptimizmeMazenActions
{
    public $returnResult;
    public $tabErrors;
    public $returnAjax;
    public $returnCode;

    /**
     * OptimizmeMazenActions constructor.
     */
    public function __construct()
    {
        $this->returnResult = [];
        $this->tabErrors = [];
        $this->returnAjax = [];
        $this->returnCode = 200;
    }

    ////////////////////////////////////////////////
    //              POSTS
    ////////////////////////////////////////////////

    /**
     * Création d'un post
     * @param $objData
     */
    public function mazenCreatePost($objData)
    {
        $flagError = 0;
        if (!isset($objData->post_type) || $objData->post_type == '') {
            // need more data
            array_push($this->tabErrors, __('No post type defined (post/page)', 'mazen-seo-connector'));
            $flagError = 1;
        }

        if (!isset($objData->title) || $objData->title == '') {
            // need more data
            array_push($this->tabErrors, __('Please enter a title', 'mazen-seo-connector'));
            $flagError = 1;
        }

        if ($flagError == 0) {
            if (isset($objData->publish) && is_numeric($objData->publish) && $objData->publish == 1) {
                $status = 'publish';
            } else {
                $status = 'draft';
            }

            $args = [
                'post_type' => $objData->post_type,
                'post_title' => $objData->title,
                'post_status' => $status
            ];

            if (isset($objData->parent) && $objData->parent != '') {
                $args['post_parent'] = $objData->parent;
            }
            if (isset($objData->content) && $objData->content != '') {
                $args['post_content'] = $objData->content;
            }

            $idPostCreate = wp_insert_post($args);
            if ($idPostCreate) {
                $permalink = get_permalink($idPostCreate);

                // load and return post data
                $this->returnAjax = [
                    'id' => $idPostCreate,
                    'permalink' => $permalink,
                    'message' => __('Element has been created', 'mazen-seo-connector')
                ];
            } else {
                // error creation
                array_push($this->tabErrors, __('Error creating post', 'mazen-seo-connector'));
            }
        }
    }

    /**
     * Load posts/pages
     * @param $data
     */
    public function mazenLoadPostsPages($data)
    {
        $tabResults = [];
        $tabExcludePostType = ['attachment', 'revision', 'nav_menu_items'];

        // FILTERS
        $tabIdIn = [];
        if (isset($data->links)) {
            if (!is_array($data->links)) {
                $data->links = [];
            }
            foreach ($data->links as $link) {
                $idFromUrl = url_to_postid($link);
                if (is_numeric($idFromUrl) && $idFromUrl > 0) {
                    array_push($tabIdIn, $idFromUrl);
                } else {
                    $this->returnAjax['link_error'][] = $link;
                }
            }
            if (empty($tabIdIn)) {
                $tabIdIn = [0]; // force non existing post, prevent loading all posts
            }
        } else {
            $data->links = [];
        }

        if (isset($data->post_type) && $data->post_type != '') {
            $postTypes = [$data->post_type];
        } else {
            $postTypes = ['any'];
        }

        if (isset($data->offset) && is_numeric($data->offset)) {
            $offset = $data->offset;
        } else {
            $offset = 0;
        }

        if (isset($data->max_number) && is_numeric($data->max_number)) {
            $maxNumber = $data->max_number;
        } else {
            $maxNumber = -1;
        }

        if (isset($data->fields) && is_array($data->fields) && !empty($data->fields)) {
            $fieldsFilter = $data->fields;
        } else {
            $fieldsFilter = [];
        }

        // load
        if (is_array($postTypes) && !empty($postTypes)) {
            foreach ($postTypes as $posttype) {
                $args = [
                    'posts_per_page' => $maxNumber,
                    'post_type' => $posttype,
                    'post_status' => 'any',
                    'offset' => $offset,
                    'include' => $tabIdIn
                ];
                $posts = get_posts($args);

                if (is_array($posts) && !empty($posts)) {
                    $productsReturn = [];
                    foreach ($posts as $postLoop) {
                        if (!in_array($postLoop->post_type, $tabExcludePostType)) {
                            $prodReturn = OptimizmeMazenFormatObjects::loadPostForMazen($postLoop, 0, $fieldsFilter, $data->links);
                            array_push($productsReturn, $prodReturn);
                        }
                    }
                    $tabResults['posts'] = $productsReturn;
                }
            }
        }

        $this->returnAjax['arborescence'] = $tabResults;
    }

    /**
     * Return content from a post
     * @param $idPost
     */
    public function mazenLoadPostContent($idPost)
    {
        $post = get_post($idPost);
        if (isset($post->ID) && $post->ID != '') {
            // load and return post data
            $postLoad = OptimizmeMazenFormatObjects::loadPostForMazen($post, 1);
            $this->returnAjax['post'] = $postLoad;
        } else {
            array_push($this->tabErrors, __('No post with this ID found', 'mazen-seo-connector'));
        }
    }

    /**
     * @param $idPost
     * @param $value
     */
    public function mazenSetTitle($idPost, $value)
    {
        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID found', 'mazen-seo-connector'));
        } elseif (!isset($value) || $value == '') {
            // need more data
            array_push($this->tabErrors, __('Please enter a title', 'mazen-seo-connector'));
        } else {
            // Update the post into the database
            $obj = [
                'ID' => $idPost,
                'post_title' => $value
            ];

            $id_update = wp_update_post($obj, true);
            $this->mazenLogWpObjectErrors($id_update);
        }
    }

    /**
     * @param $idPost
     * @param $value
     */
    public function mazenSetContent($idPost, $value)
    {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $flagCopyMedia = 1;

        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID found', 'mazen-seo-connector'));
        } elseif (!isset($value) || $value == '') {
            // need more data
            array_push($this->tabErrors, __('Please enter a new content', 'mazen-seo-connector'));
        } else {
            if ($flagCopyMedia == 1) {
                // tags to parse and attributes to transform
                $tabParseScript = [
                    'img' => 'src',
                    'a' => 'href',
                    'video' => 'src',
                    'source' => 'src'
                ];

                if (!class_exists("DOMDocument")) {
                    $newContent = $value;
                } else {
                    // copy media files to wordpress media library
                    $doc = new \DOMDocument;
                    libxml_use_internal_errors(true);
                    $doc->loadHTML('<span>' . $value . '</span>');
                    libxml_clear_errors();

                    // get all images in post content
                    $xp = new \DOMXPath($doc);

                    foreach ($tabParseScript as $tag => $attr) {
                        foreach ($xp->query('//' . $tag) as $node) {
                            // copy url media
                            $urlFile = $node->getAttribute($attr);

                            // check if already in media library
                            if (OptimizmeMazenUtils::mazenIsFileMedia($urlFile)) {
                                // media
                                $urlMediaWordpress = OptimizmeMazenUtils::mazenIsMediaInLibrary($urlFile);
                                if (!$urlMediaWordpress) {
                                    // not in media library: add
                                    $urlMediaWordpress = OptimizmeMazenUtils::mazenAddMediaInLibrary($urlFile);
                                }

                                // now, media should be in media library
                                if ($urlMediaWordpress != 'error-copy') {
                                    $node->setAttribute($attr, $urlMediaWordpress);
                                } else {
                                    array_push($this->tabErrors, __('Error copying image in CMS, error-copy returned', 'mazen-seo-connector'));
                                }
                            }
                        }
                    }

                    // span racine to remove
                    $newContent = OptimizmeMazenUtils::mazenGetHtmlFromDom($doc);
                    $newContent = OptimizmeMazenUtils::mazenCleanHtmlFromMazen($newContent);
                }
            }

            // save content in post/page
            $newContent = OptimizmeMazenUtils::safeUtf8Encode($newContent);
            $obj = [
                'ID' => $idPost,
                'post_content' => $newContent
            ];
            $id_update = wp_update_post($obj, true);
            $this->mazenLogWpObjectErrors($id_update);

            if (count($this->tabErrors) == 0) {
                $this->returnAjax = [
                    'message' => __('Content successfully saved', 'mazen-seo-connector'),
                    'id' => $idPost,
                    'content' => $newContent
                ];
            }
        }
    }

    /**
     * @param $idPost
     * @param $values
     * @param $tag
     * @param string $attr
     */
    public function mazenChangeSomeContentInTag($idPost, $values, $tag, $attr = '')
    {
        /* @var $node \DOMElement */
        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID found', 'mazen-seo-connector'));
        } else {
            $post = get_post($idPost);
            if (isset($post->ID) && $post->ID != '') {
                $this->returnAjax['success'] = [];
                $this->returnAjax['error'] = [];

                if (!is_array($values)) {
                    $values = [$values];
                }

                // content parsing
                $mazenDom = new OptimizmeMazenDomParsing();
                $mazenDom->getNodesInContent($post->post_content, $tag);
                $mazenDom->changeNodesValues($values, $attr);
                $newContent = $mazenDom->getHtml();

                /// CHANGE CONTENT IN SHORTCODES
                $newContentShortcode = OptimizmeMazenPluginsShortcode::setShortcodesFromContent($mazenDom, $values, $newContent, $tag);
                if ($newContentShortcode != '') {
                    $newContent = $newContentShortcode;
                }

                if ($mazenDom->boolSave == 1) {
                    $newContent = OptimizmeMazenUtils::safeUtf8Encode($newContent);

                    // SAVE FOR PAGE BUILDERS
                    $data = [
                        'content' => $newContent,
                        'values' => $values,
                        'tag' => $tag,
                        'attr' => $attr,
                    ];
                    $boolSaveWP = OptimizmeMazenPluginsInteraction::saveInThirdPartyPageBuilder($post, $data);

                    if ($boolSaveWP) {
                        // save in Wordpress post_content
                        $obj = [
                            'ID' => $idPost,
                            'post_content' => $newContent
                        ];
                        $id_update = wp_update_post($obj, true);
                        $this->mazenLogWpObjectErrors($id_update);
                    }

                    // return messages
                    if (is_array($mazenDom->tabSuccess) && !empty($mazenDom->tabSuccess)) {
                        foreach ($mazenDom->tabSuccess as $success) {
                            array_push($this->returnAjax['success'], $success);
                        }
                    }

                    // errors
                    if (is_array($mazenDom->tabError) && !empty($mazenDom->tabError)) {
                        foreach ($mazenDom->tabError as $error) {
                            array_push($this->returnAjax['error'], $error);
                        }
                    }

                    // values not changed
                    if (count($this->returnAjax['success']) < count($values)) {
                        if (!is_array($this->returnAjax['error']))
                            $this->returnAjax['error'] = [];

                        // we send more values than updated count
                        $nbValuesLeft = count($values) - count($this->returnAjax['success']);
                        $valuesLeft = array_slice($values, count($this->returnAjax['success']), $nbValuesLeft);
                        $this->returnAjax['error'] = array_merge($this->returnAjax['error'], $valuesLeft);
                    }
                } else {
                    // nothing done
                    array_push($this->tabErrors, __('Parsing content: No corresponding node found in content.', 'mazen-seo-connector'));
                }
            } else {
                array_push($this->tabErrors, __('No post with this ID found', 'mazen-seo-connector'));
            }
        }
    }

    /**
     * @param $idPost
     * @param $value
     */
    public function mazenSetMetaTitle($idPost, $value)
    {
        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID found', 'mazen-seo-connector'));
        } elseif (!isset($value)) {
            // need more data
            array_push($this->tabErrors, __('Please enter a Meta title', 'mazen-seo-connector'));
        } else {
            // get postmeta field
            $metaKey = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('metatitle');

            if (OptimizmeMazenUtils::mazenDoUpdatePostMeta($value, $idPost, $metaKey)) {
                $resUpdate = update_post_meta($idPost, $metaKey, $value);
                if ($resUpdate == false) {
                    array_push($this->tabErrors, __('Error saving Meta Title : ' . $metaKey, 'mazen-seo-connector'));
                }
            }
        }
    }

    /**
     * @param $idPost
     * @param $value
     */
    public function mazenSetMetaDescription($idPost, $value)
    {
        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID found', 'mazen-seo-connector'));
        } elseif (!isset($value)) {
            // need more data
            array_push($this->tabErrors, __('Please enter a Meta description', 'mazen-seo-connector'));
        } else {
            // get postmeta field
            $metaKey = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('metadescription');

            if (OptimizmeMazenUtils::mazenDoUpdatePostMeta($value, $idPost, $metaKey)) {
                $resUpdate = update_post_meta($idPost, $metaKey, $value);
                if ($resUpdate == false) {
                    array_push($this->tabErrors, __('Error saving Meta Description : ' . $metaKey, 'mazen-seo-connector'));
                }
            }
        }
    }

    /**
     * @param $idPost
     * @param $value
     */
    public function mazenSetCanonicalUrl($idPost, $value)
    {
        $flagError = 0;
        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID found', 'mazen-seo-connector'));
        } else {
            $urlCanonical = esc_url_raw($value, ['http', 'https']);

            $metaKey = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('canonical');
            if (OptimizmeMazenUtils::mazenDoUpdatePostMeta($value, $idPost, $metaKey)) {
                $resUpdate = update_post_meta($idPost, $metaKey, $value);
                if ($resUpdate == false) {
                    $flagError = 1;
                }
            }

            if ($flagError == 0) {
                $this->returnAjax = [
                    'id' => $idPost,
                    'canonical' => $urlCanonical
                ];
            } else {
                array_push($this->tabErrors, __('Error saving canonical URL for SEO Plugin', 'mazen-seo-connector'));
            }
        }
    }

    /**
     * @param $idPost
     * @param $type
     * @param $value
     */
    public function mazenSetMetaRobot($idPost, $type, $value)
    {
        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID found', 'mazen-seo-connector'));
        } elseif ($type == '') {
            array_push($this->tabErrors, __('No type for meta robots', 'mazen-seo-connector'));
        } else {
            $post = get_post($idPost);
            if (isset($post->ID) && $post->ID != '') {
                if ($type == 'noindex') {
                    $keyMeta = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('noindex');
                } else {
                    $keyMeta = OptimizmeMazenPluginsInteraction::mazenGetPostMetaKeyFromType('nofollow');
                }

                // update index/noindex
                if (!isset($value) || $value == 0) {
                    delete_post_meta($idPost, $keyMeta);
                } else {
                    $saveValue = OptimizmeMazenPluginsInteraction::formatMetaRobotBeforeSave($keyMeta);
                    update_post_meta($idPost, $keyMeta, $saveValue);
                }
            } else {
                array_push($this->tabErrors, __('Error loading post for setMetaRobots', 'mazen-seo-connector'));
            }
        }
    }

    /**
     * @param $idPost
     * @param $value
     */
    public function mazenSetPostStatus($idPost, $value)
    {
        if (!isset($idPost) || $idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID found', 'mazen-seo-connector'));
        } else {
            $post = get_post($idPost);

            if (isset($post->ID) && $post->ID != '') {
                if (!isset($value) || $value == 0) {
                    $postStatus = 'draft';
                } else {
                    $postStatus = 'publish';
                }

                // Update the post into the database
                $obj = [
                    'ID' => $idPost,
                    'post_status' => $postStatus
                ];
                $id_update = wp_update_post($obj, true);
                $this->mazenLogWpObjectErrors($id_update);
            } else {
                array_push($this->tabErrors, __('Error loading post for setPostStatus', 'mazen-seo-connector'));
            }
        }
    }

    /**
     * Change permalink of a post
     * and add a redirection
     * @param $idPost
     * @param $value
     */
    public function mazenSetSlug($idPost, $value)
    {
        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('Please enter valid ID', 'mazen-seo-connector'));
        } elseif (!isset($value) || $value == '') {
            // need more data
            array_push($this->tabErrors, __('Please enter slug', 'mazen-seo-connector'));
        } else {
            // get "current" URL
            $previousURL = get_permalink($idPost);

            // Update the post into the database
            $obj = [
                'ID' => $idPost,
                'post_name' => $value
            ];
            $id_update = wp_update_post($obj, true);
            $this->mazenLogWpObjectErrors($id_update);

            // get "new" URL
            $newURL = get_permalink($id_update);

            $post = get_post($idPost);

            if ($previousURL == $newURL) {
                // no changes, but ok
                $this->returnAjax['message'] = __('Slug is identical', 'mazen-seo-connector');
                $this->returnAjax['url'] = $newURL;
                $this->returnAjax['new_slug'] = $post->post_name;
            } else {
                // if no error: add redirect
                if (!$this->mazenHasErrors()) {
                    // add redirection for hierarchical post type
                    // non hierarchical is already catched with the postmeta "_wp_old_slug"


                    if (is_post_type_hierarchical($post->post_type)) {
                        // add redirection from old url to new url
                        $objRedirect = new OptimizmeMazenRedirections();
                        $resRedirection = $objRedirect->mazenAddRedirection($previousURL, $newURL);

                        switch ($resRedirection) {
                            case 'insert':
                                $this->returnAjax['message'] = __('Redirect added: ' . $newURL, 'mazen-seo-connector');
                                break;
                            case 'update':
                                $this->returnAjax['message'] = __('Redirect updated : ' . $newURL, 'mazen-seo-connector');
                                break;
                            case 'same':
                                $this->returnAjax['message'] = __('New URL : ' . $newURL, 'mazen-seo-connector');
                                break;
                        }
                    } else {
                        $this->returnAjax['message'] = __('Slug has been successfully changed', 'mazen-seo-connector');
                    }

                    $this->returnAjax['url'] = $newURL;
                    $this->returnAjax['message'] = __('Slug has been successfully changed', 'mazen-seo-connector');
                    $this->returnAjax['new_slug'] = $post->post_name;
                }
            }
        }
    }

    /**
     * Change post excerpt
     * @param $idPost
     * @param $value
     */
    public function mazenSetShortDescription($idPost, $value)
    {
        if ($idPost == '' || !is_numeric($idPost)) {
            // need more data
            array_push($this->tabErrors, __('Please enter valid ID', 'mazen-seo-connector'));
        } else {
            $obj = [
                'ID' => $idPost,
                'post_excerpt' => strip_tags($value)
            ];

            // Update the post into the database
            $id_update = wp_update_post($obj, true);
            $this->mazenLogWpObjectErrors($id_update);
        }
    }

    /**
     * @param $data
     */
    public function mazenIsDataUpdatable($data, $type)
    {
        $valueFounds = [];
        $content = '';

        if (!isset($data->tag) || $data->tag == '') {
            array_push($this->tabErrors, __('Need tag in updatable post', 'mazen-seo-connector'));
        } elseif (!isset($data->value) || !is_array($data->value) || empty($data->value)) {
            array_push($this->tabErrors, __('Need values to check in updatable post', 'mazen-seo-connector'));
        } else {
            if ($type == 'post') {
                $post = get_post($data->id);
                if (isset($post->ID) && $post->ID != '') {
                    $content = $post->post_content;
                }
            } elseif ($type == 'category') {
                $category = get_category($data->id);
                if (isset($category->term_id) && $category->term_id != '') {
                    $content = $category->description;
                }
            }

            if ($content != '') {
                if (class_exists('DOMDocument')) {
                    // WITH DOMDocument
                    $doc = new \DOMDocument;
                    $nodes = OptimizmeMazenUtils::mazenGetNodesInDom($doc, $data->tag, $content);
                    if ($nodes->length > 0) {
                        foreach ($data->value as $search) {
                            $flagFound = 0;
                            foreach ($nodes as $node) {
                                if (isset($data->attribute) && $data->attribute != '') {
                                    // search in node attributes
                                    if ($node->getAttribute($data->attribute) == $search) {
                                        $flagFound = 1;
                                    }
                                } else {
                                    // search in node value
                                    if ($node->nodeValue == $search) {
                                        $flagFound = 1;
                                    }
                                }
                            }
                            if ($flagFound == 1) {
                                array_push($valueFounds, $search);
                            }
                        }
                    }
                } else {
                    // WITHOUT DOMDocument: Simple HTML DOM Parser
                    $dom = HtmlDomParser::str_get_html($content);

                    foreach ($data->value as $search) {
                        $flagFound = 0;

                        foreach ($dom->find($data->tag) as $node) {
                            if (isset($data->attribute) && $data->attribute != '') {
                                $attr = $data->attribute;
                            } else {
                                $attr = '';
                            }
                            $value = OptimizmeMazenDomParsing::getValueFromSHDPTag($node, $attr);
                            if ($value == $search) {
                                $flagFound = 1;
                            }
                        }

                        if ($flagFound == 1) {
                            array_push($valueFounds, $search);
                        }
                    }
                }

                $this->returnAjax['editable'] = $valueFounds;
            } else {
                array_push($this->returnAjax['error'], __('Error loading post with id ' . $data->id, 'mazen-seo-connector'));
            }
        }
    }

    ////////////////////////////////////////////////
    //              CATEGORIES
    ////////////////////////////////////////////////

    /**
     * Load categories (from taxonomy category)
     */
    public function mazenLoadCategories()
    {
        $tabResults = [];
        $categories = get_terms('category', ['hide_empty' => false]);

        if (is_array($categories) && !empty($categories)) {
            foreach ($categories as $categoryLoop) {
                $categoryInfos = OptimizmeMazenFormatObjects::loadCategoryForMazen($categoryLoop);
                array_push($tabResults, $categoryInfos);
            }
        }

        $this->returnAjax['categories'] = $tabResults;
    }

    /**
     * @param $elementId
     */
    public function mazenLoadCategoryContent($elementId)
    {
        if ($elementId == '' || !is_numeric($elementId)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID sent', 'mazen-seo-connector'));
        } else {
            $category = get_category($elementId);
            $this->mazenLogWpObjectErrors($category);

            if (isset($category->term_id) && $category->term_id != '') {
                $tabCategory = OptimizmeMazenFormatObjects::loadCategoryForMazen($category);
            } else {
                $tabCategory = [];
            }

            $this->returnAjax = [
                'message' => __('Category loaded', 'mazen-seo-connector'),
                'category' => $tabCategory
            ];
        }
    }

    /**
     * @param $idCategory
     * @param $value
     */
    public function mazenUpdateCategory($idCategory, $value, $field)
    {
        if ($idCategory == '' || !is_numeric($idCategory)) {
            // need more data
            array_push($this->tabErrors, __('No valid ID sent', 'mazen-seo-connector'));
        } elseif ($field == '') {
            array_push($this->tabErrors, __('Please specify a field to update this category', 'mazen-seo-connector'));
        } else {
            $args = [$field => $value];
            $resCatUpdate = wp_update_term($idCategory, 'category', $args);
            $this->mazenLogWpObjectErrors($resCatUpdate);
        }
    }

    /**
     * @param $idCategory
     * @param $value
     */
    public function mazenSetCategorySlug($idCategory, $value)
    {
        if ($idCategory == '' || !is_numeric($idCategory)) {
            array_push($this->tabErrors, __('Category not found.', 'mazen-seo-connector'));
        } elseif (!isset($value) || $value == '') {
            // need more data
            array_push($this->tabErrors, __('Please enter the category slug', 'mazen-seo-connector'));
        } else {
            $categoryInit = get_category($idCategory);

            // get "current" URL
            $previousURL = get_category_link($categoryInit);

            // Update the category into the database
            $args = ['slug' => $value];
            $resCatUpdate = wp_update_term($idCategory, 'category', $args);
            $this->mazenLogWpObjectErrors($resCatUpdate);

            if (count($this->tabErrors) == 0) {
                // update OK: get "new" URL
                $categoryUpdated = get_category($resCatUpdate['term_id']);
                $newURL = get_category_link($categoryUpdated);
                if ($previousURL == $newURL) {
                    array_push($this->tabErrors, __("URL are the same, no changes.", 'mazen-seo-connector'));
                } else {
                    // add redirection from old url to new url
                    $objRedirect = new OptimizmeMazenRedirections();
                    $objRedirect->mazenAddRedirection($previousURL, $newURL);

                    // return informations
                    $this->returnAjax = [
                        'message' => 'URL changed',
                        'url' => get_category_link($categoryUpdated),
                        'new_slug' => $categoryUpdated->slug
                    ];
                }
            }
        }
    }

    /**
     * Add a new term for a given taxonomy
     * @param $objData
     * @param string $taxonomy
     */
    public function mazenCreateCategory($objData, $taxonomy = 'category')
    {
        if (!isset($objData->title) || $objData->title == '') {
            // need more data
            array_push($this->tabErrors, __('Please enter a title', 'mazen-seo-connector'));
        } else {
            if (!isset($objData->parent) || $objData->parent == '') {
                $objData->parent = 0;
            }
            if (!isset($objData->description) || $objData->description == '') {
                $objData->description = '';
            }

            $args = [
                'parent' => $objData->parent,
                'description' => $objData->description
            ];
            $resInsert = wp_insert_term($objData->title, $taxonomy, $args);

            if (is_wp_error($resInsert)) {
                $this->mazenLogWpObjectErrors($resInsert);
            } else {
                $this->returnAjax = [
                    'id' => $resInsert['term_id'],
                    'permalink' => get_category_link($resInsert['term_id']),
                    'message' => __('Category has been created', 'mazen-seo-connector')
                ];
            }
        }
    }

    ////////////////////////////////////////////////
    //              REDIRECTIONS
    ////////////////////////////////////////////////

    /**
     * load list of redirections
     */
    public function mazenLoadRedirections()
    {
        $objRedirection = new OptimizmeMazenRedirections();
        $this->returnAjax['redirections'] = $objRedirection->mazenGetAllRedirections('all');
    }

    /**
     * Enable or disable a redirection
     * @param $idRedirection
     * @param int $publish
     */
    public function mazenEnableDisableRedirection($idRedirection, $publish = 1)
    {
        if (!isset($idRedirection) || $idRedirection == '') {
            // need more data
            array_push($this->tabErrors, __('Redirect not found', 'mazen-seo-connector'));
        } else {
            $redirection = new OptimizmeMazenRedirections();
            if ($publish == 0) {
                $redirection->mazenUpdateRedirection($idRedirection, 1);
            } else {
                $redirection->mazenUpdateRedirection($idRedirection, 0);
            }
        }
    }

    /**
     * @param $idRedirection
     * @param $field
     * @param $value
     */
    public function mazenUpdateRedirection($idRedirection, $field, $value)
    {
        if (!isset($idRedirection) || $idRedirection == '') {
            // need more data
            array_push($this->tabErrors, __('Id not set for update redirection', 'mazen-seo-connector'));
        } else {
            $redirection = new OptimizmeMazenRedirections();
            $redirection->mazenEditRedirection($idRedirection, $field, $value);
        }
    }

    /**
     * @param $objData
     */
    public function mazenDeleteRedirection($objData)
    {
        if (!isset($objData->id) || $objData->id == '') {
            // need more data
            array_push($this->tabErrors, __('Id not set for delete', 'mazen-seo-connector'));
        } else {
            $redirection = new OptimizmeMazenRedirections();
            $redirection->mazenDeleteRedirection($objData->id);
        }
    }

    ////////////////////////////////////////////////
    //              SITE
    ////////////////////////////////////////////////

    /**
     * @param $value
     */
    public function mazenSetBlogPublicOrPrivate($value)
    {
        if (!isset($value)) {
            $valueBlogPublic = 0;
        } else {
            $valueBlogPublic = $value;
        }
        update_option('blog_public', $valueBlogPublic);

        $this->returnAjax = [
            'blog_public' => $valueBlogPublic,
            'message' => __('Information has been saved.', 'mazen-seo-connector')
        ];
    }

    /**
     * Load options from site
     */
    public function mazenLoadSiteOptions()
    {
        if (get_option('blogname') != '') {
            $this->returnAjax = [
                'site' => [
                    'title' => get_option('blogname'),
                    'description' => get_option('blogdescription'),
                    'public' => (int)get_option('blog_public')
                ]
            ];
        } else {
            // need more data
            array_push($this->tabErrors, __('No data found', 'mazen-seo-connector'));
        }
    }

    /**
     * Set blog name
     * @param $value
     */
    public function mazenSetBlogTitle($value)
    {
        if (!isset($value) || $value == '') {
            // need more data
            array_push($this->tabErrors, __('Please enter the site title', 'mazen-seo-connector'));
        } else {
            update_option('blogname', $value);
        }
    }

    /**
     * Set blog name
     * @param $value
     */
    public function mazenSetBlogDescription($value)
    {
        update_option('blogdescription', $value);
    }

    /**
     * Get secret key for JSON Web Signature
     * @param $objData
     */
    public function mazenRegisterCMS($objData)
    {
        if (OptimizmeMazenUtils::checkCredentials($objData->login, $objData->password)) {
            // auth ok! we can generate token
            $keyJWT = OptimizmeMazenJwt::mazenGenerateKeyForJwt();


            if (isset($keyJWT) && is_array($keyJWT)) {
                // all is ok
                $this->returnAjax = [
                    'message' => __('JSON Token generated in Wordpress', 'mazen-seo-connector'),
                    'jws_token' => $keyJWT['token'],
                    'id_client' => $keyJWT['id_client'],
                    'cms' => 'wordpress',
                    'site_domain' => home_url(),
                    'jwt_disable' => 1
                ];
            } else {
                array_push($this->tabErrors, __('Error generating JSON Token', 'mazen-seo-connector'));
            }
        } else {
            // error login user
            $this->setReturnCode(403);
            array_push($this->tabErrors, __('Invalid credentials', 'mazen-seo-connector'));
        }
    }

    /**
     * @param $data
     */
    public function mazenCheckCredentials($data)
    {
        if (isset($data) && is_object($data) && isset($data->login) && $data->login != '' && isset($data->password) && $data->password != '') {
            if (OptimizmeMazenUtils::checkCredentials($data->login, $data->password)) {
                $isValid = 1;
            } else {
                $isValid = 0;
            }

            $this->returnAjax = ['is_valid' => $isValid];
        } else {
            array_push($this->tabErrors, __('Need more informations for credentials check', 'mazen-seo-connector'));
        }
    }

    /**
     * Get plugin version
     */
    public function mazenGetPluginVersion()
    {
        $this->returnAjax['version'] = OPTIMIZME_MAZEN_VERSION;
    }

    /**
     * Get boolean if DOMDocument support
     */
    public function mazenGetDomDocumentSupport()
    {
        $mazenDom = new OptimizmeMazenDomParsing();
        $this->returnAjax['domdocument_support'] = $mazenDom->isDOMDocument;
    }

    ////////////////////////////////////////////////
    //              SEARCH
    ////////////////////////////////////////////////

    /**
     * @param $search
     * @return array
     */
    public function mazenSearchWordsInPosts($search)
    {
        $tabResult = [];

        // configure search
        $args = [
            's' => $search,
            'posts_per_page' => -1
        ];
        $search = new \WP_Query($args);

        if (isset($search) && is_array($search->posts) && !empty($search->posts)) {
            $tabFields = [
                'content',
                'short_description',
                'meta_title',
                'meta_description'
            ];
            foreach ($search->posts as $searchLoop) {
                $objPost = OptimizmeMazenFormatObjects::loadPostForMazen($searchLoop, 0, $tabFields);
                array_push($tabResult, $objPost);
            }
        }
        $this->returnAjax['posts'] = $tabResult;
    }

    ////////////////////////////////////////////////
    //              UTILS
    ////////////////////////////////////////////////

    /**
     * @param $code
     */
    public function setReturnCode($code)
    {
        $this->returnCode = $code;
    }

    /**
     * Add errors to the object
     * @param $id_update
     */
    public function mazenLogWpObjectErrors($id_update)
    {
        if (is_wp_error($id_update)) {
            $errors = $id_update->get_error_messages();
            foreach ($errors as $error) {
                array_push($this->tabErrors, $error);
            }
        } elseif ($id_update === 0) {
            array_push($this->tabErrors, 'Error saving element');
        }
    }

    /**
     * Check if has error or not
     * @return bool
     */
    public function mazenHasErrors()
    {
        if (is_array($this->tabErrors) && !empty($this->tabErrors)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $msg
     * @param string $typeResult
     * @param array $msgComplementaires
     */
    public function mazenSetMsgReturn($msg, $typeResult = 'success', $msgComplementaires = [])
    {
        $tabData = ['message' => $msg];
        $this->mazenSetDataReturn($tabData, $typeResult, $msgComplementaires);
    }

    /**
     * @param $tabData
     * @param string $typeResult : success, info, warning, danger
     * @param array $msgComplementaires
     */
    public function mazenSetDataReturn($tabData, $typeResult = 'success', $msgComplementaires = [])
    {
        $this->returnResult['result'] = $typeResult;

        if (is_array($tabData) && !empty($tabData)) {
            foreach ($tabData as $key => $value) {
                $this->returnResult[$key] = $value;
            }
        } else {
            $this->returnResult['message'] = __('Action done!', 'mazen-seo-connector');
        }

        if (is_array($msgComplementaires) && !empty($msgComplementaires)) {
            $this->returnResult['logs'] = $msgComplementaires;
        }

        // return results
        if ($this->returnCode == 403) {
            header('HTTP/1.0 403 Forbidden');
        }
        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');
        echo json_encode($this->returnResult);
        exit;
    }
}
