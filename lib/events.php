<?php

function c_dashboard_usage_create_object_event_handler($event, $object_type, $object) {
    if ($object->getSubtype() != "file") {
        return true;
    }

    $container = $object->getContainerEntity();

    $size = filesize($object->getFilenameOnFilestore());
    $usage = c_dashboard_usage_get_usage($container);
    $quota = c_dashboard_usage_get_quota($container);

    if ($usage + $size  <= $quota) {
        $object->size = $size; // persist size in object as we are not able to retrieve it on delete event, the file is already gone when delete event is triggered.
        c_dashboard_usage_update_usage($container, $object->size);
        return true;
    } else {
        c_dashboard_quota_exceeded_error($container, ($usage + $size) - $quota);
        return false;
    }
}

function c_dashboard_usage_update_object_event_handler($event, $object_type, $object) {
    if ($object->getSubtype() != "file") {
        return true;
    }

    $container = $object->getContainerEntity();

    $usage = c_dashboard_usage_get_usage($container);
    $quota = c_dashboard_usage_get_quota($container);
    $delta = filesize($object->getFilenameOnFilestore()) - $object->size;

    if ($usage + $delta <= $quota) {
        c_dashboard_usage_update_usage($container, $delta);
        $object->size = filesize($object->getFilenameOnFilestore());
        return true;
    } else {
        c_dashboard_quota_exceeded_error($container, ($usage + $delta) - $quota);
        return false;
    }
}

function c_dashboard_usage_delete_object_event_handler($event, $object_type, $object) {
    if ($object->getSubtype() == "file") {
        c_dashboard_usage_update_usage($object->getContainerEntity(), - $object->size);
    }

    return true;
}
