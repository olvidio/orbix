---
id: "menus.menus_exportar_ref_a_ficheros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Menús a/desde ficheros SQL"
capacidad: "menus.menus_exportar_ref_a_ficheros.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["exportar", "importar"]
endpoints: ["/src/menus/menus_exportar_ref_a_ficheros"]
estado_revision: "revisado"
---

# Flujo - Menús a/desde ficheros SQL

Endpoint `/src/menus/menus_exportar_ref_a_ficheros` con dos acciones:

- `accion=exportar`: genera scripts COPY en `log/menus/` (metamenús, ref, módulos).
- `accion=importar`: prepara `tot_menus.sql` desde `tot_menus_base.sql` y ejecuta `psql` (requiere
  sudoers `www-data` + `pg_hba` trust según comentarios del controller).

## Objetivo De Usuario

Respaldar o restaurar la referencia de menús vía ficheros SQL en disco.

## Punto De Entrada

Menú «importar desde ficheros». En `_referencia_menus.md` la URL apunta a
`src/menus/frontend/controller/menus_ficheros.php?accion=importar` (**muerto**); la ruta viva es
`/src/menus/menus_exportar_ref_a_ficheros`.

## Escenarios

### Exportar a ficheros

1. POST `accion=exportar` → escribe SQL bajo `log/menus/` (directorio con permiso escritura).

### Importar desde ficheros

1. POST `accion=importar` → sustituye `DIRBASE`, ejecuta `psql`, log en `log/menus/menus.log`.

## Endpoints Del Flujo

- `/src/menus/menus_exportar_ref_a_ficheros`

## Ruta de menú

- **Legacy:** sistema > menus > importar desde ficheros
- **Pills2:** sistema > menus > importar desde ficheros

(aviso: URL en BD = `menus_ficheros.php` muerto → usar `/src/menus/menus_exportar_ref_a_ficheros`)
