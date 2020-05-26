#!/usr/bin/env python3
# -*- coding: utf-8 -*-
from os import listdir
from os.path import join, isdir, exists, basename
from lxml.html import parse
# config
SITE_ROOT_DIR = 'D:\\bakup\\oit.29.ru\\oit.r29.ru_20200526'
SITE_INDEXES = 'index.html index.php'
SITE_EXCLUDE_DIRS = 'spec'

def process_index(path, index, level):
    title = parse(join(path, index)).getroot().xpath('//*[@class="widget widget-breadcrumbs"]/div/div[last()]/span')[0].text_content()
    print('{}/{}/ "{}"'.format("\t" * level, basename(path), title))

def process_folder(path, level):
    level += 1
    # process index file
    for index in SITE_INDEXES.split(' '):
        if exists(join(path, index)):
            process_index(path, index, level)
    # go next level
    for f in listdir(path):
        if f in SITE_EXCLUDE_DIRS.split(' '):
            continue
        if isdir(join(path, f)):
            process_folder(join(path, f), level)

level = -1
process_folder(SITE_ROOT_DIR, level)