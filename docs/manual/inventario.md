---
tipo: "manual_usuario"
modulo: "inventario"
flujos: 42
estado_revision: "generado"
---

# Manual De Usuario - inventario

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Cabecera Pie Txt

### Para Que Sirve

Editar textos globales de cabecera/pie para impresión de equipajes.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.cabecera_pie_txt.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/cabecera_pie_txt.md`

## Doc Asignar Ctr

### Para Que Sirve

- Gestiona DocAsignarCtr.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.doc_asignar_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/doc_asignar_ctr.md`

## Doc Asignar Dlb

### Para Que Sirve

- Gestiona DocAsignarDlb.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se encuentra el documento`
- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.doc_asignar_dlb.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/doc_asignar_dlb.md`

## Documentos

### Para Que Sirve

Asignación y consulta de documentos por tipo: selector `docs_asignar_que`, listados asignados/no asignados, formularios CTR/DLB.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`
- `No ha seleccionado ningún documento`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.documentos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/documentos.md`

## Equipajes

### Para Que Sirve

Ciclo de vida de equipajes: alta (`equipajes_nuevo`), composición de maletas (EGM/Whereis), impresión y eliminación.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

### Errores O Avisos Frecuentes

- `falta id_equipaje`
- `hay un error, no se ha eliminado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes.md`

## Equipajes Add Doc

### Para Que Sirve

- Gestiona EquipajesAddDoc.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_add_doc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_add_doc.md`

## Equipajes Del Doc

### Para Que Sirve

- Gestiona EquipajesDelDoc.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_del_doc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_del_doc.md`

## Equipajes Doc Casa

### Para Que Sirve

- Gestiona EquipajesDocCasa.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_doc_casa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_doc_casa.md`

## Equipajes Egm

### Para Que Sirve

- Gestiona EquipajesEgm.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_egm.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_egm.md`

## Equipajes Eliminar Grupo

### Para Que Sirve

- Gestiona EquipajesEliminarGrupo.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha eliminado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_eliminar_grupo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_eliminar_grupo.md`

## Equipajes Lista Activ Equipaje

### Para Que Sirve

- Gestiona EquipajesListaActivEquipaje.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `OJO! No se encuentra la actividad con id: %s`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_lista_activ_equipaje.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_lista_activ_equipaje.md`

## Equipajes Lista Activ Periodo

### Para Que Sirve

- Gestiona EquipajesListaActivPeriodo.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `debe seleccionar un lugar`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_lista_activ_periodo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_lista_activ_periodo.md`

## Equipajes Lista Activ Sel

### Para Que Sirve

- Gestiona EquipajesListaActivSel.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_lista_activ_sel.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_lista_activ_sel.md`

## Equipajes Movimientos

### Para Que Sirve

Comparar movimientos de documentos entre equipajes seleccionados.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_movimientos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_movimientos.md`

## Equipajes Nuevo

### Para Que Sirve

Crear equipaje: periodo, casa, actividades, nombre; persiste con `equipajes_nuevo_guardar`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_nuevo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_nuevo.md`

## Equipajes Texto Listado

### Para Que Sirve

- Gestiona EquipajesTextoListado.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_texto_listado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_texto_listado.md`

## Equipajes Update Grupo

### Para Que Sirve

- Gestiona EquipajesUpdateGrupo.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.equipajes_update_grupo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/equipajes_update_grupo.md`

## Inventario Css Inline

### Para Que Sirve

- Gestiona InventarioCssInline.
- CSS embebido para impresión de inventario (inventario.css.php en disco).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.inventario_css_inline.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/inventario_css_inline.md`

## Inventario Ctr

### Para Que Sirve

Impresión inventario de centros: selección en `doc_de_ctr`, render en `doc_imprimir_ctr` vía `inventario_ctr`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.inventario_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/inventario_ctr.md`

## Inventario Dlb

### Para Que Sirve

