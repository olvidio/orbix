---
id: "usuarios.preferencia_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Preferencia Tabla"
capacidad: "usuarios.preferencia_tabla.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener"]
endpoints: ["/src/usuarios/preferencia_tabla_get"]
estado_revision: "revisado"
---

# Flujo - Preferencia Tabla

## Objetivo De Usuario

Devuelve preferencias de presentación de tablas (global y SlickGrid por id_tabla+idioma).

## Punto De Entrada

Menú Legacy: menú usuario > preferencias. Pills2: menú usuario > preferencias.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Obtener

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
