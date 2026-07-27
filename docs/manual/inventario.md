---
tipo: "manual_usuario"
modulo: "inventario"
flujos: 42
estado_revision: "revisado_parcial"
---

# Manual De Usuario - inventario

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Cabecera Pie Txt

### Para Que Sirve

Editar textos globales de cabecera/pie para impresión de equipajes.

### Donde Entrar

- Textos cabecera/pie equipajes (frontend/inventario/controller/cabecera_pie_txt.php)
- Imprimir equipaje (frontend/inventario/controller/equipajes_imprimir.php)
- **Legacy:** scdl > Inventario > equipajes > tipos de texto
- **Pills2:** —

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

- Formulario asignar a centros (frontend/inventario/controller/doc_asignar_ctr.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Formulario asignar DLB (frontend/inventario/controller/doc_asignar_dlb.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** scdl > Inventario > documentos > asignar documento
- **Pills2:** —

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** scdl > Inventario > equipajes > hacer equipajes
- **Pills2:** —

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Docs por casa (equipaje) (frontend/inventario/controller/equipajes_doc_casa.php)
- Imprimir equipaje (frontend/inventario/controller/equipajes_imprimir.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Docs por casa (equipaje) (frontend/inventario/controller/equipajes_doc_casa.php)
- Imprimir equipaje (frontend/inventario/controller/equipajes_imprimir.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Imprimir equipaje (frontend/inventario/controller/equipajes_imprimir.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Actividades por periodo (frontend/inventario/controller/equipajes_lista_activ_periodo.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Fragmento selección actividades (frontend/inventario/controller/equipajes_form_nuevo.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Movimientos maletas — resultado (frontend/inventario/controller/equipajes_movimientos.php)
- **Legacy:** scdl > Inventario > equipajes > movimientos maletas
- **Pills2:** —

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** scdl > Inventario > equipajes > nuevo equipaje
- **Pills2:** —

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Imprimir inventario centros (frontend/inventario/controller/doc_imprimir_ctr.php)
- Imprimir inventario DLB (frontend/inventario/controller/doc_imprimir_dlb.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Imprimir inventario centros (frontend/inventario/controller/doc_imprimir_ctr.php)
- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —

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

- Imprimir inventario DLB (frontend/inventario/controller/doc_imprimir_dlb.php)
- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —

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

- Casas posibles (frontend/inventario/controller/equipajes_casas_posibles.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Traslado de documentos — filtro (frontend/inventario/controller/traslado_doc_que.php)
- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** sin entrada de menú en el índice

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

- Inventario por centros (frontend/inventario/controller/doc_de_ctr.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Documentos ya asignados (frontend/inventario/controller/doc_asignado.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Formulario asignar a centros (frontend/inventario/controller/doc_asignar_ctr.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Formulario asignar DLB (frontend/inventario/controller/doc_asignar_dlb.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Documentos con observaciones (frontend/inventario/controller/docs_con_observaciones.php)
- **Legacy:** scdl > Inventario > inventarios > lista docs con observ.
- **Pills2:** sin entrada de menú en el índice

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

- Traslado — lista y guardar (frontend/inventario/controller/traslado_doc_lista.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Inventario DLB/casa (frontend/inventario/controller/doc_de_dlb.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Quitar doc de maleta (frontend/inventario/controller/equipajes_form_del.php)
- Lista docs EGM/lugar (frontend/inventario/controller/equipajes_lista_docs.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Lista docs EGM/lugar (frontend/inventario/controller/equipajes_lista_docs.php)
- Ver docs de lugar (frontend/inventario/controller/equipajes_ver_docs.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Documentos pendientes (frontend/inventario/controller/docs_en_busqueda.php)
- **Legacy:** scdl > Inventario > inventarios > lista docs pendientes
- **Pills2:** sin entrada de menú en el índice

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

- Docs libres (frontend/inventario/controller/equipajes_docs_libres.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Centros sin documento (frontend/inventario/controller/doc_no_asignado.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Documentos perdidos (frontend/inventario/controller/docs_perdidos.php)
- **Legacy:** scdl > Inventario > inventarios > lista docs perdidos
- **Pills2:** sin entrada de menú en el índice

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

- Desplegable equipajes (frontend/inventario/controller/equipajes_desplegable.php)
- Movimientos maletas — filtro (frontend/inventario/controller/equipajes_movimientos_que.php)
- Gestionar equipajes (frontend/inventario/controller/equipajes_ver.php)
- **Legacy:** scdl > Inventario > equipajes > movimientos maletas
- **Pills2:** sin entrada de menú en el índice

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

- Elegir maleta/grupo (frontend/inventario/controller/equipajes_posibles_maletas.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Traslado de documentos — filtro (frontend/inventario/controller/traslado_doc_que.php)
- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** sin entrada de menú en el índice

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

- Asignar documentos — selector (frontend/inventario/controller/docs_asignar_que.php)
- Añadir doc a maleta (frontend/inventario/controller/equipajes_form_add.php)
- **Legacy:** scdl > Inventario > documentos > asignar documento
- **Pills2:** sin entrada de menú en el índice

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

- Editar texto listado (frontend/inventario/controller/equipajes_form_texto_listado.php)
- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)

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

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** scdl > Inventario > inventarios > traslado de doc
- **Pills2:** —

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

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/inventario/flujos/`.
