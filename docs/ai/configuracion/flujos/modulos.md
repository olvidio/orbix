---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "módulo (ficha)"
flujo: "configuracion.modulos.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
source: "docs/catalogo/configuracion/flujos/modulos.md"
estado_revision: "generado"
---

# Ayuda IA - módulo (ficha)

Usa este documento para responder preguntas de usuario sobre como trabajar con `módulo (ficha)`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `configuracion.pantalla.modulos_form`
- `configuracion.pantalla.modulos_update`

## Objetivo

Dar de alta un módulo nuevo o editar nombre, descripción y dependencias (módulos/apps requeridos) de uno existente.

## Errores Documentados

- `hay un error, no se ha eliminado (solo en baja desde listado)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
