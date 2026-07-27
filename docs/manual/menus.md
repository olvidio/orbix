---
tipo: "manual_usuario"
modulo: "menus"
flujos: 15
estado_revision: "revisado_parcial"
---

# Manual De Usuario - menus

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Grupmenu

### Para Que Sirve

Alta/edición/baja de grupos de menú.

### Donde Entrar

- Grupmenu Lista (frontend/menus/controller/grupmenu_lista.php)
- Grupmenu Form (frontend/menus/controller/grupmenu_form.php)
- Entrada de menú (TablaDB, no `grupmenu_lista.php`):
- **Legacy:** sistema > usuarios web > grup menu
- **Pills2:** sistema > usuarios web > grup menu · ADMIN LOCAL > usuarios web > grup menu
- Pantallas `grupmenu_*`: sin entrada de menú en el índice.

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No encuentro el grupmenu`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `debe poner un nombre`

### Permisos

- Sin control propio; autorización vía menú de administración (`frontend/menus/controller/grupmenu_lista.php`).
- Menú administración usuarios web / grupmenu.
- Menú administración.
- Menú administración grupmenu.

### Referencias Internas

- Flujo: `menus.grupmenu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/grupmenu.md`

## Grupmenu Coleccion

### Para Que Sirve

Grupos e ítems autorizados para menú lateral en index.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Filtrado por `aux_grupmenu_rol` del rol de sesión y `PermisoMenu::visible()` en cada ítem.

### Referencias Internas

- Flujo: `menus.grupmenu_coleccion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/grupmenu_coleccion.md`

## Grupmenu Info

### Para Que Sirve

Precarga formulario edición grupmenu.

### Donde Entrar

- Grupmenu Form (frontend/menus/controller/grupmenu_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No encuentro el grupmenu`

### Permisos

- Menú administración grupmenu.

### Referencias Internas

- Flujo: `menus.grupmenu_info.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/grupmenu_info.md`

## Lista Meta Menus

### Para Que Sirve

Opciones de destino URL/módulo al editar un ítem (metamenús globales).

### Donde Entrar

- Menus Get (frontend/menus/controller/menus_get.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.lista_meta_menus.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/lista_meta_menus.md`

## Lista Templates

### Para Que Sirve

Listado de plantillas ref para importación.

### Donde Entrar

- Menus Importar Form (frontend/menus/controller/menus_importar_form.php)
- **Legacy:** sistema > menus > importar
- **Pills2:** sistema > menus > importar · ADMIN LOCAL > Importar menús · ADMIN GLOBAL > menus > importar

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.lista_templates.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/lista_templates.md`

## Menu

### Para Que Sirve

Alta, edición, copia, movimiento y borrado de entradas del árbol (`aux_menus`) enlazadas a un metamenu.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sistema > menus > seleccionar
- **Pills2:** sistema > menus > seleccionar · ADMIN GLOBAL > menus > seleccionar

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `hay un error. Debe indicar el destino`
- `No encuentro el menu`
- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

### Permisos

- Gestor de menús (`menus_que`); bits `perm_menu` en el propio ítem.

### Referencias Internas

- Flujo: `menus.menu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menu.md`

## Menu Mover

### Para Que Sirve

Cambiar grupmenu de un ítem desde ficha.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sistema > menus > seleccionar
- **Pills2:** sistema > menus > seleccionar · ADMIN GLOBAL > menus > seleccionar
- (Acción de ficha del gestor; no tiene menú propio.)

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `hay un error. Debe indicar el destino`
- `No encuentro el menu`
- `hay un error, no se ha guardado`

### Referencias Internas

- Flujo: `menus.menu_mover.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menu_mover.md`

## Menus Burger Layout

### Para Que Sirve

Árbol anidado + HTML utilidades para layouts modernos (H-dlpv).

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.menus_burger_layout.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_burger_layout.md`

## Menus Exportar

### Para Que Sirve

Persiste menú actual en tablas ref de BD pública.

### Donde Entrar

- Menus Exportar Form (frontend/menus/controller/menus_exportar_form.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** ADMIN LOCAL > Exportar menús · sistema > menus > Exportar · ADMIN GLOBAL > menus > Exportar

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `ya existe`

### Referencias Internas

- Flujo: `menus.menus_exportar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_exportar.md`

## Menús a/desde ficheros SQL

### Para Que Sirve

Respaldar o restaurar la referencia de menús vía ficheros SQL en disco.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sistema > menus > importar desde ficheros
- **Pills2:** sistema > menus > importar desde ficheros
- (aviso: URL en BD = `menus_ficheros.php` muerto → usar `/src/menus/menus_exportar_ref_a_ficheros`)

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú «importar desde ficheros» (operación de servidor).

### Referencias Internas

- Flujo: `menus.menus_exportar_ref_a_ficheros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_exportar_ref_a_ficheros.md`

## Menus Generar Txt

### Para Que Sirve

Regenera fichero de cadenas traducibles de etiquetas de menú.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sistema > traducciones > menus a texto
- **Pills2:** sistema > traducciones > menus a texto

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú `sistema > traducciones > menus a texto` (`_referencia_menus.md`).

### Referencias Internas

- Flujo: `menus.menus_generar_txt.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_generar_txt.md`

## Menus Get Page

### Para Que Sirve

Builder AJAX lista vs edición en gestor de menús.

### Donde Entrar

- Menus Get (frontend/menus/controller/menus_get.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No encuentro el menu`

### Referencias Internas

- Flujo: `menus.menus_get_page.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_get_page.md`

## Menus Importar

### Para Que Sirve

Sustituye menús locales por una plantilla seleccionada.

### Donde Entrar

- Menus Importar Form (frontend/menus/controller/menus_importar_form.php)
- **Legacy:** sistema > menus > importar
- **Pills2:** sistema > menus > importar · ADMIN LOCAL > Importar menús · ADMIN GLOBAL > menus > importar

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.menus_importar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_importar.md`

## Restaurar menús ref→DL

### Para Que Sirve

Dejar los menús del esquema (o de todas las DL si dlb) como la referencia por defecto.

### Donde Entrar

- Restaurar menús ref→DL (src/menus/infrastructure/ui/http/controllers/menus_importar_de_ficheros_a_ref.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Menú `sistema > menus > importar desde ficheros`.

### Referencias Internas

- Flujo: `menus.menus_importar_de_ficheros_a_ref.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_importar_de_ficheros_a_ref.md`

## Menus Legacy Layout Items

### Para Que Sirve

Ítems visibles para barra lateral layout H-dlbv.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `menus.menus_legacy_layout_items.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/menus/flujos/menus_legacy_layout_items.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/menus/flujos/`.
