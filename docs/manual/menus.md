---
tipo: manual_usuario
modulo: menus
flujos: 18
estado_revision: revisado_parcial
---

# Manual De Usuario - menus

Administracion de la **estructura de menus** Orbix (import/export, meta menus, grupmenu).

## Acceso Por Menu (rol 13 Admin)

| Texto | Controller |
|-------|------------|
| **Menus** (raiz) | — |
| **Seleccionar** | `menus_que.php` |
| **Importar** | `menus_importar_form.php` |
| **Exportar** | `menus_exportar_form.php` |
| **Meta menus** | `shared/tablaDB` + `InfoMetaMenus` |
| **Grup menu** | `grupmenu_lista.php` |
| **Menus a texto** | API `menus_generar_txt` |
| Ficheros export/import | `menus_ficheros.php` |
| **Ayuda** | `como.html` |

## Seleccionar Y Editar Menus

1. **Seleccionar** — arbol o listado de entradas menu.
2. Editar URL, parametros, modulo, permisos.
3. Guardar cambios (segun pantalla).

## Importar / Exportar

1. **Exportar** — generar dump menus (formulario o ficheros).
2. **Importar** — cargar desde formulario o `menus_ficheros` accion=importar.
3. Validar en entorno demo si existe accion **pasar a demo**.

## Modulos Relacionados

- **usuarios** — roles, grupmenu, permisos
- **configuracion** — modulos registrados (`mods_req`)
- **shared** — tablaDB meta menus

Fuente CSV legacy: `documentacion/Documentacion_Obix/menus.csv`
