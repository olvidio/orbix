---
tipo: manual_usuario
modulo: actividadessacd
flujos: 14
estado_revision: revisado_parcial
---

# Manual De Usuario - actividadessacd

Asignacion de **SACD encargados** de actividades y **comunicacion** a SACD.

## Acceso Por Menu (rol 8 Exterior, 9, 20)

| Texto en menu | Controller | Parametros tipicos |
|---------------|------------|-------------------|
| **Asignar sacd** / **Atencion actividades** | `activ_sacd.php` | `tipo=sg`, `sr`, `na`, `sf_*`, `sssc`, `falta_sacd`, `solape` |
| **Comunic. sacd** / **Lista activ. sacd** | `com_sacd_activ_periodo.php` | `propuesta=true` en lista |
| **Asignar sacd auto** | `asignar_sacd_auto.php` | Asignacion automatica |

Variantes **Activ sv/sf …** en menu son la misma pantalla `activ_sacd.php` con distinto `tipo` y `periodo`.

## Atencion SACD (Asignar Encargados)

### Para Que Sirve

Listar actividades en un periodo y **asignar, reordenar o quitar** SACD encargados (prioridad, ciudad/centro).

### Tareas Habituales

#### Consultar actividades y SACD

1. Elegir entrada de menu segun tipo (sg, sr, n y agd, sf…, **falta sacd**, **solapes**).
2. Ajustar periodo si la pantalla lo permite.
3. Revisar tabla actividad → SACD asignados.

#### Asignar SACD nuevo

1. Elegir SACD en desplegable lateral.
2. Confirmar — crea cargo y, en actividades SV, puede crear asistencia.

#### Cambiar prioridad o quitar

1. Usar **+ prioridad** / **- prioridad** (intercambia orden entre SACD).
2. **Quitar** — elimina cargo y asistencia asociada.

### Errores Frecuentes

- Solapes: tipo `solape` lista incompatibilidades — resolver manualmente en actividades afectadas.
- SACD ya asignado — mensaje al duplicar.

## Comunicacion Actividades A SACD

### Para Que Sirve

Preparar y enviar **comunicaciones** sobre actividades del periodo a los SACD (textos editables en `com_sacd_txt`).

### Tareas Habituales

1. **Comunic. sacd** o **Lista activ. sacd** (propuesta).
2. Revisar listado de actividades/atencion.
3. Editar textos si procede.
4. Enviar mails a SACD (`comunicacion_activ_sacd_enviar`).

## Asignacion Automatica

1. Menu **Asignar sacd auto** (si visible).
2. Confirmar criterios en formulario.
3. Revisar resultado en **Atencion SACD**.

## Modulos Relacionados

- **actividades** — entidad actividad, tipos, periodos
- **actividadcargos** — filas `ActividadCargo` para SACD encargado
- **asistentes** — asistencia en actividades SV
- **personas** — SACD como personas
- **zonassacd** — zonas geograficas SACD

## Revision Pendiente

- Matriz completa menu `tipo` × `periodo`.
- Permisos escritura SACD vs consulta.
