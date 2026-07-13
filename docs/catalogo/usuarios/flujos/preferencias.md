---
id: "usuarios.preferencias.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Preferencias"
capacidad: "usuarios.preferencias.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["guardar"]
endpoints: ["/src/usuarios/preferencias_guardar"]
estado_revision: "revisado"
---

# Flujo - Preferencias

## Objetivo De Usuario

Ajuste preferencias personales: layout, inicio, idioma, tablas y estilo.

## Punto De Entrada

Menú Legacy: menú usuario > preferencias. Pills2: menú usuario > preferencias.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Guardar

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

- `/src/usuarios/preferencias_guardar`

## Errores Conocidos

- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias
