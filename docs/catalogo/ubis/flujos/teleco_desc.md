---
id: "ubis.teleco_desc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Elegir la descripción de una teleco"
capacidad: "ubis.teleco_desc.gestionar"
pantallas_principales: ["ubis.pantalla.teleco_desc_lista_ajax"]
fragmentos: ["ubis.pantalla.teleco_desc_lista_ajax"]
acciones: ["listar"]
endpoints: ["/src/ubis/teleco_desc_lista"]
estado_revision: "revisado"
---

# Flujo - Teleco Desc

## Objetivo De Usuario

Ofrecer las descripciones de telecomunicación según el tipo elegido.

## Punto De Entrada

Formulario de teleco.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_desc_lista_ajax`

## Escenarios Inferidos

### Elegir la descripción de una teleco

Pasos propuestos:
1. En el formulario de teleco, elegir el tipo.
2. El desplegable de descripción se rellena con las opciones de ese tipo.

Endpoints asociados:
- `/src/ubis/teleco_desc_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_tipo_teleco`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/teleco_desc_lista`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
