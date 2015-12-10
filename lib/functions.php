<?php

function human_filesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

function c_dashboard_usage_get_quota($object) {
    if ($object instanceof ElggGroup) {
        return elgg_get_plugin_setting('group_quota', 'c_dashboard_usage');
    } elseif ($object instanceof ElggUser) {
        return elgg_get_plugin_setting('user_quota', 'c_dashboard_usage');
    } else {
        return false;
    }
}

function c_dashboard_usage_get_usage($object) {
    $usage = $object->getPrivateSetting('usage');
    if (!$usage | $usage < 0) {
        $usage = 0;
    }

    return $usage;
}

function c_dashboard_usage_update_usage($object, $bytes) {
    $usage = $object->getPrivateSetting('usage');
    if (!$usage | $usage < 0) {
        $usage = 0;
    }

    return $object->setPrivateSetting('usage', $usage + $bytes);
}

function c_dashboard_quota_exceeded_error($object, $bytes) {
    if ($object instanceof ElggGroup) {
        register_error(elgg_echo('c_dashboard_usage:group:quota:exceeded', array(human_filesize($bytes))));
    } elseif ($object instanceof ElggUser) {
        register_error(elgg_echo('c_dashboard_usage:user:quota:exceeded', array(human_filesize($bytes))));
    } else {
        throw new Exception('Try to enforce an unknown quota');
    }
}