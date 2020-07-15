<?php
    /**
     * Plugin Name: Phone Validation
     * Plugin URI:  https://wisetr.com/
     * Description: Phone Validation.
     * Version: 1.0.0
     */
    if(!defined('ABSPATH')){
        exit;
    }

    if(in_array('woocommerce/woocommerce.php',apply_filters('active_plugins', get_option('active_plugins')))){
        add_action('plugins_loaded','wfacp_phone_validation');
    }

    function wfacp_phone_validation(){
        require 'includes/class-wfacp-phone-validation.php';
        new WFACP_Phone_Validation();
    }