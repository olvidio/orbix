#!/usr/bin/env python3
"""
Check Orbix menu/controller links using an authenticated session.

Modes:
1) docs mode: extract controller paths from markdown and request them.
2) crawl mode: start from a menu URL and follow discovered links.
"""

from __future__ import annotations

import argparse
import json
import re
from dataclasses import dataclass, asdict
from pathlib import Path
from typing import Iterable
from urllib.parse import urljoin, urlparse

import requests


CONTROLLER_RE = re.compile(r"`(apps/[a-zA-Z0-9_./-]+\.php)`")
PARAMS_RE = re.compile(r"`([^`=]+=[^`]+)`")
HREF_RE = re.compile(r'href=["\']([^"\']+)["\']', re.IGNORECASE)


@dataclass
class CheckResult:
    source: str
    url: str
    status: int | None
    ok: bool
    kind: str
    detail: str


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Check Orbix links with optional crawling.")
    parser.add_argument("--base-url", default="http://orbix.docker:8003/orbix/", help="Base Orbix URL.")
    parser.add_argument("--phpsessid", default="", help="Authenticated PHPSESSID value.")
    parser.add_argument("--esquema", default="", help="Login schema name (e.g. H-dlbv).")
    parser.add_argument("--username", default="", help="Login username.")
    parser.add_argument("--password", default="", help="Login password.")
    parser.add_argument("--verification-code", default="", help="2FA code if required.")
    parser.add_argument("--timeout", type=float, default=15.0, help="Request timeout in seconds.")
    parser.add_argument(
        "--docs-file",
        default="docs/legacy/obix/12. Estudios y STGR.md",
        help="Markdown file to extract controllers from (docs mode).",
    )
    parser.add_argument(
        "--start-url",
        default="",
        help="Start URL for crawl mode (example: index.php?id_grupmenu=13).",
    )
    parser.add_argument("--max-pages", type=int, default=40, help="Max pages to visit in crawl mode.")
    parser.add_argument(
        "--output",
        default="docs/dev/reports/orbix_runtime_link_report.json",
        help="Output report path (relative to repo root).",
    )
    parser.add_argument("--root", default=".", help="Repository root.")
    return parser.parse_args()


def is_login_page(html: str) -> bool:
    lowered = html.lower()
    signals = ["class=\"login\"", "name=\"username\"", "name=\"password\"", "form-signin"]
    return any(signal in lowered for signal in signals)


def detect_php_error_marker(html: str) -> str:
    lowered = html.lower()
    patterns = [
        "fatal error",
        "uncaught exception",
        "warning:",
        "parse error",
        "stack trace",
        "whoops",
    ]
    for pattern in patterns:
        pos = lowered.find(pattern)
        if pos != -1:
            start = max(0, pos - 60)
            end = min(len(html), pos + 140)
            snippet = " ".join(html[start:end].split())
            return snippet
    return ""


def normalized_base(base_url: str) -> str:
    return base_url if base_url.endswith("/") else base_url + "/"


def extract_doc_targets(markdown_path: Path, base_url: str) -> list[tuple[str, str]]:
    text = markdown_path.read_text(encoding="utf-8", errors="ignore")
    lines = text.splitlines()
    targets: list[tuple[str, str]] = []
    for idx, line in enumerate(lines):
        for controller in CONTROLLER_RE.findall(line):
            params = ""
            if idx + 1 < len(lines) and "Parámetros" in lines[idx + 1]:
                pairs = PARAMS_RE.findall(lines[idx + 1])
                params = "&".join(pairs).replace("&amp;", "&")
            url = urljoin(base_url, controller)
            if params:
                join_char = "&" if "?" in url else "?"
                url = f"{url}{join_char}{params}"
            targets.append((f"{markdown_path.name}:{idx + 1}", url))
    # dedupe preserving order
    seen: set[str] = set()
    deduped: list[tuple[str, str]] = []
    for source, url in targets:
        if url in seen:
            continue
        seen.add(url)
        deduped.append((source, url))
    return deduped


