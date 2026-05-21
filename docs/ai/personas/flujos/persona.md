---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Persona"
flujo: "personas.persona.gestionar.flujo"
preguntas: ["Como crear o modificar en Persona?", "Como eliminar en Persona?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/personas/persona_eliminar", "/src/personas/persona_update"]
source: "docs/catalogo/personas/flujos/persona.md"
estado_revision: "generado"
---

# Ayuda IA - Persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Persona?
- Como eliminar en Persona?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/persona_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/persona_eliminar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona Persona. Endpoint JSON: elimina una persona. Endpoint JSON: guarda los datos de una persona.

## Errores Documentados

- `No existe la clase de la persona`
- `No se encuentra la persona`
- `No se ha eliminado, porque no es de mi dl`
- `No se ha pasado el id_nom`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
