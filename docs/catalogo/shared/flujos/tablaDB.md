---
id: "shared.tablaDB.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Gestionar TablaDB"
capacidad: "shared.tablaDB.gestionar"
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
acciones: ["crear_actualizar"]
endpoints: ["/src/shared/tablaDB_update"]
estado_revision: "generado"
---

# Flujo - Gestionar TablaDB

Propuesta generada automaticamente desde la capacidad `shared.tablaDB.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TablaDB. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `shared.pantalla.tablaDB_formulario_ver`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/shared/tablaDB_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.accion`
- `form.clase_info`
- `form.valor_depende`
- `html.<?= $nom_camp ?>`
- `post.aSerieBuscar`
- `post.clase_info`
- `post.datos_buscar`
- `post.id_pau`
- `post.k_buscar`
- `post.mod`
- `post.obj_pau`
- `post.permiso`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar_depende`
- `fnjs_cancelar`
- `fnjs_comprobar_fecha`
- `fnjs_grabar`

## Endpoints Del Flujo

- `/src/shared/tablaDB_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
