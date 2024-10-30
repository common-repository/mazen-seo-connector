<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenActionsDispatcherSite
 * @package Optimizme\Mazen
 */
class OptimizmeMazenActionsDispatcherSite
{
    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function get($optAction, $data)
    {
        if (isset($data->field) && $data->field != '') {
            if ($data->field == 'register_cms') {
                $optAction->mazenRegisterCMS($data);
            } elseif ($data->field == 'plugin_version') {
                $optAction->mazenGetPluginVersion();
            } elseif ($data->field == 'domdocument_support') {
                $optAction->mazenGetDomDocumentSupport();
            } elseif ($data->field == 'check_credentials') {
                $optAction->mazenCheckCredentials($data);
            } elseif ($data->field == 'options') {
                $optAction->mazenLoadSiteOptions();
            } else {
                $msg = __('Field ' . $data->field . ' is not supported in get ' . $data->type, 'mazen-seo-connector');
                $optAction->mazenSetMsgReturn($msg, 'danger');
            }
        } else {
            $msg = __('Field not set for ' . $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        }
    }

    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function update($optAction, $data)
    {
        if (!isset($data->field)) {
            $msg = __('Field is not set in update '. $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        } elseif (!isset($data->value)) {
            $msg = __('Value is not set in update '. $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        } else {
            if ($data->field == 'register_cms') {
                $optAction->mazenRegisterCMS($data);
            } elseif ($data->field == 'title') {
                $optAction->mazenSetBlogTitle($data->value);
            } elseif ($data->field == 'description') {
                $optAction->mazenSetBlogDescription($data->value);
            } elseif ($data->field == 'public') {
                $optAction->mazenSetBlogPublicOrPrivate($data->value);
            } else {
                $msg = __('Field ' . $data->field . ' is not supported in update ' . $data->type, 'mazen-seo-connector');
                $optAction->mazenSetMsgReturn($msg, 'danger');
            }
        }
    }

    /**
     * @param OptimizmeMazenActions $optAction $optAction
     * @param $data
     */
    public function search($optAction, $data)
    {
        if (!isset($data->value)) {
            $msg = __('Value is not set in search '. $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        } else {
            $optAction->mazenSearchWordsInPosts($data->value);
        }
    }
}
