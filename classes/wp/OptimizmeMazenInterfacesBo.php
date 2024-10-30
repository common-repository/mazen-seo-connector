<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenInterfacesBo
 * @package Optimizme\Mazen
 */
class OptimizmeMazenInterfacesBo
{
    /**
     * Home
     */
    public static function mazenMenuHome()
    {
        global $title;   // Menu title, defined in add_menu_page

        $resKeys = OptimizmeMazenJwt::getAllJwts(); ?>
        <div class="wrap">
            <h2><?php echo strip_tags($title); ?></h2>
        </div>

        <div class="postbox-container">
            <div class="postbox">
                <div class="inside">
                    <h3>JWT KEYS</h3>
                    <?php if (is_array($resKeys) && !empty($resKeys)) : ?>
                        <?php foreach ($resKeys as $couple) : ?>
                            <?php echo strip_tags(str_replace('optimizme_mazen_jwt_secret_', '', $couple->option_name)) ?>
                            :
                            <?php echo strip_tags($couple->option_value) ?><br />
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No JWT keys found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="postbox-container">
            <div class="postbox">
                <div class="inside">
                    <?php
                    OptimizmeMazenUtils::mazenShowNewsRss('https://mazen-app.com/feed/', 10); ?>
                </div>
            </div>
        </div>
        <?php

    }


    /**
     * Manage redirections
     */
    public static function mazenMenuRedirect()
    {
        global $title;

        $objRedirect = new OptimizmeMazenRedirections();
        $classDisabled = '';
        $classPublish = '';

        // disable / delete redirection?
        if (isset($_GET['disable']) && $_GET['disable'] != '') {
            $objRedirect->mazenDisableRedirection($_GET['disable']);
            OptimizmeMazenUtils::mazenShowMessageBackoffice(__('Redirect disabled', 'mazen-seo-connector'));
        } elseif (isset($_GET['enable']) && $_GET['enable'] != '') {
            $objRedirect->mazenEnableRedirection($_GET['enable']);
            OptimizmeMazenUtils::mazenShowMessageBackoffice(__('Redirect enabled', 'mazen-seo-connector'));
        } elseif (isset($_GET['delete']) && $_GET['delete'] != '') {
            $objRedirect->mazenDeleteRedirection($_GET['delete']);
            OptimizmeMazenUtils::mazenShowMessageBackoffice(__('Redirect deleted', 'mazen-seo-connector'));
        }
        
        $objRedirect->mazenCheckAndPurgeUrlIfDoubleRedirections();

        // get redirections by types
        $tabRedirectionsPublish = $objRedirect->mazenGetAllRedirections();
        $tabRedirectionsDisabled = $objRedirect->mazenGetAllRedirections('disabled');

        // filter
        if (isset($_GET['status']) && $_GET['status'] == 'disabled') {
            $classDisabled = 'current';
            $tableauRedirections = $tabRedirectionsDisabled;
            $status = 'disabled';
        } else {
            $classPublish = 'current';
            $tableauRedirections = $tabRedirectionsPublish;
            $status = '';
        } ?>

        <div class="wrap">
            <h2><?php echo strip_tags($title) ?></h2>
            <p><?php _e('All redirects:', 'mazen-seo-connector') ?></p>
        </div>

        <ul class="subsubsub">
            <li class="publish">
                <a href="admin.php?page=optimizme_redirect" class="<?php echo strip_tags($classPublish) ?>">
                    <?php _e('Enabled', 'mazen-seo-connector') ?>
                    <span class="count">(<?php echo strip_tags(count($tabRedirectionsPublish)) ?>)</span>
                </a>
                |
            </li>
            <li class="disabled">
                <a href="admin.php?page=optimizme_redirect&status=disabled" class="<?php echo strip_tags($classDisabled) ?>">
                    <?php _e('disabled', 'mazen-seo-connector') ?>
                    <span class="count">(<?php echo strip_tags(count($tabRedirectionsDisabled)) ?>)</span>
                </a>
            </li>
        </ul>

        <table class="wp-list-table widefat fixed striped items">
            <thead>
            <tr>
                <th class="manage-column column-type column-primary table_short">ID</th>
                <th><?php _e("Base URL", "optimizme") ?></th>
                <th><?php _e("Redirect URL", "optimizme") ?></th>
                <th><?php _e("Created at", "optimizme") ?></th>
                <th><?php _e("Updated at", "optimizme") ?></th>
                <th class="table_short"></th>
            </tr>
            </thead>
            <tbody>
            <?php if (is_array($tableauRedirections) && !empty($tableauRedirections)) : ?>
                <?php foreach ($tableauRedirections as $redirection) : ?>
                    <tr>
                        <td><?php echo strip_tags($redirection['id']) ?></td>
                        <td>
                            <a href="<?php echo strip_tags($redirection['url_base']) ?>" target="_blank">
                                <?php echo strip_tags($redirection['url_base']) ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo strip_tags($redirection['url_redirect']) ?>" target="_blank">
                                <?php echo strip_tags($redirection['url_redirect']) ?>
                            </a>
                        </td>
                        <td><?php echo strip_tags($redirection['created_at']) ?></td>
                        <td><?php echo strip_tags($redirection['updated_at']) ?></td>
                        <td>
                            <?php if ($status == 'disabled') : ?>
                                <a href="admin.php?page=optimizme_redirect&status=disabled&enable=<?php echo strip_tags($redirection['id']) ?>">
                                    <?php _e('Enable', 'mazen-seo-connector') ?>
                                </a>
                            <?php else : ?>
                                <a href="admin.php?page=optimizme_redirect&disable=<?php echo strip_tags($redirection['id']) ?>" class="disableConfirm">
                                    <?php _e('Disable', 'mazen-seo-connector') ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <?php

    }
}
