---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Grupo"
flujo: "casas.grupo.gestionar.flujo"
preguntas: ["Como crear o modificar en Grupo?", "Como eliminar en Grupo?", "Como consultar el listado en Grupo?", "Como abrir el formulario en Grupo?"]
pantallas_principales: ["casas.pantalla.grupo"]
fragmentos: ["casas.pantalla.grupo_form", "casas.pantalla.grupo_lista"]
endpoints: ["/src/casas/grupo_eliminar", "/src/casas/grupo_form_data", "/src/casas/grupo_lista_data", "/src/casas/grupo_update"]
source: "docs/catalogo/casas/flujos/grupo.md"
estado_revision: "generado"
---

# Ayuda IA - Grupo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Grupo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Grupo?
- Como eliminar en Grupo?
- Como consultar el listado en Grupo?
- Como abrir el formulario en Grupo?

## Donde Entrar

- Grupo (`casas.pantalla.grupo`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/grupo_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/grupo_eliminar`

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/grupo_lista_data`

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/grupo_form_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.grupo`
- `casas.pantalla.grupo_form`
- `casas.pantalla.grupo_lista`

## Objetivo

Gestiona GrupoCasa. Crea o actualiza un GrupoCasa. Datos del formulario GrupoCasa (nuevo/editar). Elimina un GrupoCasa. Listado de GrupoCasa (relaciones padre ↔ hijo).

## Errores Documentados

- `debe indicar las dos casas`
- `No puede ser la misma casa`
- `no se encuentra el grupo`
- `debe indicar el grupo a eliminar`
- `Hay un error, no se ha guardado.`
- `Hay un error, no se ha eliminado.`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
