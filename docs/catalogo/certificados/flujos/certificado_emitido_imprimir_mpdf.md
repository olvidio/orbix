---
id: "certificados.certificado_emitido_imprimir_mpdf.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Obtener el PDF del certificado"
capacidad: "certificados.certificado_emitido_imprimir_mpdf.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_imprimir_mpdf"]
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir_mpdf"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_imprimir_mpdf_datos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Imprimir Mpdf

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Generar el PDF del certificado con las notas y los textos traducidos al idioma elegido.

## Punto De Entrada

Ficha de la persona > dossier de certificados (**relación de certificados**).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_imprimir_mpdf`

## Escenarios Inferidos

### Obtener el PDF del certificado

Pasos propuestos:
1. Emitir el certificado desde la ficha del alumno.
2. El PDF se genera automáticamente y se abre para imprimir o guardar.

Endpoints asociados:
- `/src/certificados/certificado_emitido_imprimir_mpdf_datos`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_imprimir_mpdf_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
