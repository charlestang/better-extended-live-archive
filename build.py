#!/usr/bin/env python3

import os
import sys
import shutil
from os.path import join
import re

# do some clean up works and prepare the build directory. 
def clean(build_path: str) -> None:
    if os.path.exists(build_path) :
        print("Target path `build/` is already exists. Try to remove it...")
        shutil.rmtree(build_path)
        print("Removed and re-create a new one...")
    else:
        print("Create a target path `build/` ...")
    os.mkdir(build_path)
    return    

# copy php files.
def copy_file(src_path: str, build_path: str) -> None:
    print("Copy plugin files...")
    shutil.copy(join(src_path, 'better-extended-live-archive.php'), build_path)
    shutil.copytree(join(src_path, 'views'), join(build_path, 'views'))
    shutil.copytree(join(src_path, 'js'), join(build_path, 'js'))
    shutil.copytree(join(src_path, 'css'), join(build_path, 'css'))
    shutil.copytree(join(src_path, 'classes'), join(build_path, 'classes'))
    os.mkdir(join(build_path, 'cache'))
    print("Done.")
    return

# generate readme.txt from README.md
def convert_readme(src_path: str, build_path: str) -> None:
    src_file = join(src_path, 'README.md')
    dst_file = join(build_path, 'readme.txt')

    content = []
    with open(src_file, 'r') as f:
        for line in f:
            match = re.match(r"^(#{1,3})\s*(.*)", line)
            if not match :
                content.append(line.strip())
            else :
                title_symbol = match.group(1)
                level = 4 - len(title_symbol)
                content.append("=" * level + " " + match.group(2) + " " + "=" * level)
    f.close()
    with open(dst_file, 'w') as f:
        f.write("\n".join(content))
    f.close()
    
if __name__ == '__main__':
    src_path = os.path.dirname(os.path.abspath(sys.argv[0]))
    build_path = os.path.join(src_path, 'build')
    
    if len(sys.argv) > 1 and 'clean' == sys.argv[1] :
        clean(build_path)
    else :
        clean(build_path)
        copy_file(src_path, build_path)
        convert_readme(src_path, build_path)
    
    sys.exit(0)