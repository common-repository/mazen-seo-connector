<?php

namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenRedirections
 * @package Optimizme\Mazen
 */
class OptimizmeMazenRedirections
{

    /**
     * OptimizmeMazenRedirections constructor.
     */
    public function __construct()
    {
        // check 404 for optimizme redirections
        add_filter('template_redirect', [$this, 'mazenRedirect404']);
    }

    /**
     * if 404, check if redirections set in mazen_redirections table
     */
    public function mazenRedirect404()
    {
        // is url in redirect table ?
        if (is_404()) {
            $objRedirection = $this->mazenGetRedirection($_SERVER['REQUEST_URI'], 0);
            if ($objRedirection) {
                wp_redirect($objRedirection->url_redirect, 301);
                exit;
            }
        }
    }

    /** add a redirection in mazen_redirections */
    public function mazenAddRedirection($oldUrl, $newUrl)
    {
        global $wpdb;

        // add in database
        if ($oldUrl != $newUrl) {
            // check if url already exists
            $redirection = $this->mazenGetRedirection($oldUrl);
            if (isset($redirection->id) && $redirection->id != '') {
                // update existing redirection
                $wpdb->update(
                    OPTIMIZME_MAZEN_TABLE_REDIRECTIONS,
                    ['url_redirect' => $newUrl, 'updated_at' => date('Y-m-d H:i:s')],
                    ['id' => $redirection->id],
                    ['%s', '%s']
                );

                $result = 'update';
            } else {
                // insert redirection
                $wpdb->insert(
                    OPTIMIZME_MAZEN_TABLE_REDIRECTIONS,
                    ['url_base' => $oldUrl, 'url_redirect' => $newUrl, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                    ['%s', '%s']
                );
                $result = 'insert';
            }

            // change all links in post_content
            OptimizmeMazenUtils::mazenChangeAllLinksInPostContent($oldUrl, $newUrl);
        } else {
            $result = 'same';
        }

        // check if there is no double redirection
        $this->mazenCheckAndPurgeUrlIfDoubleRedirections();

        return $result;
    }

    /**
     * Edit redirection
     */
    public function mazenEditRedirection($id, $field, $value)
    {
        global $wpdb;

        if ($id != '') {
            $wpdb->update(
                OPTIMIZME_MAZEN_TABLE_REDIRECTIONS,
                [$field => $value, 'updated_at' => date('Y-m-d H:i:s')],
                ['ID' => $id]
            );
        }
        return false;
    }

    /**
     * Delete redirection
     * @param $id
     */
    public function mazenDeleteRedirection($id)
    {
        global $wpdb;
        $wpdb->delete(OPTIMIZME_MAZEN_TABLE_REDIRECTIONS, ['ID' => $id]);
    }

    /**
     * @param $id
     * @param $isDisabled
     */
    public function mazenUpdateRedirection($id, $isDisabled)
    {
        $this->mazenEditRedirection($id, 'is_disabled', $isDisabled);
    }

    /**
     * @param $id
     */
    public function mazenDisableRedirection($id)
    {
        $this->mazenEditRedirection($id, 'is_disabled', 1);
    }

    /**
     * @param $id
     */
    public function mazenEnableRedirection($id)
    {
        $this->mazenEditRedirection($id, 'is_disabled', 0);
    }

    /**
     * oad all saved redirections
     * @param string $statut
     * @return array|null|object
     */
    public function mazenGetAllRedirections($statut = 'active')
    {
        global $wpdb;
        $tabReturn = [];

        if ($statut == 'disabled') {
            $complementSQL = ' WHERE is_disabled="1" ';
        } elseif ($statut == 'all') {
            $complementSQL = ' ';
        } else {
            $complementSQL = ' WHERE is_disabled="0" ';
        }

        $sqlRedirections = 'SELECT *
                            FROM ' . OPTIMIZME_MAZEN_TABLE_REDIRECTIONS . ' 
                            ' . $complementSQL . '
                            ORDER BY id';

        //return $wpdb->get_results($sqlRedirections);
        $res = $wpdb->get_results($sqlRedirections);
        if (is_array($res) && !empty($res)) {
            foreach ($res as $redirection) {
                if ($redirection->is_disabled == 0) {
                    $publish = 1;
                } else {
                    $publish = 0;
                }

                $tabRedirection = [
                    'id' => (int)$redirection->id,
                    'url_base' => $redirection->url_base,
                    'url_redirect' => $redirection->url_redirect,
                    'publish' => (int)$publish,
                    'created_at' => $redirection->created_at,
                    'updated_at' => $redirection->updated_at
                ];
                array_push($tabReturn, $tabRedirection);
            }
        }
        return $tabReturn;
    }

    /**
     * @param $oldUrl
     * @param int $isDisabled
     * @return array|null|object
     */
    public function mazenGetRedirection($oldUrl, $isDisabled = 0)
    {
        global $wpdb;
        $redirection = $wpdb->prepare(
            "
                        SELECT * 
                        FROM {$wpdb->prefix}mazen_redirections 
                        WHERE `url_base` LIKE '%%%s%%' 
                        AND `is_disabled` = %d
                        ",
            $oldUrl,
            $isDisabled
        );
        $objRedirect = $wpdb->get_row($redirection);

        return $objRedirect;
    }

    /**
     * Purge double redirections
     *  ex: link1 redirect to link2
     *      link2 redirect to link3
     *      => link1 redirect to link3
     */
    public function mazenCheckAndPurgeUrlIfDoubleRedirections()
    {
        global $wpdb;

        // get redirects which have another redirection
        $sql = 'SELECT r1.id as r1id, r1.url_base as r1url_base, r1.url_redirect as r1url_redirect,
                      r2.id as r2id, r2.url_redirect as r2url_redirect
                FROM ' . OPTIMIZME_MAZEN_TABLE_REDIRECTIONS . ' r1
                JOIN ' . OPTIMIZME_MAZEN_TABLE_REDIRECTIONS . ' r2 on r1.id != r2.id
                WHERE r2.url_base = r1.url_redirect';
        $results = $wpdb->get_results($sql);

        if (is_array($results) && !empty($results)) {
            foreach ($results as $doubleRedirection) {
                $this->mazenEditRedirection($doubleRedirection->r1id, 'url_redirect', $doubleRedirection->r2url_redirect);
            }
        }

        // delete redirection if base == redirect
        $sqlSame = 'SELECT r.*
                    FROM ' . OPTIMIZME_MAZEN_TABLE_REDIRECTIONS . ' r
                    WHERE r.url_base = r.url_redirect';
        $resSame = $wpdb->get_results($sqlSame);
        if (is_array($resSame) && !empty($resSame)) {
            foreach ($resSame as $same) {
                $this->mazenDeleteRedirection($same->id);
            }
        }
    }
}
