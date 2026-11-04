---
id: "devel_db_admin.migraciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "devel_db_admin"
nombre: "Flujo - Aplicar las migraciones pendientes"
capacidad: "devel_db_admin.migraciones.gestionar"
pantallas_principales: ["devel_db_admin.pantalla.migraciones_lista"]
fragmentos: ["devel_db_admin.pantalla.migraciones_lista"]
acciones: ["listar"]
endpoints: ["/src/devel_db_admin/migraciones_lista_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Migraciones

Propuesta generada automaticamente desde la capacidad `devel_db_admin.migraciones.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Revisar y aplicar las migraciones SQL pendientes de la base de datos (herramienta de desarrollo).

## Punto De Entrada

Menú **sistema > DB > actualizar DB**.

## Fragmentos O Pantallas Auxiliares

- `devel_db_admin.pantalla.migraciones_lista`

## Escenarios Inferidos

### Aplicar las migraciones pendientes

Pasos propuestos:
1. Entrar en **actualizar DB**: aparece la lista de migraciones de base de datos.
2. Revisar las pendientes (las de estructura se ejecutan primero en la base principal y luego en la réplica de lectura).
3. Pulsar para aplicarlas y esperar a «ejecutando...»; al terminar se reactivan las suscripciones.
4. Para anotar una como aplicada sin ejecutarla, usar quitar registro.

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
