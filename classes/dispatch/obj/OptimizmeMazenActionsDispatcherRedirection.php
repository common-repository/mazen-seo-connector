<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenActionsDispatcherRedirection
 * @package Optimizme\Mazen
 */
class OptimizmeMazenActionsDispatcherRedirection
{
    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function get($optAction, $data)
    {
        $optAction->mazenLoadRedirections();
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

            if ($data->field == 'publish') {
                $optAction->mazenEnableDisableRedirection($id, $data->value);
            } elseif ($data->field == 'url_base' || $data->field == 'url_redirect') {
                $optAction->mazenUpdateRedirection($id, $data->field, $data->value);
            } else {
                $msg = __('Field ' . $data->field . ' is not supported in update ' . $data->type, 'mazen-seo-connector');
                $optAction->mazenSetMsgReturn($msg, 'danger');
            }
        }
    }

    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function delete($optAction, $data)
    {
        if (!isset($data->id) || !is_numeric($data->id)) {
            $msg = __('Id is not set in delete '. $data->type, 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
        } else {
            $optAction->mazenDeleteRedirection($data);
        }
    }
}