def request_url(session: requests.Session, source: str, url: str, timeout: float) -> CheckResult:
    try:
        response = session.get(url, timeout=timeout, allow_redirects=True)
        html = response.text or ""
        if is_login_page(html):
            return CheckResult(source, response.url, response.status_code, False, "auth", "sesion no valida/login")
        if response.status_code >= 400:
            return CheckResult(source, response.url, response.status_code, False, "http", f"HTTP {response.status_code}")
        marker = detect_php_error_marker(html)
        if marker:
            return CheckResult(
                source,
                response.url,
                response.status_code,
                False,
                "runtime",
                f"posible error PHP en respuesta: {marker}",
            )
        return CheckResult(source, response.url, response.status_code, True, "ok", "ok")
    except requests.RequestException as exc:
        return CheckResult(source, url, None, False, "request", f"{type(exc).__name__}: {exc}")


def should_follow(href: str, base_url: str) -> bool:
    if href.startswith("#") or href.startswith("javascript:") or href.startswith("mailto:"):
        return False
    absolute = urljoin(base_url, href)
    parsed = urlparse(absolute)
    base = urlparse(base_url)
    return parsed.netloc == base.netloc and parsed.path.startswith(base.path)


def extract_hrefs(html: str) -> Iterable[str]:
    return HREF_RE.findall(html)


def crawl_menu(session: requests.Session, start_url: str, base_url: str, max_pages: int, timeout: float) -> list[CheckResult]:
    queue = [start_url]
    visited: set[str] = set()
    results: list[CheckResult] = []
    while queue and len(visited) < max_pages:
        current = queue.pop(0)
        if current in visited:
            continue
        visited.add(current)

        result = request_url(session, "crawl", current, timeout)
        results.append(result)
        if not result.ok:
            continue

        try:
            html = session.get(current, timeout=timeout).text
        except requests.RequestException:
            continue

        for href in extract_hrefs(html):
            if not should_follow(href, base_url):
                continue
            target = urljoin(base_url, href)
            if target not in visited and target not in queue:
                queue.append(target)
    return results


def main() -> int:
    args = parse_args()
    root = Path(args.root).resolve()
    base_url = normalized_base(args.base_url)
    output_path = (root / args.output).resolve()
    docs_path = (root / args.docs_file).resolve()

    session = requests.Session()
    if args.phpsessid:
        session.cookies.set("PHPSESSID", args.phpsessid, path="/")
    elif args.esquema and args.username and args.password:
        login_url = urljoin(base_url, "index.php")
        # First request initializes session cookie consistently.
        session.get(login_url, timeout=args.timeout)
        payload = {
            "idioma": "",
            "esquema": args.esquema,
            "username": args.username,
            "password": args.password,
            "verification_code": args.verification_code,
        }
        login_response = session.post(login_url, data=payload, timeout=args.timeout, allow_redirects=True)
        if is_login_page(login_response.text):
            print("WARNING: login no completado; la sesion sigue en pantalla de acceso.")

    all_results: list[CheckResult] = []

    if docs_path.exists():
        targets = extract_doc_targets(docs_path, base_url)
        for source, url in targets:
            all_results.append(request_url(session, source, url, args.timeout))
    else:
        print(f"WARNING: docs file not found: {docs_path}")

    if args.start_url:
        start_url = urljoin(base_url, args.start_url)
        all_results.extend(crawl_menu(session, start_url, base_url, args.max_pages, args.timeout))

    report = {
        "base_url": base_url,
        "docs_file": str(docs_path),
        "start_url": args.start_url,
        "total_checked": len(all_results),
        "errors_found": sum(1 for result in all_results if not result.ok),
        "results": [asdict(result) for result in all_results],
    }

    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")

    print(f"total_checked={report['total_checked']}")
    print(f"errors_found={report['errors_found']}")
    print(f"report={output_path}")
    for result in all_results:
        if not result.ok:
            print(f"{result.source} -> {result.url} :: {result.kind} :: {result.detail}")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
