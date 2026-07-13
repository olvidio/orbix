---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Fases Activ Cambio"
flujo: "procesos.fases_activ_cambio.gestionar.flujo"
preguntas: ["Como crear o modificar en Fases Activ Cambio?", "Como consultar el listado en Fases Activ Cambio?", "Como obtener en Fases Activ Cambio?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.fases_activ_cambio", "procesos.pantalla.fases_activ_cambio_lista"]
endpoints: ["/src/procesos/fases_activ_cambio_get", "/src/procesos/fases_activ_cambio_lista", "/src/procesos/fases_activ_cambio_update"]
source: "docs/catalogo/procesos/flujos/fases_activ_cambio.md"
estado_revision: "generado"
---

# Ayuda IA - Fases Activ Cambio

Usa este documento para responder preguntas de usuario sobre como trabajar con `Fases Activ Cambio`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Fases Activ Cambio?
- Como consultar el listado en Fases Activ Cambio?
- Como obtener en Fases Activ Cambio?

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
- `/src/procesos/fases_activ_cambio_update`

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/fases_activ_cambio_lista`

## Obtener

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.fases_activ_cambio`
- `procesos.pantalla.fases_activ_cambio_lista`

## Objetivo

Cambio masivo de fase en actividades: filtrar por tipo, periodo y fase destino; listar candidatas; marcar o desmarcar la tarea de la fase nueva en las actividades seleccionadas.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
