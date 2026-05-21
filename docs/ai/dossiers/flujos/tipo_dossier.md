---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "dossiers"
titulo: "Tipo Dossier"
flujo: "dossiers.tipo_dossier.gestionar.flujo"
preguntas: ["Como eliminar en Tipo Dossier?", "Como guardar en Tipo Dossier?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/dossiers/tipo_dossier_eliminar", "/src/dossiers/tipo_dossier_guardar"]
source: "docs/catalogo/dossiers/flujos/tipo_dossier.md"
estado_revision: "generado"
---

# Ayuda IA - Tipo Dossier

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tipo Dossier`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Tipo Dossier?
- Como guardar en Tipo Dossier?

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
- `/src/dossiers/tipo_dossier_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona TipoDossier. Elimina un TipoDossier. Guarda los cambios a un TipoDossier.

## Errores Documentados

- `Hay un error, no se ha eliminado.`
- `Hay un error, no se ha guardado.`
- `falta id_tipo_dossier`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
