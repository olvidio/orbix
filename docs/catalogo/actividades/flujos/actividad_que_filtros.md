---
id: "actividades.actividad_que_filtros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Actividad Que Filtros"
capacidad: "actividades.actividad_que_filtros.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_que_filtros"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Que Filtros

Propuesta generada automaticamente desde la capacidad `actividades.actividad_que_filtros.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadQueFiltrosBloque. Genera el HTML del bloque "filtros extra" (filtro_lugar + lugar + organiza + publicada) en la pantalla actividad_que. El bloque solo se muestra a usuarios con permiso de control (perm_ctr); para el resto devuelve cadena vacia. Encapsula todos los accesos a repositorios y entidades de dominio necesarios (Role, DelegacionDropdown, ActividadLugar) de forma que el frontend controller no tenga que depender directamente de src/.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.actividad_que`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl_org`
- `form.dl_propia`
- `form.entrada`
- `form.extendida`
- `form.filtro_lugar`
- `form.id_tipo_activ`
- `form.id_ubi`
- `form.isfsv`
- `form.modo`
- `form.opcion_sel`
- `form.publicado`
- `form.salida`
- `form.selected`
- `form.sfsv`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.extendida`
- `post.fases_off`
- `post.fases_on`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.listar_asistentes`
- `post.modo`
- `post.nom_activ`
- `post.periodo`
- `post.publicado`
- `post.que`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.snom_tipo`
- `post.stack`
- `post.status`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/actividad_que_filtros`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