Impresión inventario DLB/casa vía `inventario_dlb`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.inventario_dlb.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/inventario_dlb.md`

## Lista Casas Posibles Periodo

### Para Que Sirve

- Gestiona ListaCasasPosiblesPeriodo.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_casas_posibles_periodo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_casas_posibles_periodo.md`

## Lista Colecciones

### Para Que Sirve

- Gestiona ColeccionesOpciones.
- Opciones del desplegable de colecciones (lista_colecciones.php).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_colecciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_colecciones.md`

## Lista De Ctr

### Para Que Sirve

- Gestiona ListaDeCtr.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_de_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_de_ctr.md`

## Lista De Ctr Con Docs

### Para Que Sirve

- Gestiona ListaDeCtrConDocs.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_de_ctr_con_docs.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_de_ctr_con_docs.md`

## Lista Docs Asignados Por Tipo

### Para Que Sirve

- Gestiona ListaDocsAsignadosPorTipo.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_asignados_por_tipo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_asignados_por_tipo.md`

## Lista Docs Asignar Ctr

### Para Que Sirve

- Gestiona ListaDocsAsignarCtr.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_asignar_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_asignar_ctr.md`

## Lista Docs Asignar Dlb

### Para Que Sirve

- Gestiona ListaDocsAsignarDlb.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_asignar_dlb.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_asignar_dlb.md`

## Lista Docs Con Observaciones

### Para Que Sirve

- Gestiona ListaDocsConObservaciones.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_con_observaciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_con_observaciones.md`

## Lista Docs De Ctr

### Para Que Sirve

- Gestiona ListaDocsDeCtr.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_de_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_de_ctr.md`

## Lista Docs De Dlb

### Para Que Sirve

- Gestiona ListaDocsDeDlb.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_de_dlb.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_de_dlb.md`

## Lista Docs De Egm

### Para Que Sirve

- Gestiona ListaDocsDeEgm.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_de_egm.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_de_egm.md`

## Lista Docs De Lugar

### Para Que Sirve

- Gestiona ListaDocsDeLugar.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_de_lugar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_de_lugar.md`

## Lista Docs En Busqueda

### Para Que Sirve

- Gestiona ListaDocsEnBusqueda.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_en_busqueda.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_en_busqueda.md`

## Lista Docs Libres

### Para Que Sirve

- Gestiona ListaDocsLibres.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_libres.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_libres.md`

## Lista Docs No Asignados Por Tipo

### Para Que Sirve

- Gestiona ListaDocsNoAsignadosPorTipo.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_no_asignados_por_tipo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_no_asignados_por_tipo.md`

## Lista Docs Perdidos

### Para Que Sirve

- Gestiona ListaDocsPerdidos.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_docs_perdidos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_docs_perdidos.md`

## Lista Equipajes Desde Fecha

### Para Que Sirve

- Gestiona ListaEquipajesDesdeFecha.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_equipajes_desde_fecha.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_equipajes_desde_fecha.md`

## Lista Equipajes Posibles Maletas

### Para Que Sirve

- Gestiona ListaEquipajesPosiblesMaletas.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_equipajes_posibles_maletas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_equipajes_posibles_maletas.md`

## Lista Lugares De Ubi

### Para Que Sirve

- Gestiona ListaLugaresDeUbi.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_lugares_de_ubi.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_lugares_de_ubi.md`

## Lista Tipo Doc

### Para Que Sirve

- Gestiona TipoDocOpciones.
- Opciones del desplegable de tipos de documento (lista_tipo_doc.php).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.lista_tipo_doc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/lista_tipo_doc.md`

## Texto De Egm

### Para Que Sirve

- Gestiona TextoDeEgm.
- Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.texto_de_egm.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/texto_de_egm.md`

## Traslado Doc

### Para Que Sirve

Trasladar documentos entre centros/lugares: filtro en `traslado_doc_que`, selección en `traslado_doc_lista`, guardado en `traslado_doc_guardar`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `inventario.traslado_doc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/inventario/flujos/traslado_doc.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
