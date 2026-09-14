#!/usr/bin/env python3
"""Build a WordPress-compatible theme ZIP and versioned update manifest."""
import json
import pathlib
import re
import subprocess
import sys
import zipfile
root = pathlib.Path(__file__).resolve().parents[1]
theme = root / 'bottomline'
headers = (theme / 'style.css').read_text()
def header(name):
    found = re.search(r'^' + re.escape(name) + r':\s*(.+)$', headers, re.M)
    if not found:
        raise SystemExit('Missing theme header: ' + name)
    return found.group(1).strip()
version = header('Version')
if not re.fullmatch(r'\d+\.\d+\.\d+', version):
    raise SystemExit('Version must be a stable x.y.z version')
if len(sys.argv) > 1 and sys.argv[1] != 'v' + version:
    raise SystemExit('Git tag does not match the theme Version header')
for path in theme.rglob('*.php'):
    subprocess.run(['php', '-l', str(path)], check=True, stdout=subprocess.DEVNULL)
for path in (theme / 'assets/js').rglob('*.js'):
    subprocess.run(['node', '--check', str(path)], check=True)
dist = root / 'dist'
dist.mkdir(exist_ok=True)
with zipfile.ZipFile(dist / 'bottomline.zip', 'w', zipfile.ZIP_DEFLATED, compresslevel=9) as archive:
    for path in sorted(theme.rglob('*')):
        if path.is_file() and path.name != '.DS_Store':
            archive.write(path, path.relative_to(root))
history = json.loads((theme / 'release-notes.json').read_text())
changes = history.get(version, ['Theme maintenance update.'])
(dist / 'release-notes.md').write_text('\n'.join('- ' + item for item in changes) + '\n')
(dist / 'bottomline-update.json').write_text(json.dumps({'version': version, 'requires': header('Requires at least'), 'requires_php': header('Requires PHP'), 'changes': changes}, indent=2) + '\n')
print('Built bottomline.zip and bottomline-update.json for v' + version)
