---
id: "certificados.certificado_emitido_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Ver"
capacidad: "certificados.certificado_emitido_ver.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_ver_datos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Ver

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Consultar detalle de un certificado emitido seleccionado en el listado.

## Punto De Entrada

Listado Certificados → modificar/ver fila.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.certificado_pdf`
- `form.destino`
- `form.f_certificado`
- `form.f_enviado`
- `form.firmado`
- `form.idioma`
- `form.nom`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_ver_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
