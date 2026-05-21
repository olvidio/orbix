---
tipo: manual_usuario
modulo: actividadplazas
flujos: 10
estado_revision: revisado_parcial
---

# Manual De Usuario - actividadplazas

Gestion de **plazas** entre delegaciones del grupo de estudios para actividades.

## Acceso Por Menu

Parametros habituales en URL (`sasistentes`, `sactividad`): ver `menus.csv` rol **2**, **3**, **10**, **12**.

| Texto en menu | Controller | Uso |
|---------------|------------|-----|
| **Gestion de plazas** | `gestion_plazas.php` | Cuadro editable plazas totales/concedidas/pedidas |
| **Balance de plazas** | `plazas_balance_que.php` | Comparativa entre delegaciones |
| **Incorporar 1ª peticion** | `incorporar_peticion.php` | Convierte peticiones en asistencia con plaza |

Entrada adicional (sin menu propio):

- **Resumen plazas** — desde ficha **actividad** (`resumen_plazas.php`, JS actividades).
- **Peticiones de una persona** — desde selector **personas** (`peticiones_activ.php`).

## Gestion De Plazas

### Para Que Sirve

Editar en rejilla (**TablaEditable**) las plazas de una actividad: totales, concedidas y pedidas entre dl del grupo.

### Tareas Habituales

1. Abrir **Gestion de plazas** (tipo actividad segun menu: crt, ca, cve, cv, semestre-inv…).
2. Localizar fila de delegacion.
3. Editar celdas inline y guardar cambios en la rejilla.
4. Comprobar totales coherentes.

## Balance De Plazas

1. Elegir delegaciones A y B en filtros.
2. Revisar grid comparativo (concedidas + libres).
3. Exportar o imprimir si hay accion en pantalla.

## Incorporar Primera Peticion

1. Abrir **Incorporar 1ª peticion** con contexto de actividad correcto.
2. Ejecutar accion — convierte primera peticion de numerarios/agregados en asistencia con plaza asignada/pedida cuando la actividad es de otra dl.
3. Verificar en gestion de plazas o listado asistentes.

## Resumen Y Cedencia De Plazas

Desde la **actividad**:

1. Abrir resumen de plazas por delegacion (calendario / cedidas / conseguidas / ocupadas).
2. Usar accion **ceder** plazas a otra dl si procede (`plazas_ceder`).

## Peticiones De Plaza (Persona)

Desde **personas**:

1. Listado editable de actividades que la persona solicita como peticion de plaza.
2. Ajustar con desplegables +/- segun pantalla.

## Errores Frecuentes

- Validacion de plazas negativas o totales incoherentes — mensaje en alert al guardar.
- Endpoint `posibles_propietarios_data` — usado desde formularios **asistentes** (propietario plaza), no pantalla propia.

## Revision Pendiente

- Tabla de variantes `sactividad` / `sasistentes` por rol.
- Permisos de edicion por dl.
