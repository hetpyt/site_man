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

function is_part_uri($req_uri, $self_uri) {
    $res = true;
    $a_req_uri = explode('/', $req_uri);
    $a_self_uri = explode('/', $self_uri);
    if (count($a_req_uri) >= count($a_self_uri)) {
        foreach ($a_self_uri as $index => $part) {
            if ($part != $a_req_uri[$index]) {
                $res = false;
                break;
            }
        }
    }
    else {
        $res = false;
    }
    return $res;
}

function build_menu($parent_url, $menu_item) {
    $title = $menu_item['title'];
    $items = $menu_item['items'];
    $url = $menu_item['url'];
    $arg_class = 'normal';

    if (is_part_uri($_SERVER['REQUEST_URI'], $parent_url.'/'.$url)) {
        $arg_class = 'selected';
    }

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

function print_menu() {
    global $_main_menu;
    //$root = array_shift($_main_menu);
    $root = $_main_menu[0];
    $arg_class = 'normal';
    if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '') $arg_class = 'selected';
    echo_item_begin($arg_class, '/', $root['title']);
    echo_item_end();

    usort($root['items'], 'cmp_menu_items');
    foreach ($root['items'] as $menu_item) {
        build_menu('', $menu_item);
    }
}

function print_breadcrumbs() {
    global $_main_menu;
    $a_req_uri = explode('/', $_SERVER['REQUEST_URI']);
    $cur_level_nemu = $_main_menu[0];
    foreach ($a_req_uri as $level => $part) {
        if ($level == 0) continue;
        //$cur_level_nemu[$part][]
    }
}
?>