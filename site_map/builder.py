#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
    Build a php array, contents site menu
"""

from os import listdir, rename
from os.path import join, isdir, exists, basename
from html import escape
from lxml.html import parse
from lxml.etree import PI

# config
#SITE_ROOT_DIR = 'C:\\www\\oit.r29.ru'
SITE_ROOT_DIR = 'D:\\bakup\\oit.29.ru\\oit.r29.ru_20200601\\spec'
SITE_INDEXES = 'index.html index.php'
SITE_EXCLUDE_DIRS = 'spec'
SITE_MENU_TREE_EXCLUDE_DIRS = ''
SITE_RENAME_TO_PHP = True
SITE_CREATE_MENU_PHP = False
# end config

_result = "$_main_menu = array(\n"

def remove_child_nodes(node):
    for child in list(node):
        node.remove(child)

def process_index(path, index, level):
    # parse dom tree from index html file
    tree = parse(join(path, index))
    # retrieve a title of page
    #title = tree.getroot().xpath('//*[@class="widget widget-breadcrumbs"]/div/div[last()]/span')[0].text_content()
    title = tree.find('//*[@class="widget widget-breadcrumbs"]/div/div[last()]/span').text_content()
    print('{}{}/{}/ "{}"'.format(level, "\t" * level, basename(path), title))
    if SITE_RENAME_TO_PHP:
        # insert import clause
        prefix = '../' * level
        tree.find('head').append(PI('php', 'require_once("{}main_menu.php"); ?'.format(prefix)))
        # replace breadcrumb content with php function call
        node = tree.find('//div[@class="widget widget-breadcrumbs"]/div')
        remove_child_nodes(node)
        node.append(PI('php', 'print_breadcrumbs(); ?'))
        # replace menu content with php function call
        node = tree.find('//ul[@class="navigation"]')
        remove_child_nodes(node)
        node.append(PI('php', 'print_menu(); ?'))
        # move original file to bakup
        rename(join(path, index), join(path, 'index.orig'))
        # save modified dom tree
        tree.write(join(path, 'index.php'), encoding = 'UTF-8', xml_declaration= True, method= 'html')
    return title

def add_menu_item(path, title, level):
    pass

def process_folder(result, path, level):
    level += 1
    # process index file
    title = ''
    has_index = False
    for index in SITE_INDEXES.split(' '):
        if exists(join(path, index)):
            has_index = True
            title = process_index(path, index, level)
            break
    # build menu item array        
    if has_index:
        #result += '{}"{}" => array(\n'.format('\t' * level, basename(path))
        result += '{}array(\n'.format('\t' * level)
        result += '{}"title" => "{}",\n'.format('\t' *(level + 1), escape(title))
        result += '{}"url" => "{}",\n'.format('\t' *(level + 1), basename(path))
        result += '{}"items" => array(\n'.format('\t' *(level + 1))

        # go next level
        for f in listdir(path):
            if f not in SITE_EXCLUDE_DIRS.split(' '):
                if isdir(join(path, f)):
                    result = process_folder(result, join(path, f), level)

        result += '{}),\n'.format('\t' * (level + 1)) # close items
        result += '{}),\n'.format('\t' * level) # close menu item
    return result

level = -1
_result = process_folder(_result, SITE_ROOT_DIR, level)
_result += ');\n'
#print(_result)
if SITE_CREATE_MENU_PHP:
    with open('php\\menu.php', mode = 'w', encoding = 'UTF-8') as fo:
        fo.write('<?\n')
        fo.write(_result)
        fo.write('?>')