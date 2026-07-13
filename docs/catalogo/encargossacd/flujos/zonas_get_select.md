---
id: "encargossacd.zonas_get_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Zonas Get Select"
capacidad: "encargossacd.zonas_get_select.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/zonas_get_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Zonas Get Select

Propuesta generada automaticamente desde la capacidad `encargossacd.zonas_get_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoZonasSelect. Payload JSON para el desplegable de zonas (grupo «zonas misas»). Devuelve el contrato estandar definido en refactor.md, sin instanciar frontend\shared\web\Desplegable (responsabilidad exclusiva del frontend).

## Punto De Entrada

Fragmento AJAX embebido; sin entrada de menú directa.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.encargo_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.desc_enc`
- `form.desc_lugar`
- `form.filtro_ctr`
- `form.grupo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_zona`
- `form.idioma_enc`
- `form.lst_ctrs`
- `form.nom_tipo`
- `form.observ`
- `form.que`
- `html.desc_enc`
- `html.desc_lugar`
- `post.desc_enc`
- `post.desc_lugar`
- `post.filtro_ctr`
- `post.grupo`
- `post.id_enc`
- `post.id_tipo_enc`
- `post.id_zona`
- `post.que`
- `post.refresh`
- `post.sel`

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_generarNomEnc`
- `fnjs_guardar`
- `fnjs_lista_ctrs`
- `fnjs_lista_ctrs_por_zona`
- `fnjs_lista_zonas`
- `fnjs_lst_tipo_enc`
- `fnjs_strip_hash_params`

## Endpoints Del Flujo

- `/src/encargossacd/zonas_get_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

