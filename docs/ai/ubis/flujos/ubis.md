---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Ubis"
flujo: "ubis.ubis.gestionar.flujo"
preguntas: ["Como eliminar en Ubis?", "Como guardar en Ubis?", "Como consultar el listado en Ubis?"]
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_eliminar", "ubis.pantalla.ubis_lista", "ubis.pantalla.ubis_update"]
endpoints: ["/src/ubis/ubis_eliminar", "/src/ubis/ubis_guardar", "/src/ubis/ubis_lista_data"]
source: "docs/catalogo/ubis/flujos/ubis.md"
estado_revision: "generado"
---

# Ayuda IA - Ubis

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ubis`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Ubis?
- Como guardar en Ubis?
- Como consultar el listado en Ubis?

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
- `/src/ubis/ubis_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/ubis_lista_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.ubis_eliminar`
- `ubis.pantalla.ubis_lista`
- `ubis.pantalla.ubis_update`

## Objetivo

Gestiona Ubis. Descripcion funcional pendiente de revisar.

## Errores Documentados

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `no se encuentra el ubi a borrar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
