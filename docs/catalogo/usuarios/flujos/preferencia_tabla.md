---
id: "usuarios.preferencia_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Reutilizar la presentación de una tabla"
capacidad: "usuarios.preferencia_tabla.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener"]
endpoints: ["/src/usuarios/preferencia_tabla_get"]
estado_revision: "revisado"
---

# Flujo - Preferencia Tabla

## Objetivo De Usuario

Recordar cómo prefiere el usuario ver cada tabla (anchos, orden) para presentarla igual la próxima vez.

## Punto De Entrada

Automático al abrir una tabla; la opción general está en **preferencias > Presentación tablas**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Reutilizar la presentación de una tabla

Pasos propuestos:
1. Abrir cualquier listado en formato SlickGrid.
2. Ajustar columnas si se desea; se guardan para las siguientes veces.
3. La opción global se elige en **preferencias**.

Endpoints asociados:
- `/src/usuarios/preferencia_tabla_get`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/preferencia_tabla_get`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias
