<?php

/**
 * Dashboard usage groups page
 *
 * @param int $guid Group entity GUID
 */
function c_dashboard_usage_group_page($guid) {

    elgg_entity_gatekeeper($guid, 'group');
    $group = get_entity($guid);
    elgg_set_page_owner_guid($guid);
    elgg_group_gatekeeper();

    $title = elgg_echo('c_dashboard_usage:group', array($group->name));

    elgg_push_breadcrumb($group->name, $group->getURL());
    elgg_push_breadcrumb(elgg_echo('admin:administer_utilities:disk_space_usage'));

    $content = "<b>Todo....</b>";

    $params = array(
        'content' => $content,
        'title' => $title,
        'filter' => '',
    );
    $body = elgg_view_layout('content', $params);

    echo elgg_view_page($title, $body);
}