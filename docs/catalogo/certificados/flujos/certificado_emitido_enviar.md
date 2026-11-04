---
id: "certificados.certificado_emitido_enviar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Enviar un certificado a otra delegación"
capacidad: "certificados.certificado_emitido_enviar.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_lista"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_emitido_enviar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Enviar

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Enviar un certificado emitido a la delegación destino del alumno.

## Punto De Entrada

Menú Pills2 **ESTUDIOS > Actas y certificados > Certificados**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Enviar un certificado a otra delegación

Pasos propuestos:
1. En **Certificados**, marcar el certificado.
2. Pulsar enviar: se manda una copia a la delegación del alumno y se le avisa.

Endpoints asociados:
- `/src/certificados/certificado_emitido_enviar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_enviar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- sin entrada de menú en el índice
