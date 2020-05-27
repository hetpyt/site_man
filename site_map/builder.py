#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
    Build a php array, contents site menu
"""

from os import listdir
from os.path import join, isdir, exists, basename
from html import escape
from lxml.html import parse
# config
#SITE_ROOT_DIR = 'C:\\www\\oit.r29.ru'
SITE_ROOT_DIR = 'D:\\bakup\\oit.29.ru\\oit.r29.ru_20200526'
SITE_INDEXES = 'index.html index.php'
SITE_EXCLUDE_DIRS = 'spec'
# end config
_result = "$main_menu = array(\n"

def process_index(path, index, level):
    title = parse(join(path, index)).getroot().xpath('//*[@class="widget widget-breadcrumbs"]/div/div[last()]/span')[0].text_content()
    print('{}/{}/ "{}"'.format("\t" * level, basename(path), title))
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
                    result = process_folder(result, join(path, f), level+1)

        result += '{}),\n'.format('\t' * (level + 1)) # close items
        result += '{}),\n'.format('\t' * level) # close menu item
    return result

level = -1
_result = process_folder(_result, SITE_ROOT_DIR, level)
_result += ');\n'
#print(_result)
with open('php\main_menu.php', mode = 'w', encoding = 'UTF8') as fo:
    fo.write('<?\n')
    fo.write(_result)
    fo.write('?>')