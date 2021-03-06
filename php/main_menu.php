<?
require_once('menu.php');

function url_join($lpath, $rpath) {
    if (strlen($lpath) == 0 && strlen($rpath) == 0) return '';
    elseif (strlen($lpath) == 0 && strlen($rpath) > 0) return trim($rpath, '/');
    elseif (strlen($lpath) > 0 && strlen($rpath) == 0) return trim($lpath, '/');
    else return (trim($lpath, '/').'/'.trim($rpath, '/'));
}

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

function php_self_to_uri() {
    // remove script file name from uri, and return array of parts of uri exploded by '/'
    $res = explode('/', $_SERVER['PHP_SELF']);
    // remove first empty item (before first '/', eg '/foo/bar.php')
    array_shift($res);
    // remove script name
    array_pop($res);
    return $res;
}

function is_part_uri($self_uri) {
    $res = true;
    $a_req_uri = php_self_to_uri();
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

function build_menu($back_path, $parent_url, $menu_item) {
    $title = $menu_item['title'];
    $items = $menu_item['items'];
    $url = $menu_item['url'];
    $arg_class = 'normal';

    $menu_url = url_join($parent_url, $url);

    if (is_part_uri($menu_url)) {
        $arg_class = 'selected';
    }

    if (count($items) > 0) {
        $arg_class .= ' navigation-item-expand';
    }
    echo_item_begin($arg_class, url_join($back_path, $menu_url).'/', $title);

    if (count($items) > 0) {
        echo('<ul>');
        usort($items, 'cmp_menu_items');
        foreach ($items as $i) {
            build_menu($back_path, $menu_url, $i);
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
    $a_req_uri = php_self_to_uri();
    $max_level = count($a_req_uri);
    $spec_url = '';
    // spec
    if ($max_level > 0 && $a_req_uri[0] == 'spec') {
        // array_shift($a_req_uri);
        // $max_level = count($a_req_uri);
        $spec_url = '/spec';
    }
    $back_path = str_repeat('../', $max_level);
    if ($max_level == 0 || (strlen($spec_url) > 0 && $max_level == 1)) $arg_class = 'selected';
    // echo root menu item - Main page
    echo_item_begin($arg_class, $spec_url.'/', $root['title']);
    echo_item_end();

    usort($root['items'], 'cmp_menu_items');
    foreach ($root['items'] as $menu_item) {
        build_menu($back_path, $spec_url, $menu_item);
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
    $a_req_uri = php_self_to_uri();

    $max_level = count($a_req_uri);
    // spec
    if ($max_level > 0 && $a_req_uri[0] == 'spec') {
        array_shift($a_req_uri);
        $max_level = count($a_req_uri);
    }
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

function print_js_tag() {
    global $_js_file_name;
    $a_req_uri = php_self_to_uri();

    $max_level = count($a_req_uri);
    // spec
    if ($max_level > 0 && $a_req_uri[0] == 'spec') {
        array_shift($a_req_uri);
        $max_level = count($a_req_uri);
    }
    if ($max_level == 0) {
        $back_path = '';
    }
    else {
        $back_path = str_repeat('../', $max_level);
    }
    echo('<script src="'.$back_path.'js/'.$_js_file_name.'.js"></script>');
}

// echo script tag
print_js_tag();
?>