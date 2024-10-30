<?php

namespace Optimizme\Mazen;

use \Firebase\JWT\JWT;

/**
 * Class Optimizme
 */
class OptimizmeMazenCore
{
    public $boolNoAction;

    /**
     * OptimizmeMazenCore constructor.
     */
    public function __construct()
    {
        $this->boolNoAction = 0;
    }

    /**
     * Action to do after wordpress init
     */
    public static function mazenProcessCore()
    {
        /////////////////////////////////////
        // core ajax request
        /////////////////////////////////////

        $optimizmeMazenCore = new \Optimizme\Mazen\OptimizmeMazenCore();
        $optimizmeMazenCore->mazenRootAction();

        /////////////////////////////////////
        // REDIRECTIONS
        //  - if necessary, redirect
        /////////////////////////////////////

        $optimizmeMazenRedirect = new \Optimizme\Mazen\OptimizmeMazenRedirections();

        /////////////////////////////////////////
        // FRONT-OFFICE
        //  - add meta description if necessary
        /////////////////////////////////////////

        $optimizmeMazenFo = new \Optimizme\Mazen\OptimizmeMazenFo();


        /////////////////////////////////////
        // BACK-OFFICE
        //  - menu "Mazen"
        //  - news from blog on dashboard
        /////////////////////////////////////

        $optimizmeMazenBo = new \Optimizme\Mazen\OptimizmeMazenBo();
    }

    /**
     * Route action if necessary
     */
    public function mazenRootAction()
    {
        // ACTIONS
        $optAction = new OptimizmeMazenActions();

        $isDataFormMazen = false;
        if (isset($_GET['mazenseoconnectorsearch']) && $_GET['mazenseoconnectorsearch'] != '') {
            $requestDataOptme = new \stdClass();
            $requestDataOptme->data_optme = [
                'action' => 'search',
                'type' => 'site',
                'value' => $_GET['mazenseoconnectorsearch']
            ];
            $requestDataOptme = json_encode($requestDataOptme);
            $isDataFormMazen = true;
        } elseif (isset($_REQUEST['data_optme'])) {
            // $_POST or $_GET
            $requestDataOptme = new \stdClass();
            $requestDataOptme->data_optme = $_REQUEST['data_optme'];
            $requestDataOptme = json_encode($requestDataOptme);
            $isDataFormMazen = true;
        } else {
            // try to get application/json content
            $phpInput = file_get_contents('php://input');
            if ($phpInput) {
                $requestDataOptme = stripslashes($phpInput);
                if (strstr($requestDataOptme, 'data_optme')) {
                    $isDataFormMazen = true;
                }
            }
        }

        if (isset($requestDataOptme) && $requestDataOptme != '' && $isDataFormMazen == true) {
            $jsonData = json_decode($requestDataOptme);
            if (!isset($jsonData->data_optme) || $jsonData->data_optme == '') {
                exit;
            }

            if (OptimizmeMazenJwt::mazenIsJwt($jsonData->data_optme)) {
                // JWT
                $idProject = OptimizmeMazenJwt::getIdProject($jsonData->data_optme, $optAction);
                if ($idProject == '' || !is_numeric($idProject)) {
                    $msg = __('JWT Error: no idc set in header', 'mazen-seo-connector');
                    $optAction->mazenSetMsgReturn($msg, 'danger');
                    die;
                }

                $jwtSecret = OptimizmeMazenJwt::getJwtSecretFromIdProject($idProject, $optAction);
                try {
                    // try decode JSON Web Token
                    $decoded = JWT::decode($jsonData->data_optme, $jwtSecret, ['HS256']);
                    $dataOptimizme = $decoded;
                } catch (\Firebase\JWT\SignatureInvalidException $e) {
                    $msg = __('JSON Web Token not decoded properly, secret may be not correct: ', 'mazen-seo-connector');
                    $optAction->mazenSetMsgReturn($msg, 'danger');
                    die;
                }

                // log action
                if (OPTIMIZME_MAZEN_ENABLE_LOGS == 1) {
                    $logContent = "--------------\n" . 'Date: ' . date('Y-m-d H:i:s') . "\n";
                    $logContent .= 'Data: ' . $requestDataOptme . "\n";
                    try {
                        if (is_writable(OPTIMIZME_MAZEN_LOGS)) {
                            if ($handle = fopen(OPTIMIZME_MAZEN_LOGS, 'a+')) {
                                fwrite($handle, $logContent);
                                fclose($handle);
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }
            } else {
                // simple JSON, allowed only for "register_cms" action and "search/site" action
                $dataOptimizme = $jsonData->data_optme;
                if (!isset($dataOptimizme->action)) {
                    $dataOptimizme->action = '';
                }
                if (!isset($dataOptimizme->type)) {
                    $dataOptimizme->type = '';
                }

                if (!is_object($dataOptimizme)) {
                    $msg = __('JSON Web Token needed - not an object', 'mazen-seo-connector');
                    $optAction->mazenSetMsgReturn($msg, 'danger');
                    die;
                } elseif ($dataOptimizme->action != 'register_cms' &&
                    ($dataOptimizme->action != 'search' && $dataOptimizme->type != 'site')
                ) {
                    $msg = __('JSON Web Token needed - action not allowed', 'mazen-seo-connector');
                    $optAction->mazenSetMsgReturn($msg, 'danger');
                    die;
                }
            }

            /////////////////////////////////
            //          ACTIONS
            /////////////////////////////////

            if (!is_object($dataOptimizme)) {
                $msg = __('Data is not correctly formatted', 'mazen-seo-connector');
                $optAction->mazenSetMsgReturn($msg, 'danger');
            } elseif (!isset($dataOptimizme->action) || $dataOptimizme->action == '') {
                // no action specified
                $msg = __('No action set', 'mazen-seo-connector');
                $optAction->mazenSetMsgReturn($msg, 'danger');
            } else {
                // action to do
                $this->boolNoAction = OptimizmeMazenDispatcher::mazenDispatch($optAction, $dataOptimizme);

                // RESULTS OF ACTIONS
                if ($this->boolNoAction == 1) {
                    // no action done
                    $msg = __('No action found.', 'mazen-seo-connector');
                    $optAction->mazenSetMsgReturn($msg, 'danger');
                } else {
                    // action done
                    if (is_array($optAction->tabErrors) && !empty($optAction->tabErrors)) {
                        $msg = __('One or several errors have been detected', 'mazen-seo-connector');
                        $optAction->mazenSetMsgReturn($msg, 'danger', $optAction->tabErrors);
                    } else {
                        // ajax to return - encode data
                        $optAction->mazenSetDataReturn($optAction->returnAjax);
                    }
                }
            }

            // stop script
            die;
        }
    }
}
