---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Crear una actividad con o sin permiso"
flujo: "actividades.actividad_permiso_crear.gestionar.flujo"
preguntas: ["Como crear una actividad con o sin permiso en Crear una actividad con o sin permiso?"]
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
endpoints: ["/src/actividades/actividad_permiso_crear_datos"]
source: "docs/catalogo/actividades/flujos/actividad_permiso_crear.md"
estado_revision: "generado"
---

# Ayuda IA - Crear una actividad con o sin permiso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear una actividad con o sin permiso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear una actividad con o sin permiso en Crear una actividad con o sin permiso?

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear una actividad con o sin permiso

1. Abrir una actividad nueva.
2. Si el usuario tiene permiso de crear para ese tipo, el formulario está activo.
3. Si no, el formulario se muestra bloqueado y hay que pedir el permiso.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_permiso_crear_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`

## Objetivo

Al crear una actividad nueva, permitir o bloquear el formulario según los permisos de proceso del usuario.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
