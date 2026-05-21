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

Gestiona FasesActivCambio, FasesActivCambioGet, FasesActivCambioLista. Caso de uso: aplica setCompletado(t|f) a la tarea de la fase nueva para cada id_activ seleccionado, respetando permisos de oficina del responsable. Caso de uso: devuelve las fases posibles para el id_tipo_activ y la dl_propia actual, incluyendo la opcion seleccionada por id_fase_sel. Respuesta conforme al contrato de refactor.md para desplegables (payload JSON con id, opciones, selected, blanco, action). El frontend construye el <select> con el helper JS estandar. Caso de uso: devuelve los datos estructurados para la tabla de actividades candidatas a cambiar de fase, segun filtros de tipo de actividad, dl_propia, periodo y accion (marcar/desmarcar). El frontend renderiza el formulario con frontend\shared\web\Lista + web\Hash.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
