<?php
/*
Plugin Name: EC Price-Table
Plugin URI: https://github.com/ecnordbund/ec-price-tabel
Description: Adds a shortcode to Wordpress which shows prices for events in an table
Author: Tobias Krause
Author URI: https://github.com/tobikrs
Version: 1.0
*/

if (!defined('ABSPATH')) exit;


add_shortcode('ec-prices', 'ecpt_pricetable_shortcode');

function ecpt_pricetable_shortcode($atts) {
    $prices = shortcode_atts(array(
        'default' => 0,
        'fruehbucher' => 0,
        'last_minute' => 0,
        'fruebucher_bis' => '',
        'last_minute_ab' => '',
        'hint' => '',
    ), $atts);

    $column_count = 0;

    $html = '<div id="ec-prices" style="margin: 24px 0' . ( empty($prices['hint']) ? '' : ' 0' ) . ';">';

    // Frühbucher
    if($prices['fruehbucher'] > 0) {
        $column_count++;
        $html .= ecpt_generate_column('Frühbucher', $prices['fruehbucher'], 'bis ' .$prices['fruebucher_bis'], $prices['hint'], 'fruehbucher');
    }

    // Normalpreis
    if($prices['default'] > 0) {
        $column_count++;
        if ($prices['fruehbucher'] > 0 && $prices['last_minute'] > 0) {
            $html .= ecpt_generate_column('Normalpreis', $prices['default'], '', $prices['hint']);
        } else {
            $html .= ecpt_generate_column('Kosten', $prices['default'], '', $prices['hint']);
        }
    }

    // Last-Minute
    if($prices['last_minute'] > 0) {
        $column_count++;
        $html .= ecpt_generate_column('Last-Minute', $prices['last_minute'], 'ab ' . $prices['last_minute_ab'], $prices['hint'], 'last_minute');
    }
    
    $html .= '</div>';

    if(!empty($prices['hint'])) {
        $html .= '<div style="padding: 0 2px; text-align: left; font-size: 14px; font-style: italic; margin-bottom: 24px;">* ' . $prices['hint'] . '</div>';
    }

    $html .= ecpt_generate_style($column_count);

    return $html;
}

function ecpt_generate_column($title = '', $price = '', $date = '', $hint = '', $type = 'default') {
    $color = ecpt_get_column_color($type);
    $text_color = ecpt_get_column_text_color($type);

    $html = '<div class="columns" style="float: left; background-color: white;">';
    $html .= '<ul style="list-style-type: none; margin: 0; padding: 0;">';
        $html .= '<li style="padding: 12px 24px; text-align: center; font-size: 25px; background-color: ' . $color . '; color: ' . $text_color . '">' . $title . (empty($hint) ? '' : '*') . '</li>';
        $html .= '<li style="border-left: 1px solid #eee; border-right: 1px solid #eee; border-bottom: 1px solid #eee; padding: 12px 24px; text-align: center;"><strong>' . $price . '</strong> EUR </li>';
        if(!empty($date)) {
            $html .= '<li style="border-left: 1px solid #eee; border-right: 1px solid #eee; border-bottom: 1px solid #eee; padding: 8px 24px; min-height: 60px; text-align: center;">' . $date . '</li>';
        }
    $html .= '</ul>';
    $html .= '</div>';

    return $html;
}

function ecpt_generate_style($column_count) {
    $col_width = ecpt_get_column_width($column_count);

    $style = '<style>';

    $style .= '#ec-prices { display: flex; display: -webkit-flex; } ';
    $style .= '#ec-prices > .columns { width: ' . $col_width . '; } ';
    
    $style .= '@media only screen and (max-width: 600px) {';
        $style .= '#ec-prices { display: inline-block; }';
        $style .= '#ec-prices > .columns { width: 100%; margin-top: 16px; }';
    $style .= '}';

    $style .= '</style>';
    
    return $style;
}

function ecpt_get_column_width($column_count) {
    switch ($column_count) {
        case 3:
            return '33.3%';
        
        case 2:
            return '50%';
        
        default:
            return '100%';
    }
}

function ecpt_get_column_color($price_type) {
    switch ($price_type) {
        case 'fruehbucher':
            return '#b1ca34';
        
        case 'last_minute':
            return '#18471e';
        
        default:
            return '#eee';
    }
}

function ecpt_get_column_text_color($price_type) {
    switch ($price_type) {
        
        case 'last_minute':
            return '#ddd';
        
        default:
            return 'inherit';
    }
}