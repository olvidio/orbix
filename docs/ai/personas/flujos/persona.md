---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Guardar o eliminar persona"
flujo: "personas.persona.gestionar.flujo"
preguntas: []
pantallas_principales: []
fragmentos: ["personas.pantalla.personas_editar"]
endpoints: ["/src/personas/persona_update", "/src/personas/persona_eliminar"]
source: "docs/catalogo/personas/flujos/persona.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar o eliminar persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar o eliminar persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.personas_editar`

## Objetivo

Guardar cambios en la ficha o eliminar un registro de la propia delegación.

## Errores Documentados

- `No se ha pasado el id_nom`
- `No se ha eliminado, porque no es de mi dl`
- `hay un error, no se ha guardado / no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
