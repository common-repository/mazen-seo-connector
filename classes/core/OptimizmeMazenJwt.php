<?php
namespace Optimizme\Mazen;

use \Firebase\JWT\JWT;

/**
 * Class OptimizmeMazenJwt
 * @package Optimizme\Mazen
 */
class OptimizmeMazenJwt
{
    /**
     * @param $s
     * @return bool
     */
    public static function mazenIsJwt($s)
    {
        if (is_array($s)) {
            return false;
        }
        if (is_object($s)) {
            return false;
        }
        if (substr_count($s, '.') != 2) {
            return false;
        }
        if (strstr($s, '{')) {
            return false;
        }
        if (strstr($s, '}')) {
            return false;
        }
        if (strstr($s, ':')) {
            return false;
        }

        // all tests OK, JWT
        return true;
    }

    /**
     * @param int $length
     * @return array
     */
    public static function mazenGenerateKeyForJwt($length = 32)
    {
        // generate jwt secret
        try {
            $key = bin2hex(random_bytes($length));

            // generate id client
            $idClient = random_int(1, 999999999);

            // save
            update_option('optimizme_mazen_jwt_secret_'. $idClient, $key);

            $tab = [
                'token' => $key,
                'id_client' => $idClient
            ];
        } catch (\Exception $e) {
            return false;
        }

        return $tab;
    }

    /**
     * @param $jwt
     * @param OptimizmeMazenActions $optAction
     * @return int
     */
    public static function getIdProject($jwt, $optAction)
    {
        $tks = explode('.', $jwt);
        if (count($tks) != 3) {
            $msg = __('JWT Error: should be in 3 parts', 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
            die;
        }
        $headb64 = $tks[0];
        if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
            $msg = __('JWT Error: Header is null', 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
            die;
        }

        if (!is_object($header) || !isset($header->idc) || !is_numeric($header->idc)) {
            $msg = __('JWT Error: Header not formatted correctly', 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
            die;
        } else {
            return $header->idc;
        }
    }

    /**
     * @return array|null|object
     */
    public static function getAllJwts()
    {
        global $wpdb;

        // get jwt's in database
        $keys = 'SELECT *
                FROM '. $wpdb->options .'
                WHERE option_name LIKE "optimizme_mazen_jwt_secret_%"';
        $resKeys = $wpdb->get_results($keys);

        return $resKeys;
    }

    /**
     * @param $idClient
     * @param OptimizmeMazenActions $optAction
     * @return mixed
     */
    public static function getJwtSecretFromIdProject($idClient, $optAction)
    {
        $resKeys = OptimizmeMazenJwt::getAllJwts();

        if (!is_array($resKeys)) {
            $optAction->setReturnCode(403);
            $msg = __('JWT Error: Key list is not an array', 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
            die;
        } elseif (empty($resKeys)) {
            $optAction->setReturnCode(403);
            $msg = __('JWT Error: no JWT in database. Please connect your CMS with Mazen.', 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
            die;
        } else {
            foreach ($resKeys as $optionRow) {
                if ($optionRow->option_name == 'optimizme_mazen_jwt_secret_'. $idClient) {
                    return $optionRow->option_value;
                }
            }

            // not found
            $optAction->setReturnCode(403);
            $msg = __('JWT Error: JWT did not match with idc', 'mazen-seo-connector');
            $optAction->mazenSetMsgReturn($msg, 'danger');
            die;
        }
    }
}
