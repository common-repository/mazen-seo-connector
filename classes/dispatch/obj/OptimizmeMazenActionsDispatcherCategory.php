<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenActionsDispatcherPost
 * @package Optimizme\Mazen
 */
class OptimizmeMazenActionsDispatcherCategory
{
    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function create($optAction, $data)
    {
        $optAction->mazenCreateCategory($data);
    }

    /**
     * @param OptimizmeMazenActions $optAction
     * @param $data
     */
    public function get($optAction, $data)
    {
        if (isset($data->id) && is_numeric($data->id)) {
            $optAction->mazenLoadCategoryContent($data->id);
        } else {
            $optAction->mazenLoadCategories();
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
            $optAction->mazenIsDataUpdatable($data, 'category');
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

            if ($data->field == 'name') {
                $optAction->mazenUpdateCategory($id, $data->value, 'name');
            } elseif ($data->field == 'description') {
                $optAction->mazenUpdateCategory($id, $data->value, 'description');
            } elseif ($data->field == 'slug') {
                $optAction->mazenSetCategorySlug($id, $data->value);
            } else {
                $msg = __('Field ' . $data->field . ' is not supported in update ' . $data->type, 'mazen-seo-connector');
                $optAction->mazenSetMsgReturn($msg, 'danger');
            }
        }
    }
}
