---
id: "certificados.certificado_emitido_lista.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Lista"
capacidad: "certificados.certificado_emitido_lista.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_lista"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_lista_datos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Lista

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Consultar y gestionar certificados emitidos pendientes de envío en la región STGR.

## Punto De Entrada

Menú ESTUDIOS > Actas y certificados > Certificados (`certificado_emitido_lista.php`). Solo ámbito rstgr/r.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_lista`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.mod`
- `form.sel`
- `html.btn_ok`
- `html.certificado`
- `html.mod`
- `html.refresh`
- `post.certificado`
- `post.refresh`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_certificado`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`
- `fnjs_upload_certificado`

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_lista_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** —
- **Pills2:** ESTUDIOS > Actas y certificados > Certificados
