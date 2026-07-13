---
id: "ubis.teleco_desc.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Teleco Desc"
capacidad: "ubis.teleco_desc.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.teleco_desc_lista_ajax"]
acciones: ["listar"]
endpoints: ["/src/ubis/teleco_desc_lista"]
estado_revision: "revisado"
---

# Flujo - Teleco Desc

## Objetivo De Usuario

Devuelve descripciones de telecomunicación dependientes del tipo seleccionado.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_desc_lista_ajax`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

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
