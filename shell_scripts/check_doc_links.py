#!/usr/bin/env python3
"""
Check Markdown links in Documentacion_Obix.

- Validates Markdown links: [text](target)
- Validates Obsidian wikilinks: [[path/to/note]]
- Ignores links inside fenced code blocks (```...```)
- Reports missing local files and unreachable HTTP(S) links
"""

from __future__ import annotations

import argparse
import json
import re
import ssl
import urllib.error
import urllib.parse
import urllib.request
from concurrent.futures import ThreadPoolExecutor, as_completed
from dataclasses import dataclass, asdict
from pathlib import Path
from typing import Iterable


MD_LINK_RE = re.compile(r"(?<!!)\[[^\]]*\]\(([^)]+)\)")
WIKI_LINK_RE = re.compile(r"\[\[([^\]]+)\]\]")


@dataclass
class LinkEntry:
    type: str
    file: str
    line: int
    target: str


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Check docs links in Documentacion_Obix.")
    parser.add_argument(
        "--root",
        default=".",
        help="Repository root path. Default: current directory.",
    )
    parser.add_argument(
        "--docs-dir",
        default="documentacion/Documentacion_Obix",
        help="Docs directory to scan, relative to root.",
    )
    parser.add_argument(
        "--output",
        default="documentacion/link_check_report.json",
        help="Output JSON file path, relative to root.",
    )
    parser.add_argument(
        "--timeout",
        type=float,
        default=12.0,
        help="HTTP timeout in seconds. Default: 12.",
    )
    parser.add_argument(
        "--workers",
        type=int,
        default=8,
        help="Max parallel workers for HTTP checks. Default: 8.",
    )
    return parser.parse_args()


def iter_entries(root: Path, docs_dir: Path) -> Iterable[LinkEntry]:
    md_files = [p for p in docs_dir.rglob("*.md") if p.is_file()]
    for file_path in md_files:
        rel = str(file_path.relative_to(root))
        lines = file_path.read_text(encoding="utf-8", errors="ignore").splitlines()
        in_code_block = False
        for idx, line in enumerate(lines, start=1):
            if line.strip().startswith("```"):
                in_code_block = not in_code_block
                continue
            if in_code_block:
                continue

            for match in MD_LINK_RE.finditer(line):
                target = match.group(1).strip()
                if target.startswith("<") and target.endswith(">"):
                    target = target[1:-1].strip()
                yield LinkEntry(type="markdown", file=rel, line=idx, target=target)

            for match in WIKI_LINK_RE.finditer(line):
                target = match.group(1).strip()
                yield LinkEntry(type="wiki", file=rel, line=idx, target=target)


def check_http_targets(urls: set[str], timeout: float, workers: int) -> dict[str, tuple[bool, str]]:
    ssl_ctx = ssl.create_default_context()
    ssl_ctx.check_hostname = False
    ssl_ctx.verify_mode = ssl.CERT_NONE

    def check_one(url: str) -> tuple[bool, str]:
        try:
            req = urllib.request.Request(url, method="HEAD", headers={"User-Agent": "Mozilla/5.0"})
            with urllib.request.urlopen(req, timeout=timeout, context=ssl_ctx) as response:
                code = getattr(response, "status", 200)
                return code < 400, f"HTTP {code}"
        except Exception:
            try:
                req = urllib.request.Request(url, method="GET", headers={"User-Agent": "Mozilla/5.0"})
                with urllib.request.urlopen(req, timeout=timeout, context=ssl_ctx) as response:
                    code = getattr(response, "status", 200)
                    return code < 400, f"HTTP {code}"
            except urllib.error.HTTPError as err:
                return False, f"HTTP {err.code}"
            except Exception as err:  # pylint: disable=broad-except
                return False, f"{type(err).__name__}: {err}"

    results: dict[str, tuple[bool, str]] = {}
    with ThreadPoolExecutor(max_workers=workers) as executor:
        futures = {executor.submit(check_one, url): url for url in urls}
        for future in as_completed(futures):
            url = futures[future]
            results[url] = future.result()
    return results


def resolve_wikilink(docs_dir: Path, target: str) -> bool:
    core = target.split("|", 1)[0].split("#", 1)[0].strip()
    if not core:
        return False

    candidate = docs_dir / urllib.parse.unquote(core)
    candidates = [candidate] if candidate.suffix else [candidate.with_suffix(".md"), candidate / "index.md"]
    return any(path.exists() for path in candidates)


def resolve_local_markdown_link(root: Path, source_rel_file: str, target: str) -> bool:
    parsed = urllib.parse.urlparse(target)
    if parsed.scheme in ("http", "https"):
        return True
    if target.startswith("#") or target.startswith("mailto:") or target.startswith("tel:"):
        return True
    if target.startswith("javascript:"):
        return False

    core = target.split("#", 1)[0].split("?", 1)[0].strip()
    if not core:
        return True
    source_abs = root / source_rel_file
    resolved = (source_abs.parent / urllib.parse.unquote(core)).resolve()
    return resolved.exists()


def main() -> int:
    args = parse_args()
    root = Path(args.root).resolve()
    docs_dir = (root / args.docs_dir).resolve()
    output_path = (root / args.output).resolve()

    if not docs_dir.exists():
        print(f"ERROR: docs directory not found: {docs_dir}")
        return 2

    entries = list(iter_entries(root, docs_dir))
    md_files = [p for p in docs_dir.rglob("*.md") if p.is_file()]
    http_targets = {
        entry.target
        for entry in entries
        if entry.type == "markdown" and urllib.parse.urlparse(entry.target).scheme in ("http", "https")
    }
    http_results = check_http_targets(http_targets, timeout=args.timeout, workers=max(1, args.workers))

    errors: list[dict] = []
    for entry in entries:
        if entry.type == "wiki":
            if not resolve_wikilink(docs_dir, entry.target):
                errors.append({**asdict(entry), "error": "wikilink apunta a fichero inexistente"})
            continue

        parsed = urllib.parse.urlparse(entry.target)
        if parsed.scheme in ("http", "https"):
            ok, message = http_results.get(entry.target, (False, "sin resultado"))
            if not ok:
                errors.append({**asdict(entry), "error": message})
            continue

        if entry.target.startswith("javascript:"):
            errors.append({**asdict(entry), "error": "javascript link no verificable"})
            continue

        if not resolve_local_markdown_link(root, entry.file, entry.target):
            errors.append({**asdict(entry), "error": "ruta local no existe"})

    errors.sort(key=lambda item: (item["file"], item["line"], item["target"]))
    report = {
        "checked_files": len(md_files),
        "total_links_found": len(entries),
        "errors_found": len(errors),
        "errors": errors,
    }

    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")

    print(f"checked_files={report['checked_files']}")
    print(f"total_links_found={report['total_links_found']}")
    print(f"errors_found={report['errors_found']}")
    print(f"report={output_path}")
    for error in errors:
        print(
            f"{error['file']}:{error['line']} [{error['type']}] -> "
            f"{error['target']} :: {error['error']}"
        )

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
