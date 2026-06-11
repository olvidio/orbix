---
tipo: manual_usuario
modulo: actividades
flujos: 28
estado_revision: revisado_parcial
---

# Manual De Usuario - actividades

Modulo **hub**: busqueda, alta, edicion, calendarios y tipos de actividad. Casi todos los menus de actividades (roles 2, 3, 8, 10, 20…) apuntan aqui.

## Acceso Por Menu (patrones)

| Accion | Controller | Parametros tipicos |
|--------|------------|-------------------|
| **Buscar** crt/ca/cv/cve | `actividad_select.php` | `sactividad`, `sasistentes`, `que=ver` |
| **Importar** | `actividad_que.php` | `modo=importar` |
| **List varios** | `actividad_que.php` | `que=list_cjto` |
| **Calendario casas** | `calendario_listas.php` | `que=c_comunes`, `c_comunes_sf/sv` |
| **Tipo actividad** | `tipo_activ.php` | Catalogo tipos |
| **Nuevo curso** | `actividad_nuevo_curso.php` | Duplicar actividades curso |

Parametros `sactividad`: `crt`, `ca`, `cv`, `cve`, etc. definen subconjunto y permisos.

## Conceptos Del Dominio (ficha de actividad)

- **Tipo de actividad** (`id_tipo_activ`, 6 digitos): se compone en cascada
  seccion (sf/sv) → asistentes → actividad → tipo. Los niveles sin concretar se
  representan con `.` en las busquedas.
- **Estado** (`status`): 1 proyecto, 2 actual, 3 terminada, 4 borrable
  (9 = cualquiera, solo como filtro). Con la app `procesos` instalada el estado
  no se cambia a mano: lo gobiernan las fases del proceso.
- **Organiza** (`dl_org`): si la actividad es de **otra dl**, el alta la crea
  como "externa" (queda publicada, en estado actual e importada automaticamente);
  no se permite crearla si esa dl ya usa Orbix (debe crearla ella).
- **Lugar**: una casa (`id_ubi`), un "lugar especial" en texto libre
  (`id_ubi=1` + `lugar_esp`) o "sin determinar".
- **Publicada**: visible para otras dl. Se publica en masa desde el modo
  publicar; se despublica editando la ficha.

## Buscar Y Abrir Actividades

1. Menu **Buscar …** segun rol (crt, ca, agd…).
2. Pantalla de filtros `actividad_que` (tipo en cascada, estado, nombre, lugar,
   organiza, publicada, periodo y fases si hay procesos). Los roles de centro
   no ven los filtros de lugar/organiza/publicada.
3. Listado (`actividad_select`) → abrir ficha (`actividad_ver`) con dossiers
   (asistentes, cargos, plazas, procesos…).

## Crear, Editar, Eliminar (tareas habituales)

- **¿Como creo una actividad?** Ficha en blanco (`actividad_ver`, modo nuevo):
  concretar el tipo completo en la cascada, rellenar nombre, fechas, estado y
  organiza (campos con `*`) y pulsar *crear ficha*. Con `procesos`, el sistema
  comprueba el permiso de crear para ese tipo y fija el estado inicial segun el
  proceso. Tras crear, el formulario se vacia para crear otra.
- **¿Como edito?** Abrir la ficha y *guardar cambios* (boton visible solo con
  permiso de modificar). Cambiar las plazas propaga el valor al modulo de
  plazas; cambiar el organiza desde/hacia la propia dl regenera el proceso.
- **¿Como cambio el tipo?** Accion *cambiar tipo* (ficha/listado): la actividad
  vuelve a proyecto y hay que volver a marcar las fases. El sistema sugiere
  regenerar el nombre.
- **¿Como elimino?** Desde los listados (`actividad_select`,
  `lista_actividades_sg`) con las actividades marcadas. Solo se borra de verdad
  si esta en *proyecto* y es de la propia dl; en otro caso queda *borrable*
  (o, si era importada, solo se quita la importacion). Con `procesos` se exige
  el permiso de borrar por actividad.
- **¿Como duplico?** Accion *duplicar* en el listado: copia **la primera**
  actividad marcada como `dup <nombre>` en estado proyecto.
- **¿Como importo actividades de otra dl?** Menu *Importar*
  (`actividad_que?modo=importar`) → marcar → importar. Pueden salir avisos de
  fases del proceso.
- **¿Como publico?** Menu *Publicar* (`actividad_que?modo=publicar`) → marcar →
  publicar (solo actividades de la propia dl).

## Calendario Y Listas Por Casa

- **Casas comunes** (roles 8, 20): `calendario_listas.php` — variantes sf/sv/comunes.
- Enlace a **casas**, **actividadtarifas**, **resumen plazas** desde JS actividades.

## Tipos De Actividad

- Mantenimiento catalogo `tipo_activ` — metadata, formularios modificar.
- Afecta permisos en asistentes, cargos, SACD, procesos.

## Modulos Dependientes (documentados aparte)

actividadplazas, actividadcargos, asistentes, actividadtarifas, actividadessacd, actividadescentro, procesos, planning, pasarela, notas, casas…

Legacy: mapas `documentacion/Documentacion_Obix/actividades/mapa_*.md`

## Notas De Revision (tanda 1 ficha actividad, jun 2026)

Hallazgos de la revision profunda de `actividad_*` (no se ha tocado codigo):

- **Regresion latente**: `_actividad_form_body.html.twig` usa la clase legacy
  `actividades\model\value_objects\StatusId` (ya inexistente) en la rama que se
  renderiza cuando la app `procesos` NO esta instalada; en ese escenario la
  ficha fallaria. Con `procesos` instalada (caso habitual) no afecta.
- **Entrada muerta**: `tipo_horario` se lee en el endpoint `actividad_nuevo`
  pero no se guarda (el legacy original si lo guardaba en editar/cambiar_tipo);
  el formulario actual no tiene ese campo.
- **Endpoint sin consumidor**: `/src/actividades/actividad_fase_completada_datos`
  no se llama desde ningun sitio (API de paridad documentada en `agents.md`).
- **Permisos**: editar, cambiar tipo, publicar, importar y duplicar no
  re-validan permisos en servidor; el control esta en la UI (botones segun
  `PermisosActividades`). Crear y eliminar si validan (con `procesos`).
