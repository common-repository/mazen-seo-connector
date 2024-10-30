<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenActionsDispatcherPost
 * @package Optimizme\Mazen
 */
class OptimizmeMazenActionsDispatcherPost
{
    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function create($optAction, $data)
    {
        $optAction->mazenCreatePost($data);
    }

    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function get($optAction, $data)
    {
        if (isset($data->id) && is_numeric($data->id)) {
            $optAction->mazenLoadPostContent($data->id);
        } else {
            $optAction->mazenLoadPostsPages($data);
        }
    }

    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function updatable($optAction, $data)
    {
        if (!isset($data->id) || !is_numeric($data->id)) {
            $msg = __('Id is not set in updatable '. $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        } else {
            $optAction->mazenIsDataUpdatable($data, 'post');
        }
    }

    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function update($optAction, $data)
    {
        if (!isset($data->id) || !is_numeric($data->id)) {
            $msg = __('ID is not set in update '. $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        } elseif (!isset($data->field)) {
            $msg = __('Field is not set in update '. $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        } elseif (!isset($data->value)) {
            $msg = __('Value is not set in update '. $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        } else {
            $id = $data->id;

            if ($data->field == 'title') {
                $optAction->mazenSetTitle($id, $data->value);
            } elseif ($data->field == 'content') {
                $optAction->mazenSetContent($id, $data->value);
            } elseif ($data->field == 'short_description') {
                $optAction->mazenSetShortDescription($id, $data->value);
            } elseif ($data->field == 'slug') {
                $optAction->mazenSetSlug($id, $data->value);
            } elseif ($data->field == 'publish') {
                $optAction->mazenSetPostStatus($id, $data->value);
            } elseif ($data->field == 'meta_title') {
                $optAction->mazenSetMetaTitle($id, $data->value);
            } elseif ($data->field == 'meta_description') {
                $optAction->mazenSetMetaDescription($id, $data->value);
            } elseif ($data->field == 'url_canonical') {
                $optAction->mazenSetCanonicalUrl($id, $data->value);
            } elseif ($data->field == 'noindex' || $data->field == 'nofollow') {
                $optAction->mazenSetMetaRobot($id, $data->field, $data->value);
            } elseif ($data->field == 'blog_public') {
                $optAction->mazenSetBlogPublicOrPrivate($data->value);
            } elseif ($data->field == 'img') {
                if (isset($data->attribute) && $data->attribute != '') {
                    $optAction->mazenChangeSomeContentInTag($id, $data->value, 'img', $data->attribute);
                } else {
                    $optAction->mazenSetMsgReturn('Attribute is required for update a post image', 'danger');
                }
            } elseif ($data->field == 'a') {
                if (isset($data->attribute) && $data->attribute != '') {
                    $optAction->mazenChangeSomeContentInTag($id, $data->value, 'a', $data->attribute);
                } else {
                    $optAction->mazenChangeSomeContentInTag($id, $data->value, 'a');
                }
            } elseif ($data->field == 'h1' ||
                $data->field == 'h2' ||
                $data->field == 'h3' ||
                $data->field == 'h4' ||
                $data->field == 'h5' ||
                $data->field == 'h6'
            ) {
                $optAction->mazenChangeSomeContentInTag($id, $data->value, $data->field);
            } else {
                $msg = __('Field ' . $data->field . ' is not supported in update ' . $data->type, 'mazen-seo-connector');
                $optAction->mazenSetMsgReturn($msg, 'danger');
            }
        }
    }
}
