---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Grupo"
flujo: "usuarios.grupo.gestionar.flujo"
preguntas: ["Como eliminar en Grupo?", "Como guardar en Grupo?", "Como consultar el listado en Grupo?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.grupo_lista"]
endpoints: ["/src/usuarios/grupo_eliminar", "/src/usuarios/grupo_guardar", "/src/usuarios/grupo_lista"]
source: "docs/catalogo/usuarios/flujos/grupo.md"
estado_revision: "generado"
---

# Ayuda IA - Grupo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Grupo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Grupo?
- Como guardar en Grupo?
- Como consultar el listado en Grupo?

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
- `/src/usuarios/grupo_eliminar`

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
- `/src/usuarios/grupo_lista`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.grupo_lista`

## Objetivo

Gestiona GruposLista. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
