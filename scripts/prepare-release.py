#!/usr/bin/env python3
"""Assign a new patch version when a main-branch push reuses a published version."""
import pathlib
import re
import subprocess

root = pathlib.Path(__file__).resolve().parents[1]
style = root / 'bottomline/style.css'
source = style.read_text()
version = re.search(r'^Version: (\d+\.\d+\.\d+)$', source, re.M).group(1)
tags = subprocess.check_output(['git', 'tag', '--list', 'v*'], cwd=root, text=True).splitlines()
published = [tuple(map(int, tag[1:].split('.'))) for tag in tags if re.fullmatch(r'v\d+\.\d+\.\d+', tag)]
if published and tuple(map(int, version.split('.'))) <= max(published):
    major, minor, patch = max(published)
    version = f'{major}.{minor}.{patch + 1}'
    style.write_text(re.sub(r'^Version: .+$', 'Version: ' + version, source, flags=re.M))
print('v' + version)
