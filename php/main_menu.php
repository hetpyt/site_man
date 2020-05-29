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

function php_self_to_uri($php_self) {
    // remove script file name from uri, and return array of parts of uri exploded by '/'
    $res = explode('/', $php_self);
    // remove first empty item (before first '/', eg '/foo/bar.php')
    array_shift($res);
    // remove script name
    array_pop($res);
    return $res;
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

function find_menu_item($arr_menu, $url) {
    foreach ($arr_menu as $item) {
        if ($item['url'] == $url) {
            return $item;
        }
    }
    return null;
}

function echo_bc_item($title, $url, $last) {
    $class = '';
    echo('<div itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb">');
    if (!$last) {
        echo('<a class="page" href="'.$url.'" itemprop="url">');
    }
    else {
        $class = 'class="page"';
    }

    echo('<span itemprop="title" '.$class.'>'.$title.'</span>');

    if (!$last) {
        echo('</a>');
    }
    echo('</div>');
}

function echo_bc_separator() {
    echo('<span class="separator">&gt;</span>');
}

function print_breadcrumbs() {
    global $_main_menu;
    $a_req_uri = php_self_to_uri($_SERVER['PHP_SELF']);

    $max_level = count($a_req_uri);
    if ($max_level == 0) {
        // root directory
        echo_bc_item($_main_menu[0]['title'], '', true);
    }
    else {
        $base_path = str_repeat('../', $max_level);
        $rel_path = '';
        echo_bc_item($_main_menu[0]['title'], $base_path, false);
        $cur_level_nemu_items = $_main_menu[0]['items'];
        foreach ($a_req_uri as $level => $part) {
            $menu_item = find_menu_item($cur_level_nemu_items, $part);
            if (!$menu_item) break;
            echo_bc_separator();
            $rel_path .= $menu_item['url'].'/';
            $last = ($max_level - 1 == $level);
            echo_bc_item($menu_item['title'], $base_path.$rel_path, $last);
            $cur_level_nemu_items = $menu_item['items'];
        }
    }
}
?>