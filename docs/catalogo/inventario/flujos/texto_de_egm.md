---
id: "inventario.texto_de_egm.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Abrir el texto para modificarlo"
capacidad: "inventario.texto_de_egm.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_form_texto_listado"]
fragmentos: ["inventario.pantalla.equipajes_form_texto_listado"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/texto_de_egm"]
estado_revision: "revisado"
---

# Flujo - Gestionar Texto De Egm

## Objetivo De Usuario

Cargar el texto que se añade a las maletas (cabecera, cabecera B, pie) para poder modificarlo.

## Punto De Entrada

Dentro de **imprimir equipajes**, al pulsar el lápiz (**modificar texto**) de la cabecera, cabecera B) o pie.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_form_texto_listado`

## Escenarios Inferidos

### Abrir el texto para modificarlo

Pasos propuestos:
1. En **imprimir equipajes**, pulsar el lápiz junto a la cabecera, la cabecera B) o el pie.
2. Se abre el cuadro con el texto actual.
3. Cambiarlo y guardar.

Endpoints asociados:
- `/src/inventario/texto_de_egm`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.texto`
- `html.texto`
- `post.id_equipaje`
- `post.loc`
- `post.texto`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar_listado`

## Endpoints Del Flujo

- `/src/inventario/texto_de_egm`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
