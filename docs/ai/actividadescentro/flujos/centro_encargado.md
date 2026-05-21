---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadescentro"
titulo: "Centro Encargado"
flujo: "actividadescentro.centro_encargado.gestionar.flujo"
preguntas: ["Como eliminar en Centro Encargado?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/actividadescentro/centro_encargado_eliminar"]
source: "docs/catalogo/actividadescentro/flujos/centro_encargado.md"
estado_revision: "generado"
---

# Ayuda IA - Centro Encargado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Centro Encargado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Centro Encargado?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadescentro/centro_encargado_eliminar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona CentroEncargado. Elimina un CentroEncargado de una actividad.

## Errores Documentados

- `el centro encargado ya no existe`
- `hay un error, no se ha eliminado el centro`
- `no se sabe cual borrar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
