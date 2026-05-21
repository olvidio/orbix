---
id: "encargossacd.listas_com_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Listas Com Sacd"
capacidad: "encargossacd.listas_com_sacd.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_com_sacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_sacd_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Listas Com Sacd

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_com_sacd.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListasComSacd. Datos para la comunicacion a los SACD. Sustituye la logica de frontend/encargossacd/controller/listas_com_sacd.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_com_sacd`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_com_sacd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
