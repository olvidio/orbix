#!/usr/bin/env python3
"""
Detecta usos de "src" en frontend/<modulo> que no pasan por PostRequest.

Uso:
  python tools/qa/detect_frontend_src_without_postrequest.py ubis
"""

from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path


POSTREQUEST_PATTERNS = [
    re.compile(r"PostRequest::getDataFromUrl\s*\("),
    re.compile(r"PostRequest::getDataMultipart\s*\("),
    re.compile(r"PostRequest::getContent\s*\("),
]

SRC_USAGE_PATTERNS = [
    re.compile(r"\bsrc\\[A-Za-z0-9_\\]+"),   # use src\...
    re.compile(r"(^|['\"\s])\/src\/"),       # '/src/...'
    re.compile(r"\bsrc\/"),                  # src/...
]


def has_postrequest_call(line: str) -> bool:
    return any(p.search(line) for p in POSTREQUEST_PATTERNS)


def has_src_usage(line: str) -> bool:
    return any(p.search(line) for p in SRC_USAGE_PATTERNS)


def should_ignore_line(line: str) -> bool:
    stripped = line.strip()
    if not stripped:
        return True
    if stripped.startswith("//") or stripped.startswith("#"):
        return True
    if stripped.startswith("*") or stripped.startswith("/*") or stripped.startswith("*/"):
        return True
    if "use frontend\\shared\\PostRequest;" in stripped:
        return True
    return False


def scan_file(path: Path) -> list[tuple[int, str]]:
    issues: list[tuple[int, str]] = []
    try:
        text = path.read_text(encoding="utf-8", errors="replace")
    except OSError:
        return issues

    for n, line in enumerate(text.splitlines(), start=1):
        if should_ignore_line(line):
            continue
        if has_src_usage(line) and not has_postrequest_call(line):
            issues.append((n, line.rstrip()))
    return issues


def main() -> int:
    parser = argparse.ArgumentParser(description="Detecta uso de src sin PostRequest en frontend/<modulo>.")
    parser.add_argument("module", help="Nombre del modulo dentro de frontend (ej: ubis)")
    args = parser.parse_args()

    root = Path.cwd()
    module_dir = root / "frontend" / args.module
    if not module_dir.exists() or not module_dir.is_dir():
        print(f"[ERROR] No existe el modulo: {module_dir}", file=sys.stderr)
        return 2

    files = sorted(
        [
            *module_dir.rglob("*.php"),
            *module_dir.rglob("*.phtml"),
            *module_dir.rglob("*.js"),
            *module_dir.rglob("*.ts"),
        ]
    )

    total_issues = 0
    files_with_issues = 0

    for file_path in files:
        issues = scan_file(file_path)
        if not issues:
            continue
        files_with_issues += 1
        total_issues += len(issues)
        rel = file_path.relative_to(root)
        print(f"\n{rel}")
        for line_no, text in issues:
            print(f"  L{line_no}: {text}")

    print("\n---")
    print(f"Modulo: {args.module}")
    print(f"Ficheros analizados: {len(files)}")
    print(f"Ficheros con posibles usos directos de src: {files_with_issues}")
    print(f"Coincidencias totales: {total_issues}")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())

