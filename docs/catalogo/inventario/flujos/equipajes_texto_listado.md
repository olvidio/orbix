---
id: "inventario.equipajes_texto_listado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Cambiar los textos del listado del equipaje"
capacidad: "inventario.equipajes_texto_listado.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_ver"]
fragmentos: []
acciones: ["guardar"]
endpoints: ["/src/inventario/equipajes_texto_listado_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Texto Listado

## Objetivo De Usuario

Guardar el texto de cabecera, cabecera B), pie o el texto para las maletas de un equipaje.

## Punto De Entrada

Dentro de **imprimir equipajes**, con el icono del lápiz (**modificar texto**) junto a la cabecera, la cabecera B) o el pie. El lápiz no sale al imprimir.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Cambiar los textos del listado del equipaje

Pasos propuestos:
1. En **imprimir equipajes**, pulsar el lápiz (**modificar texto**) (o **introducir texto** si aún está vacío).
2. Escribir el texto de **cabecera**, **cabecera B)**, **pie** o **texto a añadir en las maletas**.
3. Pulsar **guardar** (o **cancel**).

Endpoints asociados:
- `/src/inventario/equipajes_texto_listado_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_texto_listado_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
