#!/usr/bin/env node
/**
 * slickgrid (npm) declara //# sourceMappingURL en dist/browser pero no incluye
 * todos los .map → el navegador muestra 404 en consola. Crear mapas vacíos
 * válidos solo si el fichero .map referenciado no existe.
 */
'use strict';

const fs = require('fs');
const path = require('path');

const browserRoot = path.join(__dirname, '..', 'node_modules', 'slickgrid', 'dist', 'browser');
const emptyMapBody =
  JSON.stringify({ version: 3, sources: [], names: [], mappings: '' }) + '\n';

function walk(dir) {
  if (!fs.existsSync(dir)) {
    return;
  }
  for (const ent of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, ent.name);
    if (ent.isDirectory()) {
      walk(p);
    } else if (ent.isFile() && ent.name.endsWith('.js')) {
      ensureMapForJs(p);
    }
  }
}

function ensureMapForJs(jsPath) {
  let text;
  try {
    text = fs.readFileSync(jsPath, 'utf8');
  } catch {
    return;
  }
  const m = text.match(/\/\/# sourceMappingURL=([^\s\r\n]+)\s*$/m);
  if (!m) {
    return;
  }
  const mapRel = m[1];
  if (mapRel.includes(':') || mapRel.startsWith('/')) {
    return;
  }
  const mapPath = path.join(path.dirname(jsPath), mapRel);
  if (fs.existsSync(mapPath)) {
    return;
  }
  fs.mkdirSync(path.dirname(mapPath), { recursive: true });
  fs.writeFileSync(mapPath, emptyMapBody, 'utf8');
  process.stdout.write(`ensure-slickgrid-sourcemaps: created ${path.relative(process.cwd(), mapPath)}\n`);
}

walk(browserRoot);
