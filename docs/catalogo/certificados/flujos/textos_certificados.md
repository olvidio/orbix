---
id: "certificados.textos_certificados.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Textos legales del certificado"
capacidad: "certificados.textos_certificados.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/certificados/textos_certificados"]
estado_revision: "revisado"
---

# Flujo - Gestionar Textos Certificados

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Textos legales que se incluyen automáticamente al generar el PDF (no requiere acción del usuario).

## Punto De Entrada

Automático al generar el certificado.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Textos legales del certificado

Pasos propuestos:
1. Al emitir un certificado, el sistema incluye los textos legales en el idioma elegido.
2. No hace falta hacer nada.

Endpoints asociados:
- `/src/certificados/textos_certificados`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/textos_certificados`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
