# Checklist QA por Grupmenu

Formato pensado para pruebas manuales. Marca OK/KO y añade observaciones.

## Grupo 2

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| buscar n (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| n r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_numerarios&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| n de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=n&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Actualizar datos desde BDU (Sincronizar con los datos de Listas) | `apps/dbextern/controller/sincro_index.php`\n`tipo=n` | [[dbextern/mapa_sincro_index]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| crt (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar crt (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad=crt&que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar crt (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=n&sactividad=crt&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| list varios crt (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad=crt&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| list por ctr (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_activ&n_agd=n&sasistentes=n&sactividad=crt` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| pendientes (actividades pendientes) | `apps/asistentes/controller/activ_pendientes_select.php`\n`sactividad=crt&tipo_personas=n` | [[asistentes/mapa_activ_pendientes_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=n&sactividad=crt` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Balance de plazas (Balance de Plazas) | `apps/actividadplazas/controller/plazas_balance_que.php`\n`sasistentes=n&sactividad=crt` | [[actividadplazas/mapa_plazas_balance_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Incorporar 1ª petición (Incorporar peticiones de plazas) | `apps/actividadplazas/controller/incorporar_peticion.php`\n`sasistentes=n&sactividad=crt` | [[actividadplazas/mapa_incorporar_peticion]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`sasistentes=n&sactividad=crt` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ca (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar ca (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad=ca&que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar ca (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`extendido=1&sasistentes=n&sactividad=ca&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista varios ca (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad=ca&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista por ctr (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_activ&n_agd=n&sasistentes=n&sactividad=ca` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| pendientes (actividades pendientes) | `apps/asistentes/controller/activ_pendientes_select.php`\n`sactividad=ca&tipo_personas=n` | [[asistentes/mapa_activ_pendientes_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=n&sactividad=ca` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Balance de plazas (Balance de Plazas) | `apps/actividadplazas/controller/plazas_balance_que.php`\n`sasistentes=n&sactividad=ca` | [[actividadplazas/mapa_plazas_balance_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Incorporar 1ª petición (Incorporar peticiones de plazas) | `apps/actividadplazas/controller/incorporar_peticion.php`\n`sasistentes=n&sactividad=ca` | [[actividadplazas/mapa_incorporar_peticion]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`sasistentes=n&sactividad=ca` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cve (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar cve (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=n&sactividad=cve&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar cve (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad=cve&que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista varias cve (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad=cve&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista por ctr (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_activ&n_agd=n&sasistentes=n&sactividad=cve` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=n&sactividad=cve` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`sasistentes=n&sactividad=cve` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| planning (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| persona r/dl vsm (Planning persona) | `apps/planning/controller/planning_persona_que.php`\n`obj_pau=PersonaDl` | [[planning/mapa_planning_persona_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por centro (planning ctr) | `apps/planning/controller/planning_ctr_que.php` | [[planning/mapa_planning_ctr_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 3

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| buscar n (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Actualizar datos desde BDU (Sincronizar con los datos de Listas) | `apps/dbextern/controller/sincro_index.php`\n`tipo=a` | [[dbextern/mapa_sincro_index]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| crt (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar crt (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=agd&sactividad=crt&que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar crt (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=agd&sactividad=crt&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista por ctr (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_activ&n_agd=a&sasistentes=agd&sactividad=crt` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| list varios crt (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=agd&sactividad=crt&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=agd&sactividad=crt` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Balance de plazas (Balance de Plazas) | `apps/actividadplazas/controller/plazas_balance_que.php`\n`sasistentes=agd&sactividad=crt` | [[actividadplazas/mapa_plazas_balance_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Incorporar 1ª petición (Incorporar peticiones de plazas) | `apps/actividadplazas/controller/incorporar_peticion.php`\n`sasistentes=agd&sactividad=crt` | [[actividadplazas/mapa_incorporar_peticion]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`id_tipo_activ=131` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cv (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar cv (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=agd&sactividad=cv&que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar cv (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=agd&sactividad=cv&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| list varias cv (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=agd&sactividad=cv&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| list por centros (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_activ&n_agd=a&sasistentes=agd&sactividad=cv` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| pendientes (actividades pendientes) | `apps/asistentes/controller/activ_pendientes_select.php`\n`sactividad=ca&tipo_personas=agd` | [[asistentes/mapa_activ_pendientes_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=agd&sactividad=cv` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Balance de plazas (Balance de Plazas) | `apps/actividadplazas/controller/plazas_balance_que.php`\n`sasistentes=agd&sactividad=cv` | [[actividadplazas/mapa_plazas_balance_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Incorporar 1ª petición (Incorporar peticiones de plazas) | `apps/actividadplazas/controller/incorporar_peticion.php`\n`sasistentes=agd&sactividad=cv` | [[actividadplazas/mapa_incorporar_peticion]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`sasistentes=agd&sactividad=ca` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cve (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar cve agd (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=agd&sactividad=cve` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar cve (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=agd&sactividad=cve&que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=agd&sactividad=cve` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`sasistentes=agd&sactividad=cve` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| planning  (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| persona r/dl (Planning persona) | `apps/planning/controller/planning_persona_que.php`\n`obj_pau=PersonaDl` | [[planning/mapa_planning_persona_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por centro (planning ctr) | `apps/planning/controller/planning_ctr_que.php` | [[planning/mapa_planning_ctr_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 4

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| direcciones (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar (buscar ubis) | `apps/ubis/controller/ubis_buscar.php`\n`simple=1` | [[ubis/mapa_ubis_buscar]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listados (listas de ubis) | `apps/ubis/controller/list_ctr.php` | [[ubis/mapa_list_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| modificar centros (Centros seleccionar) | `apps/ubis/controller/centros_que.php` | [[ubis/mapa_centros_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cartas presentacion (Raiz cartas presentacion) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| modificar (Cartas Presentacion) | `apps/cartaspresentacion/controller/cartas_presentacion_que.php` | [[cartaspresentacion/mapa_cartas_presentacion_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista todo (Listado cartas presentación) | `apps/cartaspresentacion/controller/cartas_presentacion_lista.php`\n`que=lista_todo` | [[cartaspresentacion/mapa_cartas_presentacion_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista dl (Listado cartas presentación) | `apps/cartaspresentacion/controller/cartas_presentacion_lista.php`\n`que=lista_dl` | [[cartaspresentacion/mapa_cartas_presentacion_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar (Buscar Cartas presentacion) | `apps/cartaspresentacion/controller/cartas_presentacion_buscar.php` | [[cartaspresentacion/mapa_cartas_presentacion_buscar]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| planning (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| persona dl (Planning persona) | `apps/planning/controller/planning_persona_que.php`\n`obj_pau=PersonaDl` | [[planning/mapa_planning_persona_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| num de paso (Planning persona) | `apps/planning/controller/planning_persona_que.php`\n`obj_pau=PersonaEx&na=n` | [[planning/mapa_planning_persona_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| agd de paso (Planning persona) | `apps/planning/controller/planning_persona_que.php`\n`obj_pau=PersonaEx&na=a` | [[planning/mapa_planning_persona_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por ctr (planning ctr) | `apps/planning/controller/planning_ctr_que.php` | [[planning/mapa_planning_ctr_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| inventario (Raiz documentos) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Colecciones (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\inventario\domain\InfoColeccion` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| documentos (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipos de documentos (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\inventario\domain\InfoTipoDoc` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por centros (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\inventario\domain\InfoDocsxCtr` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por sigla (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\inventario\domain\InfoDocsxSigla` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| asignar documento (asignar documentos) | `frontend/inventario/controller/docs_asignar_que.php` | [[inventario/mapa_docs_asignar_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| centros (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\inventario\domain\InfoUbiInventario` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lugares (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\inventario\domain\InfoLugar` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nuevo equipaje (Nuevo Documento) | `frontend/inventario/controller/equipajes_nuevo.php` | [[inventario/mapa_equipajes_nuevo]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| hacer equipajes (Ver equipajes) | `frontend/inventario/controller/equipajes_ver.php` | [[inventario/mapa_equipajes_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| eliminar equipajes (Ver equipajes) | `frontend/inventario/controller/equipajes_ver.php`\n`eliminar=1` | [[inventario/mapa_equipajes_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipos de texto (textos inventario) | `frontend/inventario/controller/cabecera_pie_txt.php` | [[inventario/mapa_cabecera_pie_txt]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| imprimir equipajes (Ver equipajes) | `frontend/inventario/controller/equipajes_ver.php`\n`imprimir=1` | [[inventario/mapa_equipajes_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| movimientos maletas (Equipajes movimientos) | `frontend/inventario/controller/equipajes_movimientos_que.php` | [[inventario/mapa_equipajes_movimientos_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| de centros o dlb (inventario buscar) | `frontend/inventario/controller/inventario_que.php` | [[inventario/mapa_inventario_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista docs pendientes (Lista documentos en búsqueda) | `frontend/inventario/controller/docs_en_busqueda.php` | [[inventario/mapa_docs_en_busqueda]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista docs perdidos (Lista documentos perdidos) | `frontend/inventario/controller/docs_perdidos.php` | [[inventario/mapa_docs_perdidos]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista docs con observ. (Lista doscumentos con observaciones) | `frontend/inventario/controller/docs_con_observaciones.php` | [[inventario/mapa_docs_con_observaciones]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| traslado de doc (Traslado Documentos) | `frontend/inventario/controller/traslado_doc_que.php` | [[inventario/mapa_traslado_doc_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 7

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| Planing Casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listado gerentes (Casa que) | `apps/casas/controller/casa_que.php`\n`periodo=any_actual&tipo_lista=ctrsEncargados&ver_ctr=si` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión económica (Casa que) | `apps/casas/controller/casa_que.php` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| gastos casas (Casa que) | `apps/casas/controller/casa_que.php`\n`tipo_lista=datosEcGastos` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| estadística  por casas (Casa resumen) | `apps/casas/controller/casas_resumen.php`\n`tipo=planning_cdc&ssfsv=sf` | [[casas/mapa_casas_resumen]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| estadística por años (Casa ec que) | `apps/casas/controller/casa_ec_que.php` | [[casas/mapa_casa_ec_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| grupos (Grupo Casas) | `apps/casas/controller/grupo_lista.php` | [[casas/mapa_grupo_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Definir periodos (Calendario Periodos) | `apps/ubis/controller/calendario_periodos.php` | [[ubis/mapa_calendario_periodos]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nuevo planing (Calendario que) | `apps/actividades/controller/calendario_que.php` | [[actividades/mapa_calendario_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Cases comunes (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_comunes&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listados de todas las casas (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_todas&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listado por oficinas (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=o_todas&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Estadística por años (Casa ec que) | `apps/casas/controller/casa_ec_que.php`\n`periodo=no&tipo_lista=datosEc` | [[casas/mapa_casa_ec_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Previsión económica (Ubi resumen ec) | `apps/casas/controller/calendario_ubi_resumen.php` | [[casas/mapa_calendario_ubi_resumen]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| previsión asistentes (prevision asistentes) | `apps/casas/controller/prevision_asistentes.php` | [[casas/mapa_prevision_asistentes]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| definir tarifa (Tarifa definir) | `apps/actividadtarifas/controller/tarifa.php` | [[actividadtarifas/mapa_tarifa]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tarifa <-> tipo de actividad (Tipo tarifa) | `apps/actividadtarifas/controller/tarifa_tipo_actividad.php` | [[actividadtarifas/mapa_tarifa_tipo_actividad]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tarifas por casa y año (actividad Tarifa Ubi) | `apps/actividadtarifas/controller/tarifa_ubi.php` | [[actividadtarifas/mapa_tarifa_ubi]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ -> tarifa (Tarifa actividades) | `apps/actividadtarifas/controller/tarifa_actividad.php` | [[actividadtarifas/mapa_tarifa_actividad]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 8

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| personas (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sacd num (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_numerarios&na=n&tipo=persona&es_sacd=1` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| n de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=n&tipo=persona&es_sacd=1` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sacd agd (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_agregados&na=a&tipo=persona&es_sacd=1` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| agd de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=a&tipo=persona&es_sacd=1` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sacd sssc (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_sssc&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sssc de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=sss&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cp & as.ecles (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_cp_ae_sssc&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista varias activ (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`ssfsv=sv&sasistentes=n&que=list_cjto_sacd` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ca pendientes (actividades pendientes) | `apps/asistentes/controller/activ_pendientes_select.php`\n`sactividad=ca&tipo_personas=sacd` | [[asistentes/mapa_activ_pendientes_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| crt pendientes (actividades pendientes) | `apps/asistentes/controller/activ_pendientes_select.php`\n`sactividad=crt&tipo_personas=sacd` | [[asistentes/mapa_activ_pendientes_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Actualizar datos desde BDU (Sincronizar con los datos de Listas) | `apps/dbextern/controller/sincro_index.php`\n`tipo=sssc` | [[dbextern/mapa_sincro_index]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| centros y casas (Raiz ubis) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar (buscar ubis) | `apps/ubis/controller/ubis_buscar.php`\n`simple=1&tipo=ctr&loc=dl` | [[ubis/mapa_ubis_buscar]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listados (listas de ubis) | `apps/ubis/controller/list_ctr.php` | [[ubis/mapa_list_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| zonas (Raiz Zonas) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| zonas geogr. (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\zonassacd\domain\InfoZona` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| zonas (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\zonassacd\domain\InfoZona` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| zonas-ctr (Zona-ctr) | `apps/zonassacd/controller/zona_ctr.php` | [[zonassacd/mapa_zona_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| zonas-sacd (Zona sacd) | `apps/zonassacd/controller/zona_sacd.php` | [[zonassacd/mapa_zona_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista sacd-zona (Zona sacd ajax) | `apps/zonassacd/controller/zona_sacd_ajax.php`\n`que=get_lista_tot` | [[zonassacd/mapa_zona_sacd_ajax]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| planning (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sacd r/dl des (Planning persona) | `apps/planning/controller/planning_persona_que.php`\n`obj_pau=PersonaSacd&tipo=planning` | [[planning/mapa_planning_persona_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por centro (planning ctr) | `apps/planning/controller/planning_ctr_que.php` | [[planning/mapa_planning_ctr_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por zonas (Planning por zonas) | `apps/planning/controller/planning_zones_que.php` | [[planning/mapa_planning_zones_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| actividades (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar activ (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=ver` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sv  n (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=11&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sv agd (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=13&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sv s y sg (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=1[45]&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sv sr (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=17&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf n (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=21&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf nax (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=22&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf agd (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=23&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf s y sg (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=2[45]&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf sr,sr-nax,sr-agd (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=2[789]&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar activ (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| publicar activ (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=ver&modo=publicar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nueva activ (actividad nueva) | `apps/actividades/controller/actividad_ver.php` | [[actividades/mapa_actividad_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| comunic. sacd (Comunicación activ sacd) | `apps/actividadessacd/controller/com_sacd_activ_periodo.php` | [[actividadessacd/mapa_com_sacd_activ_periodo]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listas sg (Lista Activ que) | `apps/actividades/controller/lista_activ_que.php`\n`que=list_activ_inv_sg` | [[actividades/mapa_lista_activ_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| asignar centros (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sg (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sg` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sr (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sr` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sv n y agd (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=nagd` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf s y sg (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sfsg` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf sr (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sfsr` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf n, nax y agd (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sfnagd` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sss+ (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sssc` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sssc (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar activ (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=ver&ssfsv=sv&sasistentes=sss%2B` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| propuesta cl cv (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=sss+&sactividad=cv&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| planing zonas (Planning por zonas) | `apps/planning/controller/planning_zones_que.php`\n`propuesta=true` | [[planning/mapa_planning_zones_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista activ. sacd (Comunicación activ sacd) | `apps/actividadessacd/controller/com_sacd_activ_periodo.php`\n`propuesta=true` | [[actividadessacd/mapa_com_sacd_activ_periodo]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| asignar sacd (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sv sg (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=sg&periodo=desdeHoy` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sv sr (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=sr` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sv n y agd (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=na` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sf sg (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=sf_sg` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sf sr (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=sf_sr` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sf n,nax y agd (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=sf_na` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ sss+ (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=sssc` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| falta sacd (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=falta_sacd` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| solapes (Atención sacd) | `apps/actividadessacd/controller/activ_sacd.php`\n`tipo=solape` | [[actividadessacd/mapa_activ_sacd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Encargos (Raiz encargos sacd) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| propuestas (propuestas encargos sacd) | `apps/encargossacd/controller/propuestas_menu.php` | [[encargossacd/mapa_propuestas_menu]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ficha ctr (Ficha encargos ctr) | `apps/encargossacd/controller/ctr_ficha.php` | [[encargossacd/mapa_ctr_ficha]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ficha sacd (Encargo sacd ficha) | `apps/encargossacd/controller/sacd_ficha.php` | [[encargossacd/mapa_sacd_ficha]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listados (Encargos Listas) | `apps/encargossacd/controller/listas_index.php` | [[encargossacd/mapa_listas_index]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipo encargo (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\encargossacd\domain\InfoEncargoTipo` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ver encargo (Encargos lista) | `apps/encargossacd/controller/encargo_select.php` | [[encargossacd/mapa_encargo_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ausencias (Raiz encargos sacd) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sacd (Ausencias sacd) | `apps/encargossacd/controller/sacd_ausencias.php` | [[encargossacd/mapa_sacd_ausencias]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Nuevo calendario (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Definir periodos (Calendario Periodos) | `apps/ubis/controller/calendario_periodos.php` | [[ubis/mapa_calendario_periodos]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nuevo curso (Nuevo Curso) | `apps/actividades/controller/actividad_nuevo_curso.php` | [[actividades/mapa_actividad_nuevo_curso]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nuevo planing (Planing Casa) | `apps/planning/controller/planning_casa_que.php`\n`propuesta_calendario=1` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| casas comunes (des) (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_comunes&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| casas comunes sf (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_comunes_sf` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| casas comunes sv (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_comunes_sv&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| todas casas (des) (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_todas&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| todas casas sf (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_todas_sf&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| todas casas sv (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_todas_sv&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listado por oficinas (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=o_todas&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listado oficina propia (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=o_actual&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| definir tarifa (Tarifa definir) | `apps/actividadtarifas/controller/tarifa.php` | [[actividadtarifas/mapa_tarifa]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tarifa <-> tipo actividad (Tipo tarifa) | `apps/actividadtarifas/controller/tarifa_tipo_actividad.php` | [[actividadtarifas/mapa_tarifa_tipo_actividad]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tarifas por casa y año (actividad Tarifa Ubi) | `apps/actividadtarifas/controller/tarifa_ubi.php` | [[actividadtarifas/mapa_tarifa_ubi]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Pasarela (Raiz pasarela) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| parámetros (pasarela parametros) | `apps/pasarela/controller/parametros_menu.php` | [[pasarela/mapa_parametros_menu]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| exportar actividades (exportar actividades) | `apps/pasarela/controller/exportar_que.php` | [[pasarela/mapa_exportar_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Misas (Raiz Misas) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| aaaaaaaaaaaaa (llista de modul misas) | `apps/misas/controller/misas_index.php` | [[misas/mapa_misas_index]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| resto (listas de matriculas) | `apps/actividadestudios/controller/matriculas_lista.php` | [[actividadestudios/mapa_matriculas_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 9

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| planning zonas (Planning por zonas) | `apps/planning/controller/planning_zones_que.php` | [[planning/mapa_planning_zones_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| atención actividades (Comunicación activ sacd) | `apps/actividadessacd/controller/com_sacd_activ_periodo.php` | [[actividadessacd/mapa_com_sacd_activ_periodo]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| casas (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar (buscar ubis) | `apps/ubis/controller/ubis_buscar.php`\n`simple=1&tipo=tot&loc=tot` | [[ubis/mapa_ubis_buscar]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| planning por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listado gerentes (Casa que) | `apps/casas/controller/casa_que.php`\n`periodo=any_actual&tipo_lista=ctrsEncargados&ver_ctr=si` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista actividades (Casa que) | `apps/casas/controller/casa_que.php`\n`periodo=tot_any&tipo_lista=lista_activ` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| gastos casa (Casa que) | `apps/casas/controller/casa_que.php`\n`tipo_lista=datosEcGastos` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión económica (Casa que) | `apps/casas/controller/casa_que.php` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| definir tarifa (Tarifa definir) | `apps/actividadtarifas/controller/tarifa.php` | [[actividadtarifas/mapa_tarifa]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tarifa <-> tipo de actividad (Tipo tarifa) | `apps/actividadtarifas/controller/tarifa_tipo_actividad.php` | [[actividadtarifas/mapa_tarifa_tipo_actividad]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tarifas por casa y año (actividad Tarifa Ubi) | `apps/actividadtarifas/controller/tarifa_ubi.php` | [[actividadtarifas/mapa_tarifa_ubi]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| activ -> tarifa (Tarifa definir) | `apps/actividadtarifas/controller/tarifa.php` | [[actividadtarifas/mapa_tarifa]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=ver` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista por casas (Casa que) | `apps/casas/controller/casa_que.php`\n`periodo=any_actual&tipo_lista=ctrsEncargados&ver_ctr=si` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 10

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| s de la r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_supernumerarios` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Actualizar datos desde BDU (Sincronizar con los datos de Listas) | `apps/dbextern/controller/sincro_index.php`\n`tipo=s` | [[dbextern/mapa_sincro_index]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista ctr i nº s (lista ctr sg) | `apps/ubis/controller/lista_ctrs.php` | [[ubis/mapa_lista_ctrs]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar cv (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=s&sactividad=cv&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| de la r/dl (lista activ sg) | `apps/actividades/controller/lista_actividades_sg.php`\n`tipo_activ_sg=cv` | [[actividades/mapa_lista_actividades_sg]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| de cada ctr (Centro que activ) | `apps/actividades/controller/actividades_centro_que.php`\n`tipo_ctr=sg&tipo_lista=cv` | [[actividades/mapa_actividades_centro_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| este curso (Ultima asistencia) | `apps/asistentes/controller/lista_ultim_que_ctr.php`\n`que=cv_s&curso=actual` | [[asistentes/mapa_lista_ultim_que_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| el curso pasado (Ultima asistencia) | `apps/asistentes/controller/lista_ultim_que_ctr.php`\n`que=cv_s&curso=anterior` | [[asistentes/mapa_lista_ultim_que_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| corresponde ir a cv ad (Ultima asistencia) | `apps/asistentes/controller/lista_ultim_que_ctr.php`\n`que=cv_s_ad` | [[asistentes/mapa_lista_ultim_que_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| corresponde ir a cv joves (Ultima asistencia) | `apps/asistentes/controller/lista_ultim_que_ctr.php`\n`que=cv_jovenes` | [[asistentes/mapa_lista_ultim_que_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista cargos (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=s&sactividad=cv&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=s&sactividad=cv` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Balance de plazas (Balance de Plazas) | `apps/actividadplazas/controller/plazas_balance_que.php`\n`sasistentes=s&sactividad=cv` | [[actividadplazas/mapa_plazas_balance_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`sasistentes=s&sactividad=cv` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar crt s (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=s&sactividad=crt` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar crt sg (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=sg&sactividad=crt` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| de la r/dl (lista activ sg) | `apps/actividades/controller/lista_actividades_sg.php`\n`tipo_activ_sg=crt` | [[actividades/mapa_lista_actividades_sg]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| de cada ctr (Centro que activ) | `apps/actividades/controller/actividades_centro_que.php`\n`tipo_ctr=sg&tipo_lista=crt` | [[actividades/mapa_actividades_centro_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| este curso (Ultima asistencia) | `apps/asistentes/controller/lista_ultim_que_ctr.php`\n`que=crt_s_sg&curso=actual` | [[asistentes/mapa_lista_ultim_que_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| el curso pasado (Ultima asistencia) | `apps/asistentes/controller/lista_ultim_que_ctr.php`\n`que=crt_s_sg&curso=anterior` | [[asistentes/mapa_lista_ultim_que_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| s que han de hacer crt interno (Ultima asistencia) | `apps/asistentes/controller/lista_ultim_que_ctr.php`\n`que=crt_s` | [[asistentes/mapa_lista_ultim_que_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| s que han de hacer el crt de cel (Ultima asistencia) | `apps/asistentes/controller/lista_ultim_que_ctr.php`\n`que=crt_cel` | [[asistentes/mapa_lista_ultim_que_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=s,sg&sactividad=crt` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Balance de plazas (Balance de Plazas) | `apps/actividadplazas/controller/plazas_balance_que.php`\n`sasistentes=s&sactividad=crt` | [[actividadplazas/mapa_plazas_balance_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`sasistentes=s&sactividad=crt` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| actividades-centros (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sg` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| n r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_numerarios&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| n de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=n&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| agd r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_agregados&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| agd de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=a&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| persona r/dl (Planning persona) | `apps/planning/controller/planning_persona_que.php`\n`obj_pau=PersonaDl` | [[planning/mapa_planning_persona_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por centro (planning ctr) | `apps/planning/controller/planning_ctr_que.php` | [[planning/mapa_planning_ctr_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 12

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| buscar persona (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| n r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_numerarios&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| n de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=n&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| agd r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_agregados&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| agd de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=a&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| matricular a todos (matricular) | `apps/actividadestudios/controller/matricular.php` | [[actividadestudios/mapa_matricular]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| profesor para asignatura (profesores para asignatura) | `apps/profesores/controller/profesor_asignatura_que.php` | [[profesores/mapa_profesor_asignatura_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| claustro (listado claustro) | `apps/profesores/controller/lista_por_departamentos.php` | [[profesores/mapa_lista_por_departamentos]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| actas... (Raiz stgr) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| actas (actas) | `apps/notas/controller/acta_select.php` | [[notas/mapa_acta_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tabla alumnos-asignaturas (cuadro alumnos-asignaturas) | `apps/notas/controller/asignaturas_pendientes.php` | [[notas/mapa_asignaturas_pendientes]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| resúmenes (resumen anual) | `apps/notas/controller/resumen_anual.php` | [[notas/mapa_resumen_anual]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Matr. Pendientes (matriculas pendientes) | `apps/actividadestudios/controller/matriculas_pendientes.php` | [[actividadestudios/mapa_matriculas_pendientes]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Matrículas (listas de matriculas) | `apps/actividadestudios/controller/matriculas_lista.php` | [[actividadestudios/mapa_matriculas_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| resumen pendientes (Resumen asig. pendientes) | `apps/notas/controller/asignaturas_pendientes_resumen.php` | [[notas/mapa_asignaturas_pendientes_resumen]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar asig. pendientes (Asignaturas pendientes) | `apps/notas/controller/asig_faltan_que.php` | [[notas/mapa_asig_faltan_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ver docencia (Docencia) | `apps/profesores/controller/docencia.php` | [[profesores/mapa_docencia]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| asitencia a congresos (congresos) | `apps/profesores/controller/congresos.php` | [[profesores/mapa_congresos]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| certificados (Certificado lista) | `frontend/certificados/controller/certificado_emitido_lista.php` | [[certificados/mapa_certificado_emitido_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| posibles ca (Raiz stgr 2) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| posibles ca (Posibles ca) | `apps/actividadestudios/controller/ca_posibles_que.php` | [[actividadestudios/mapa_ca_posibles_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ca n (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar ca (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad=ca&que=ver` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista varios ca (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad=ca&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista por ctr (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_activ&n_agd=n&sasistentes=n&sactividad=ca` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| estudios x ctr (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_est&n_agd=n&sasistentes=n&sactividad=ca` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| pendientes (actividades pendientes) | `apps/asistentes/controller/activ_pendientes_select.php`\n`sactividad=ca&tipo_personas=n` | [[asistentes/mapa_activ_pendientes_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`id_tipo_activ=133` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cv agd (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar cv (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=agd&sactividad=cv&que=ver` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| list varias cv (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=agd&sactividad=cv&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista por ctr (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_activ&n_agd=a&sasistentes=agd&sactividad=cv` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| estudios x ctr (Buscar por centro) | `apps/asistentes/controller/que_ctr_lista.php`\n`lista=list_est&n_agd=a&sasistentes=n&sactividad=cv` | [[asistentes/mapa_que_ctr_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| pendientes (actividades pendientes) | `apps/asistentes/controller/activ_pendientes_select.php`\n`sactividad=ca&tipo_personas=agd` | [[asistentes/mapa_activ_pendientes_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`id_tipo_activ=112` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sem inv. (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=n&sactividad2=semestre-inv&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| gestión de plazas (Gestión de plazas) | `apps/actividadplazas/controller/gestion_plazas.php`\n`sasistentes=n&sactividad2=semestre-inv` | [[actividadplazas/mapa_gestion_plazas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=n&sactividad2=semestre-inv&que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php`\n`sasistentes=n&sactividad2=semestre-inv` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| planning (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| persona r/dl (Planning persona) | `apps/planning/controller/planning_persona_que.php`\n`obj_pau=PersonaDl` | [[planning/mapa_planning_persona_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por centro (planning ctr) | `apps/planning/controller/planning_ctr_que.php` | [[planning/mapa_planning_ctr_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| mantenimiento (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| dic latin (modificar los datos de una tabla) | `apps/core/mod_tabla_sql.php`\n`clase_info=personas\model\InfoLatin` | [[core/mapa_mod_tabla_sql]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| actualizar docencia (guardar docencia ca en dossier) | `apps/actividadestudios/controller/actualizar_docencia.php` | [[actividadestudios/mapa_actualizar_docencia]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 13

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| menus (Raiz admin) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| seleccionar (gestor de menus) | `frontend/menus/controller/menus_que.php` | [[menus/mapa_menus_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar (importar menus) | `frontend/menus/controller/menus_importar_form.php` | [[menus/mapa_menus_importar_form]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| meta menus (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\menus\domain\InfoMetaMenus` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| exportar (exportar menus) | `frontend/menus/controller/menus_exportar_form.php` | [[menus/mapa_menus_exportar_form]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| exportar a ficheros (menu a/de ficheros) | `src/menus/frontend/controller/menus_ficheros.php`\n`accion=exportar` | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar desde ficheros (menu a/de ficheros) | `src/menus/frontend/controller/menus_ficheros.php`\n`accion=importar` | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ayuda (ayuda menus) | `frontend/menus/view/como.html` | [[menus/mapa_como]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| DB (Raiz admin) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nuevo esquema (nuevo esquema) | `apps/devel/controller/db_que.php` | [[devel/mapa_db_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| mover tabla a otra DB (DB mover tabla) | `apps/devel/controller/db_mover_que.php` | [[devel/mapa_db_mover_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| mover y cambiar nombre dl (cambiar nombre dl) | `apps/devel/controller/db_cambiar_nombre_que.php` | [[devel/mapa_db_cambiar_nombre_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| borrar passwords (borrar passwords de todos los usuarios) | `frontend/usuarios/controller/borrar_todos_pwd.php` | [[usuarios/mapa_borrar_todos_pwd]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| devel (Raiz devel) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| factory (generador de clases) | `apps/devel/controller/factory_form.php` | [[devel/mapa_factory_form]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| perm_dossiers (Raiz admin) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| personas (permisos dossiers) | `apps/dossiers/controller/perm_dossiers.php`\n`tipo=p` | [[dossiers/mapa_perm_dossiers]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ubis (permisos dossiers) | `apps/dossiers/controller/perm_dossiers.php`\n`tipo=u` | [[dossiers/mapa_perm_dossiers]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| actividades (permisos dossiers) | `apps/dossiers/controller/perm_dossiers.php`\n`tipo=a` | [[dossiers/mapa_perm_dossiers]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| procesos activ. (Raiz procesos) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| fases (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\procesos\domain\InfoFases` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| fases-tareas (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\procesos\domain\InfoTareas` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipos de procesos (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\procesos\domain\InfoProcesoTipo` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| procesos (procesos select) | `apps/procesos/controller/procesos_select.php` | [[procesos/mapa_procesos_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipo activ - proceso (Tipos de actividad) | `apps/procesos/controller/tipo_activ_proceso.php` | [[procesos/mapa_tipo_activ_proceso]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| usuarios web (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista de roles (lista roles) | `frontend/usuarios/controller/role_lista.php` | [[usuarios/mapa_role_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| grup menu (lista grupmenus) | `frontend/menus/controller/grupmenu_lista.php` | [[menus/mapa_grupmenu_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista usuarios (lista usuarios) | `frontend/usuarios/controller/usuario_lista.php` | [[usuarios/mapa_usuario_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista grupos (lista grupos) | `frontend/usuarios/controller/grupo_lista.php` | [[casas/mapa_grupo_lista]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| generar tabla avisos (avisos generar tabla) | `apps/cambios/controller/avisos_generar_tabla.php` | [[cambios/mapa_avisos_generar_tabla]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ver lista cambios (avisos generar) | `apps/cambios/controller/avisos_generar.php` | [[cambios/mapa_avisos_generar]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Configuración (Raiz admin) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| aplicaciones (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\configuracion\domain\InfoApps` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| definir módulos (modulos select) | `frontend/configuracion/controller/modulos_select.php` | [[configuracion/mapa_modulos_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| instalar módulos (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\configuracion\domain\InfoModsInstalled` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Tablas de apps (Manage tablas de Apps) | `apps/devel/controller/apptables.php` | [[devel/mapa_apptables]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| config esquema (Configuracion esquema) | `frontend/configuracion/controller/parametros.php` | [[configuracion/mapa_parametros]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| gestión Tipos actividades (gestionar tipos actividades) | `apps/actividades/controller/tipo_activ.php` | [[actividades/mapa_tipo_activ]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| traducciones (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| menus a texto (pasa los menus a texto) | `src/menus/infrastructure/controllers/menus_generar_txt.php` | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| posibles idiomas (modificar los datos de una tabla) | `apps/core/mod_tabla_sql.php`\n`clase_info=usuarios\model\InfoLocales` | [[core/mapa_mod_tabla_sql]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 15

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| n r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_numerarios&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| n de paso  (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=n&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| agd r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_agregados&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| agd de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=a&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar cv (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=sr&sactividad=cv` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| lista cargos (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`sasistentes=sr&sactividad=cv&que=list_cjto` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tabla de cv (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=list_activ&ssfsv=sv&sasistentes=sr&sactividad=cv` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar crt (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=sr&sactividad=crt` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tabla de crt (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=list_activ&ssfsv=sv&sasistentes=sr&sactividad=crt` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listado csv (Activ Sr Que (csv)) | `apps/actividades/controller/lista_sr_csv_que.php` | [[actividades/mapa_lista_sr_csv_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| actividades-centros (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sr` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por ctr (planning ctr) | `apps/planning/controller/planning_ctr_que.php` | [[planning/mapa_planning_ctr_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por zonas (Planning por zonas) | `apps/planning/controller/planning_zones_que.php` | [[planning/mapa_planning_zones_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 16

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| buscar nax (Raiz base) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nax r/dl (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_nax&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nax de paso (búsqueda de personas) | `apps/personas/controller/personas_que.php`\n`tabla=p_de_paso&na=x&tipo=persona` | [[personas/mapa_personas_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| crt (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar crt (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=nax&sactividad=crt&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ca (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar ca (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=nax&sactividad=ca&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cve (Raiz activ1) | — | — | Sin mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar cve (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`sasistentes=nax&sactividad=cve&que=ver` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 19

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| tipos de repetición (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\actividades\domain\InfoTipoRepeticion` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nivel stgr (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\actividades\domain\InfoNivelStgr` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sectores (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\asignaturas\domain\InfoSectores` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| departamentos (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\asignaturas\domain\InfoDepartamentos` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| opcionales (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\asignaturas\domain\InfoOpcionales` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| asignaturas (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\asignaturas\domain\InfoAsignaturas` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipos asignaturas (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\asignaturas\domain\InfoAsignaturaTipo` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| delegaciones (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\ubis\domain\InfoDelegaciones` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| regiones (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\ubis\domain\InfoRegiones` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipo de casa (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\ubis\domain\InfoTipoCasa` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipo de ctr (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\ubis\domain\InfoTipoCtr` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipos de situacion (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\personas\domain\InfoSituacion` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipos de profesor (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\profesores\domain\InfoProfesorTipo` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tipos de telecos (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\ubis\domain\InfoTipoTeleco` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| descripción teleco (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\ubis\domain\InfoDescTeleco` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cargos (modificar los datos de una tabla con Repository) | `frontend/shared/controller/tablaDB_lista_ver.php`\n`clase_info=src\actividadcargos\domain\InfoCargo` | [[shared/mapa_tablaDB_lista_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |

## Grupo 20

| Menú | Entrada (URL + parámetros) | Mapa | Flujo esperado | Resultado esperado | OK/KO | Observaciones |
|---|---|---|---|---|---|---|
| buscar (buscar ubis) | `apps/ubis/controller/ubis_buscar.php`\n`simple=1&tipo=ctr&loc=dl` | [[ubis/mapa_ubis_buscar]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listados (listas de ubis) | `apps/ubis/controller/list_ctr.php` | [[ubis/mapa_list_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por centro (planning ctr) | `apps/planning/controller/planning_ctr_que.php` | [[planning/mapa_planning_ctr_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| por casas (Planing Casa) | `apps/planning/controller/planning_casa_que.php` | [[planning/mapa_planning_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| buscar activ (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=ver` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf n (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=21....&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf nax (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=22....&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf agd (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=23....&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf s y sg (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=2[45]....&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf sr,sr-nax,sr-agd (Listado de Actividades) | `apps/actividades/controller/actividad_select.php`\n`id_tipo_activ=2[789]....&status=2&periodo=desdeHoy` | [[actividades/mapa_actividad_select]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| importar activ (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=ver&modo=importar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| publicar activ (seleccionar actividad) | `apps/actividades/controller/actividad_que.php`\n`que=ver&modo=publicar` | [[actividades/mapa_actividad_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nueva activ (actividad nueva) | `apps/actividades/controller/actividad_ver.php` | [[actividades/mapa_actividad_ver]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listas sg (Lista Activ que) | `apps/actividades/controller/lista_activ_que.php`\n`que=list_activ_inv_sg` | [[actividades/mapa_lista_activ_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listas gerentes (Casa que) | `apps/casas/controller/casa_que.php`\n`periodo=any_actual&tipo_lista=ctrsEncargados&ver_ctr=si` | [[casas/mapa_casa_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| asignar centros (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf s y sg (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sfsg` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf sr (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sfsr` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| sf n, nax y agd (actividades centro) | `apps/actividadescentro/controller/activ_ctr.php`\n`tipo=sfnagd` | [[actividadescentro/mapa_activ_ctr]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| cambiar de fase (Fases activ cambio) | `apps/procesos/controller/fases_activ_cambio.php` | [[procesos/mapa_fases_activ_cambio]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| ver lista cambios (avisos generar) | `apps/cambios/controller/avisos_generar.php` | [[cambios/mapa_avisos_generar]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| nuevo planing (Calendario que) | `apps/actividades/controller/calendario_que.php` | [[actividades/mapa_calendario_que]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| casas comunes (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_comunes&ver_ctr=si&periodo=actual` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| casas comunes sf (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_comunes_sf` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| todas casas sf (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=c_todas_sf&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listado por oficinas (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=o_todas&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| listado oficina propia (calendario listas) | `apps/actividades/controller/calendario_listas.php`\n`que=o_actual&ver_ctr=si` | [[actividades/mapa_calendario_listas]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| definir tarifa (Tarifa actividades) | `apps/actividadtarifas/controller/tarifa_actividad.php` | [[actividadtarifas/mapa_tarifa_actividad]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tarifa <-> tipo de actividad (Tipo tarifa) | `apps/actividadtarifas/controller/tarifa_tipo_actividad.php` | [[actividadtarifas/mapa_tarifa_tipo_actividad]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| tarifas por casa y año (actividad Tarifa Ubi) | `apps/actividadtarifas/controller/tarifa_ubi.php` | [[actividadtarifas/mapa_tarifa_ubi]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Previsión económica (Ubi resumen ec) | `apps/casas/controller/calendario_ubi_resumen.php` | [[casas/mapa_calendario_ubi_resumen]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
| Previsión asistentes (prevision asistentes) | `apps/casas/controller/prevision_asistentes.php` | [[casas/mapa_prevision_asistentes]] | Seguir mapa | Pantalla carga y acciones básicas funcionan |  |  |
