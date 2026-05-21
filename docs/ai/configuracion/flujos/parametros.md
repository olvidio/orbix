---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "Parametros"
flujo: "configuracion.parametros.gestionar.flujo"
preguntas: ["Como crear o modificar en Parametros?", "Como consultar el listado en Parametros?"]
pantallas_principales: []
fragmentos: ["configuracion.pantalla.parametros"]
endpoints: ["/src/configuracion/parametros_lista", "/src/configuracion/parametros_update"]
source: "docs/catalogo/configuracion/flujos/parametros.md"
estado_revision: "generado"
---

# Ayuda IA - Parametros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Parametros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Parametros?
- Como consultar el listado en Parametros?

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
- `/src/configuracion/parametros_update`

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/parametros_lista`

## Pantallas Y Fragmentos Relacionados

- `configuracion.pantalla.parametros`

## Objetivo

Gestiona Parametros. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
