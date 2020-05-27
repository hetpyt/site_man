<?
require_once('menu.php');

function echo_item_begin($arg_class, $url, $title) {
    echo('<li class="'.$arg_class.'">');
    echo('<a href="'.$url.'">');
    echo('<span class="navigation-item-bullet">></span>');
    echo('<span class="navigation-item-text">'.$title.'</span>');
    echo('</a>');
}

function echo_item_end() {
    echo('</li>');
}

function cmp_menu_items($a, $b) {
    if ($a['title'] == $b['title']) return 0;
    if ($a['title'] < $b['title']) return -1;
    if ($a['title'] > $b['title']) return 1;
}

function build_menu($parent_url, $menu_item) {
    $title = $menu_item['title'];
    $items = $menu_item['items'];
    $url = $menu_item['url'];
    $arg_class = 'normal';
    if (count($items) > 0) {
        $arg_class .= ' navigation-item-expand';
    }

    echo_item_begin($arg_class, $parent_url.'/'.$url.'/', $title);

    if (count($items) > 0) {
        echo('<ul>');
        usort($items, 'cmp_menu_items');
        foreach ($items as $i) {
            build_menu($parent_url.'/'.$url, $i);
        }
        echo('</ul>');
    }

    echo_item_end();
}

function print_menu(){
    global $_main_menu;
    $root = array_shift($_main_menu);
    echo_item_begin('normal', '/', $root['title']);
    echo_item_end();

    usort($root['items'], 'cmp_menu_items');
    foreach ($root['items'] as $menu_item) {
        build_menu('', $menu_item);
    }
}
?>