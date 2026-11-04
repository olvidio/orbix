---
id: "certificados.certificado_emitido_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Consultar un certificado emitido"
capacidad: "certificados.certificado_emitido_ver.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_ver"]
fragmentos: ["certificados.pantalla.certificado_emitido_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_ver_datos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Ver

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Ver el detalle de un certificado emitido.

## Punto De Entrada

Menú Pills2 **ESTUDIOS > Actas y certificados > Certificados**.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_ver`

## Escenarios Inferidos

### Consultar un certificado emitido

Pasos propuestos:
1. En **Certificados**, buscar y pulsar el certificado.
2. Se muestran sus datos (alumno, fecha, número) y el PDF si lo tiene.

Endpoints asociados:
- `/src/certificados/certificado_emitido_ver_datos`

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
