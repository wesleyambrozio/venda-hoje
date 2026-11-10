#!/bin/bash
set -euo pipefail
CONF=/etc/nginx/sites-available/vendahoje
cp "$CONF" "${CONF}.bak.fix"
python3 - <<'PY'
from pathlib import Path
p = Path('/etc/nginx/sites-available/vendahoje')
text = p.read_text()
text = text.replace('location ~ /config/ { deny all; }', '# /config is an app route (ConfigController)')
text = text.replace('location ~ ^/(vendor|config|database)/ { deny all; }', 'location ~ ^/(vendor|database)/ { deny all; }')
p.write_text(text)
print('patched')
PY
nginx -t
systemctl reload nginx
echo NGINX_OK
grep -n 'config\|vendor\|database' "$CONF"
