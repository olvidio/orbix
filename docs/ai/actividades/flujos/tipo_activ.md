---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Tipo Activ"
flujo: "actividades.tipo_activ.gestionar.flujo"
preguntas: ["Como crear en Tipo Activ?", "Como crear o modificar en Tipo Activ?", "Como eliminar en Tipo Activ?", "Como consultar el listado en Tipo Activ?"]
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
endpoints: ["/src/actividades/tipo_activ_eliminar", "/src/actividades/tipo_activ_lista", "/src/actividades/tipo_activ_nuevo", "/src/actividades/tipo_activ_update"]
source: "docs/catalogo/actividades/flujos/tipo_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Tipo Activ

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tipo Activ`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Tipo Activ?
- Como crear o modificar en Tipo Activ?
- Como eliminar en Tipo Activ?
- Como consultar el listado en Tipo Activ?

## Donde Entrar

- Tipo Activ (`actividades.pantalla.tipo_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/tipo_activ_eliminar`

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/tipo_activ_lista`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.tipo_activ`

## Objetivo

Gestiona TipoActiv, TipoActivLista. Actualiza el nombre de un tipo de actividad. Portado del case update del dispatcher legacy. Crea un nuevo tipo de actividad. Portado del case nuevo del dispatcher legacy. Devuelve cadena vacia si todo va bien o un texto de error/aviso. Devuelve la tabla HTML con los tipos de actividad existentes. Portado desde el case lista del dispatcher legacy frontend/actividades/controller/tipo_activ_ajax.php. Elimina un tipo de actividad. Portado del case eliminar del dispatcher legacy.

## Errores Documentados

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
