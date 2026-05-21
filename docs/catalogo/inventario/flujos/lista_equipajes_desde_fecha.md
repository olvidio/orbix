---
id: "inventario.lista_equipajes_desde_fecha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Lista Equipajes Desde Fecha"
capacidad: "inventario.lista_equipajes_desde_fecha.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_desplegable", "inventario.pantalla.equipajes_movimientos_que", "inventario.pantalla.equipajes_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Equipajes Desde Fecha

Propuesta generada automaticamente desde la capacidad `inventario.lista_equipajes_desde_fecha.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaEquipajesDesdeFecha. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_desplegable`
- `inventario.pantalla.equipajes_movimientos_que`
- `inventario.pantalla.equipajes_ver`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.filtro`
- `form.id_equipaje`
- `form.loc`
- `form.sel`
- `form.texto`
- `post.eliminar`
- `post.filtro`
- `post.imprimir`

Acciones JavaScript:
- `fnjs_actualizar_lista_equipaje`
- `fnjs_add_doc`
- `fnjs_cerrar`
- `fnjs_del_doc`
- `fnjs_docs_libres`
- `fnjs_eliminar_equipaje`
- `fnjs_eliminar_grupo`
- `fnjs_guardar_listado`
- `fnjs_lista_docs`
- `fnjs_mod_texto_equipaje`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`
- `fnjs_nuevo_grupo`
- `fnjs_update_div`
- `fnjs_update_grupo`
- `fnjs_ver_1`
- `fnjs_ver_2`
- `fnjs_ver_docs`
- `fnjs_ver_movimientos`

## Endpoints Del Flujo

- `/src/inventario/lista_equipajes_desde_fecha`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
