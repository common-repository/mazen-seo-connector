<?php
namespace Optimizme\Mazen;

/**
 * Class OptimizmeMazenBo
 * @package Optimizme\Mazen
 */
class OptimizmeMazenBo
{
    /**
     * OptimizmeMazenFo constructor.
     */
    public function __construct()
    {
        // Register the new dashboard widget with the 'wp_dashboard_setup' action
        add_action('wp_dashboard_setup', array( $this, 'mazenAddOptimizmeMazenBoWidgets' ));

        // add in left menu
        add_action('admin_menu', array( $this, 'mazenRegisterMenuPages'));

        // add css and js
        add_action('admin_enqueue_scripts', array( $this, 'mazenOptimizmeAdminEnqueueScripts'));


        add_action('admin_init', array( $this, 'mazenTinymceAddCss'));
    }


    /**
     * Add widget on the dashboard
     */
    public function mazenAddOptimizmeMazenBoWidgets()
    {
        wp_add_dashboard_widget('optimizme-widget', 'News from Mazen', array($this, 'mazenDashboardOptimizmeWidgetFunction'));
    }

    /**
     * Show RSS
     */
    public function mazenDashboardOptimizmeWidgetFunction()
    {
        OptimizmeMazenUtils::mazenShowNewsRss('https://mazen-app.com/feed/', 10);
    }


    /**
     * Register a custom menu page.
     */
    public function mazenRegisterMenuPages()
    {
        // main entry
        add_menu_page(
            'Mazen',
            'Mazen',
            'manage_options',
            'optimizme',
            array('Optimizme\Mazen\OptimizmeMazenInterfacesBo', 'mazenMenuHome'),
            plugins_url('mazen-seo-connector/assets/img/logo-mini.png'),
            3
        );

        // subpages
        add_submenu_page('optimizme', __('Home', 'mazen-seo-connector'), __('Home', 'mazen-seo-connector'), 'manage_options', 'optimizme', array('Optimizme\Mazen\OptimizmeMazenInterfacesBo', 'mazenMenuHome'));
        add_submenu_page('optimizme', __('Redirections', 'mazen-seo-connector'), __('Redirections', 'mazen-seo-connector'), 'manage_options', 'optimizme_redirect', array('Optimizme\Mazen\OptimizmeMazenInterfacesBo', 'mazenMenuRedirect'));
    }

    /**
     * Enqueue scripts
     */
    public function mazenOptimizmeAdminEnqueueScripts()
    {
        wp_enqueue_script("optimizme_functions", OPTIMIZME_MAZEN_FOR_WP_URL .'assets/js/optimizme_functions.js', array('jquery'));
        wp_enqueue_style("optimizme_css_bo", OPTIMIZME_MAZEN_FOR_WP_URL .'assets/css/optimizme_bo.css');
    }

    /**
     *  Add custom css
     */
    public function mazenTinymceAddCss()
    {
        //add_editor_style(OPTIMIZME_MAZEN_FOR_WP_URL .'assets/css/optimizme_fo.css');
    }
}
