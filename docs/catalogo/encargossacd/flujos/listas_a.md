---
id: "encargossacd.listas_a.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Listas A"
capacidad: "encargossacd.listas_a.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_a"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_a_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Listas A

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_a.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListasA. Genera el listado de atencion SACD "a" (cr 9/05, Anexo2, 9.4 a). Sustituye la logica que habia en frontend/encargossacd/controller/listas_a.php. Devuelve el HTML completo junto con los textos de cabecera, listos para inyectarlos en la vista listas.phtml.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_a`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sf`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_a_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
