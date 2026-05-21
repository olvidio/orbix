---
id: "encargossacd.horario_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Horario Ver"
capacidad: "encargossacd.horario_ver.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.horario_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_ver_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Horario Ver

Propuesta generada automaticamente desde la capacidad `encargossacd.horario_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoHorarioVer. Datos del formulario de horario de encargo (no sacd).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.horario_ver`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.f_fin`
- `html.f_ini`
- `html.h_fin`
- `html.h_ini`
- `html.n_sacd`
- `post.desc_enc`
- `post.id_enc`
- `post.id_item_h`
- `post.mod`
- `post.origen`
- `post.refresh`

Acciones JavaScript:
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/encargossacd/horario_ver_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
