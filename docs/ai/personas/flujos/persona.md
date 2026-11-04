---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Guardar los datos personales"
flujo: "personas.persona.gestionar.flujo"
preguntas: ["Como guardar los datos personales en Guardar los datos personales?"]
pantallas_principales: ["personas.pantalla.personas_editar"]
fragmentos: ["personas.pantalla.personas_editar"]
endpoints: ["/src/personas/persona_update", "/src/personas/persona_eliminar"]
source: "docs/catalogo/personas/flujos/persona.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar los datos personales

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar los datos personales`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar los datos personales en Guardar los datos personales?

## Donde Entrar

- Ficha de persona (`personas.pantalla.personas_editar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar los datos personales

1. En «DATOS PERSONALES» cambiar los campos necesarios.
2. Pulsar **Guardar Cambios** (solo si tu rol lo permite).
3. Si hay un problema el servidor lo avisa con un mensaje.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/persona_update`
- `/src/personas/persona_eliminar`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.personas_editar`
- `personas.pantalla.personas_editar`

## Objetivo

Guardar o eliminar los datos personales de una persona.

## Errores Documentados

- `No se ha pasado el id_nom`
- `No se ha eliminado, porque no es de mi dl`
- `hay un error, no se ha guardado / no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
