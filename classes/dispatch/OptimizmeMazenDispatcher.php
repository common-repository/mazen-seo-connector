<?php

namespace Optimizme\Mazen;

/**
 * Class Optimizme
 */
class OptimizmeMazenDispatcher
{
    public $boolNoAction;

    /**
     * Route action if necessary
     * @param OptimizmeMazenActions $optAction
     * @param $data
     * @return int
     */
    public static function mazenDispatch($optAction, $data)
    {
        $boolNoAction = 1;

        if (!isset($data->action)) {
            array_push($optAction->tabErrors, __('No action set', 'mazen-seo-connector'));
        } elseif ($data->action == 'register_cms') {
            $optAction->mazenRegisterCMS($data);
            $boolNoAction = 0;
        } else {
            // load required class for doing action
            if (isset($data->type) && $data->type != '') {
                $postTypes = get_post_types();
                $type = strtolower(strtolower($data->type));
                if (in_array($type, $postTypes)) {
                    $type = 'Post'; // same in Wordpress
                }
                $class = '\Optimizme\Mazen\OptimizmeMazenActionsDispatcher'. $type;

                if (class_exists($class)) {
                    $obj = new $class();
                    if (method_exists($obj, $data->action)) {
                        $obj->{$data->action}($optAction, $data);
                        $boolNoAction = 0;
                    } else {
                        array_push($optAction->tabErrors, __('Method '. $data->action .' not found for class '. $class, 'mazen-seo-connector'));
                    }
                } else {
                    array_push($optAction->tabErrors, __('Class not found for type '. $data->type, 'mazen-seo-connector'));
                }
            }
        }

        if ($boolNoAction == 1) {
            // try deprecated api (temporary)
            $boolNoAction = OptimizmeMazenDispatcher::dispatchMazenActionDeprecated($optAction, $data);
        }

        return $boolNoAction;
    }

    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     * @return int
     */
    public static function dispatchMazenActionDeprecated($optAction, $data)
    {
        $boolNoAction = 0;

        // id post to update, from url
        if (isset($data->id_post) && $data->id_post != '') {
            $elementId = $data->id_post;
        } elseif (isset($data->id) && $data->id != '') {
            $elementId = $data->id;
        } else {
            $elementId = '';
        }

        // dispatch action
        switch ($data->action) {
            // init dialog with cms
            case 'register_cms':
                $optAction->mazenRegisterCMS($data);
                break;
            case 'check_credentials':
                $optAction->mazenCheckCredentials($data);
                break;

            // v2
            case 'get':
                if (isset($data->type) && $data->type != '') {
                    if ($data->type == 'post') {
                        if (isset($elementId) && is_numeric($elementId)) {
                            $optAction->mazenLoadPostContent($elementId);
                        } else {
                            $optAction->mazenLoadPostsPages($data);
                        }
                    } elseif ($data->type == 'category') {
                        if (isset($elementId) && is_numeric($elementId)) {
                            $optAction->mazenLoadCategoryContent($elementId);
                        } else {
                            $optAction->mazenLoadCategories();
                        }
                    } elseif ($data->type == 'redirection') {
                        $optAction->mazenLoadRedirections();
                    } elseif ($data->type == 'site') {
                        if ($data->field == 'plugin_version') {
                            $optAction->mazenGetPluginVersion();
                        } elseif ($data->field == 'options') {
                            $optAction->mazenLoadSiteOptions();
                        } else {
                            $msg = __('Field ' . $data->field . ' is not supported in get ' . $data->type, 'mazen-seo-connector');
                            $optAction->mazenSetMsgReturn($msg, 'danger');
                        }
                    } else {
                        array_push($optAction->tabErrors, __('Not allowed type to get', 'mazen-seo-connector'));
                    }
                } else {
                    array_push($optAction->tabErrors, __('No type specified for get', 'mazen-seo-connector'));
                }
                break;

            case 'create':
                if (isset($data->type) && $data->type != '') {
                    if ($data->type == 'post') {
                        $optAction->mazenCreatePost($data);
                    } elseif ($data->type == 'category') {
                        $optAction->mazenCreateCategory($data);
                    } else {
                        array_push($optAction->tabErrors, __('Not allowed type to create', 'mazen-seo-connector'));
                    }
                } else {
                    array_push($optAction->tabErrors, __('No specified type in create', 'mazen-seo-connector'));
                }
                break;

            case 'update':
                if (isset($data->type) && $data->type != '') {
                    if ($data->type != 'site' && (!isset($elementId) || !is_numeric($elementId))) {
                        array_push($optAction->tabErrors, __('ID not specified or invalid', 'mazen-seo-connector'));
                    } elseif (!isset($data->value)) {
                        array_push($optAction->tabErrors, __('Value not specified', 'mazen-seo-connector'));
                    } else {
                        if ($data->type == 'post') {
                            // POSTS
                            if ($data->field == 'title') {
                                $optAction->mazenSetTitle($elementId, $data->value);
                            } elseif ($data->field == 'content') {
                                $optAction->mazenSetContent($elementId, $data->value);
                            } elseif ($data->field == 'short_description') {
                                $optAction->mazenSetShortDescription($elementId, $data->value);
                            } elseif ($data->field == 'slug') {
                                $optAction->mazenSetSlug($elementId, $data->value);
                            } elseif ($data->field == 'publish') {
                                $optAction->mazenSetPostStatus($elementId, $data->value);
                            } elseif ($data->field == 'meta_title') {
                                $optAction->mazenSetMetaTitle($elementId, $data->value);
                            } elseif ($data->field == 'meta_description') {
                                $optAction->mazenSetMetaDescription($elementId, $data->value);
                            } elseif ($data->field == 'url_canonical') {
                                $optAction->mazenSetCanonicalUrl($elementId, $data->value);
                            } elseif ($data->field == 'noindex' || $data->field == 'nofollow') {
                                $optAction->mazenSetMetaRobot($elementId, $data->field, $data->value);
                            } elseif ($data->field == 'blog_public') {
                                $optAction->mazenSetBlogPublicOrPrivate($data->value);
                            } elseif ($data->field == 'img') {
                                if (isset($data->attribute) && $data->attribute != '') {
                                    $optAction->mazenChangeSomeContentInTag($elementId, $data->value, 'img', $data->attribute);
                                } else {
                                    $optAction->mazenSetMsgReturn('Attribute is required for update a post image', 'danger');
                                }
                            } elseif ($data->field == 'a') {
                                if (isset($data->attribute) && $data->attribute != '') {
                                    $optAction->mazenChangeSomeContentInTag($elementId, $data->value, 'a', $data->attribute);
                                } else {
                                    $optAction->mazenChangeSomeContentInTag($elementId, $data->value, 'a');
                                }
                            } elseif ($data->field == 'h1' ||
                                $data->field == 'h2' ||
                                $data->field == 'h3' ||
                                $data->field == 'h4' ||
                                $data->field == 'h5' ||
                                $data->field == 'h6'
                            ) {
                                $optAction->mazenChangeSomeContentInTag($elementId, $data->value, $data->field);
                            } else {
                                $msg = __('Field ' . $data->field . ' is not supported in update ' . $data->type, 'mazen-seo-connector');
                                $optAction->mazenSetMsgReturn($msg, 'danger');
                            }
                        } elseif ($data->type == 'category') {
                            // CATEGORIES
                            if ($data->field == 'name') {
                                $optAction->mazenUpdateCategory($elementId, $data->value, 'name');
                            } elseif ($data->field == 'description') {
                                $optAction->mazenUpdateCategory($elementId, $data->value, 'description');
                            } elseif ($data->field == 'slug') {
                                $optAction->mazenSetCategorySlug($elementId, $data->value);
                            } else {
                                $msg = __('Field ' . $data->field . ' is not supported in update ' . $data->type, 'mazen-seo-connector');
                                $optAction->mazenSetMsgReturn($msg, 'danger');
                            }
                        } elseif ($data->type == 'redirection') {
                            if ($data->field == 'publish') {
                                $optAction->mazenEnableDisableRedirection($elementId, $data->value);
                            } elseif ($data->field == 'url_base' || $data->field == 'url_redirect') {
                                $optAction->mazenUpdateRedirection($elementId, $data->field, $data->value);
                            } else {
                                $msg = __('Field ' . $data->field . ' is not supported in update ' . $data->type, 'mazen-seo-connector');
                                $optAction->mazenSetMsgReturn($msg, 'danger');
                            }
                        } elseif ($data->type == 'site') {
                            if ($data->field == 'title') {
                                $optAction->mazenSetBlogTitle($data->value);
                            } elseif ($data->field == 'description') {
                                $optAction->mazenSetBlogDescription($data->value);
                            } elseif ($data->field == 'public') {
                                $optAction->mazenSetBlogPublicOrPrivate($data->value);
                            } else {
                                $msg = __('Field ' . $data->field . ' is not supported in update ' . $data->type, 'mazen-seo-connector');
                                $optAction->mazenSetMsgReturn($msg, 'danger');
                            }
                        } else {
                            array_push($optAction->tabErrors, __('Not allowed type to update', 'mazen-seo-connector'));
                        }
                    }
                } else {
                    array_push($optAction->tabErrors, __('No specified type in update ', 'mazen-seo-connector'));
                }
                break;


            default:
                $boolNoAction = 1;
                break;
        }

        return $boolNoAction;
    }
}
