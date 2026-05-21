---
id: "encargossacd.listas_c.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Listas C"
capacidad: "encargossacd.listas_c.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_c"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_c_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Listas C

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_c.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListasC. Genera el listado de atencion SACD "c" (cr 9/05, Anexo2, 9.4 c). Sustituye la logica de frontend/encargossacd/controller/listas_c.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_c`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_c_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
