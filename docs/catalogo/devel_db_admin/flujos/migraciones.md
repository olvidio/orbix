---
id: "devel_db_admin.migraciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Gestionar Migraciones"
capacidad: "devel_db_admin.migraciones.gestionar"
pantallas_principales: []
fragmentos: ["devel_db_admin.pantalla.migraciones_lista"]
acciones: ["listar"]
endpoints: ["/src/devel_db_admin/migraciones_lista_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Migraciones

Propuesta generada automaticamente desde la capacidad `devel_db_admin.migraciones.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Revisar y aplicar migraciones SQL del repositorio.


## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.migraciones_lista`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/devel_db_admin/migraciones_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`

Acciones JavaScript:
- `fnjs_migraciones_checked`
- `fnjs_migraciones_ejecutar_hasta`
- `fnjs_migraciones_ejecutar_seleccion`
- `fnjs_migraciones_enviar`
- `fnjs_migraciones_quitar_registro`

## Endpoints Del Flujo

- `/src/devel_db_admin/migraciones_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sistema > DB > actualizar DB
- **Pills2:** sistema > DB > actualizar DB
