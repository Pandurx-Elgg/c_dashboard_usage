<?php
/**
 * Dashbaord plugin settings
 */

echo '<div>';
echo elgg_echo('c_dashboard_usage:group:quota');
echo ' ';
echo elgg_view('input/text', array(
    'name' => 'params[group_quota]',
    'value' => $vars['entity']->group_quota,
));
echo '</div>';

echo '<div>';
echo elgg_echo('c_dashboard_usage:user:quota');
echo ' ';
echo elgg_view('input/text', array(
    'name' => 'params[user_quota]',
    'value' => $vars['entity']->user_quota,
));
echo '</div>';