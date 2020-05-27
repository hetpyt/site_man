<?php
require_once("main_menu.php");

$root = array_shift($main_menu);
echo_item_begin('normal', '', $root['title']);
echo_item_end();

usort($root['items'], 'cmp_menu_items');
foreach ($root['items'] as $menu_item) {
    build_menu('', $menu_item);
}
?>