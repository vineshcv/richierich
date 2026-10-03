#!/usr/bin/env python3
"""Add gzip and static cache rules if they are missing. Restore on nginx -t failure."""
import glob
import shutil
import subprocess
from pathlib import Path

gzip_conf = Path("/etc/nginx/conf.d/rr-gzip.conf")
gzip_body = """gzip_vary on;
gzip_proxied any;
gzip_comp_level 5;
gzip_min_length 256;
gzip_types text/plain text/css text/javascript application/javascript application/json image/svg+xml application/xml;
"""

site = None
for pattern in ("/etc/nginx/sites-enabled/*", "/etc/nginx/conf.d/*.conf"):
    for path in glob.glob(pattern):
        text = Path(path).read_text(errors="replace")
        if "richierichdress.com" in text or "richierich" in text and "server_name" in text:
            site = Path(path)
            break
    if site:
        break

print("SITE", site)

existing_gzip = gzip_conf.exists()
print("GZIP_PRESENT", existing_gzip)

if not existing_gzip:
    gzip_conf.write_text(gzip_body)
    print("GZIP_ADDED")

if site is None:
    print("NO_SITE")
else:
    text = site.read_text()
    marker = "deploy/nginx-perf.conf"
    if marker in text or "rr-static-cache" in text:
        print("CACHE_PRESENT")
    else:
        snippet = Path("/var/www/richierich/deploy/nginx-perf.conf").read_text()
        block = "\n    # rr-static-cache\n    include " + marker + ";\n"
        # Prefer include so the repo file is the source. The include path is
        # relative to the nginx prefix unless absolute.
        include_line = "\n    # rr-static-cache\n    include /var/www/richierich/deploy/nginx-perf.conf;\n"
        # Insert before the closing brace of the server block that names this site.
        lines = text.splitlines(keepends=True)
        depth = 0
        insert_at = None
        in_target = False
        target_depth = None
        for i, line in enumerate(lines):
            if "server_name" in line and ("richierichdress.com" in line or "richierich" in line):
                in_target = True
                target_depth = depth
            for ch in line:
                if ch == "{":
                    depth += 1
                elif ch == "}":
                    depth -= 1
                    if in_target and target_depth is not None and depth == target_depth - 1:
                        insert_at = i
                        in_target = False
                        break
            if insert_at is not None:
                break
        if insert_at is None:
            print("INSERT_FAILED")
        else:
            backup = Path("/root/richierich.nginx.bak-perf")
            shutil.copy(site, backup)
            lines.insert(insert_at, include_line)
            site.write_text("".join(lines))
            print("CACHE_ADDED", insert_at, "backup", backup)

test = subprocess.run(["nginx", "-t"], capture_output=True, text=True)
print(test.stdout)
print(test.stderr)
if test.returncode != 0:
    backup = Path("/root/richierich.nginx.bak-perf")
    if site and backup.exists():
        shutil.copy(backup, site)
    if gzip_conf.exists() and not existing_gzip:
        gzip_conf.unlink()
    print("RESTORED")
    raise SystemExit(1)
subprocess.run(["systemctl", "reload", "nginx"], check=True)
print("NGINX_OK")
