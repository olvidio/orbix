---
id: "cambios.avisos_generar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Gestionar Avisos Generar"
capacidad: "cambios.avisos_generar.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.avisos_generar"]
acciones: ["listar"]
endpoints: ["/src/cambios/avisos_generar_lista_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Avisos Generar

Propuesta generada automaticamente desde la capacidad `cambios.avisos_generar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona AvisosGenerar. Listado de avisos CambioUsuario (con avisado=false) para el usuario/aviso_tipo dado + opciones de desplegables de la pantalla avisos_generar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cambios.pantalla.avisos_generar`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/cambios/avisos_generar_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.aviso_tipo`
- `form.id_usuario`
- `html.f_fin`
- `html.refresh`
- `post.Gstack`
- `post.aviso_tipo`
- `post.id_usuario`
- `post.refresh`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_borrar_hasta_fecha`
- `fnjs_enviar_formulario`
- `fnjs_selectAll`

## Endpoints Del Flujo

- `/src/cambios/avisos_generar_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
