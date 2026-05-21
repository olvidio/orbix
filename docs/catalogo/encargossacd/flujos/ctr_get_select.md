---
id: "encargossacd.ctr_get_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Ctr Get Select"
capacidad: "encargossacd.ctr_get_select.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.ctr_ficha", "encargossacd.pantalla.encargo_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/ctr_get_select_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ctr Get Select

Propuesta generada automaticamente desde la capacidad `encargossacd.ctr_get_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoCtrSelect. Payload JSON para el desplegable de centros segun filtro (y zona opcional). Devuelve el contrato estandar definido en refactor.md (id, name, opciones, selected, blanco, val_blanco, action) para que el frontend monte el <select> con fnjs_construir_desplegable (o el modelo frontend/encargossacd/model/DesplCentros). Importante: esta clase vive en capa application y por tanto **no** puede instanciar frontend\shared\web\Desplegable (ver refactor.md).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.ctr_ficha`
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
- `form.id_ubi`
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
- `post.id_ubi`
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
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/ctr_get_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
