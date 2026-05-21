---
tipo: manual_usuario
modulo: cartaspresentacion
flujos: 6
estado_revision: revisado_parcial
---

# Manual De Usuario - cartaspresentacion

Cartas de presentacion de centros/labor Orbix. Menu rol **4** (Exterior).

## Acceso Por Menu

| Texto en menu | Controller | Uso |
|---------------|------------|-----|
| **Modificar** | `cartas_presentacion.php` | Alta/edicion/baja de una carta |
| **Lista todo** | `cartas_presentacion_lista.php?que=lista_todo` | Todas las delegaciones |
| **Lista dl** | `cartas_presentacion_lista.php?que=lista_dl` | Solo delegacion actual |
| **Buscar** | `cartas_presentacion_buscar.php` | Filtros region/pais/dl/poblacion |

## Modificar Carta De Presentacion

### Para Que Sirve

Crear, editar o eliminar una **carta de presentacion** (texto por tipo de labor, delegacion, region, etc.).

### Tareas Habituales

1. Abrir **Modificar** desde menu.
2. Elegir carta existente o crear nueva.
3. Completar campos del formulario (region, pais, delegacion, tipo labor, texto…).
4. **Guardar** o **Eliminar** (solo cartas de la propia dl o con permiso `cr`).

### Errores Frecuentes

| Mensaje | Accion |
|---------|--------|
| Operacion no autorizada | Permiso dl/`cr`; recargar formulario |
| Errores de validacion en guardado | Revisar campos obligatorios en alert |

### Referencias

- Flujo: `cartaspresentacion.carta_presentacion.gestionar.flujo`
- Legacy: `documentacion/Documentacion_Obix/cartaspresentacion/mapa_cartas_presentacion_que.md`

## Consultar Listados

### Lista por delegacion o global

1. **Lista dl** — cartas de mi delegacion, agrupadas por tipo de labor.
2. **Lista todo** — todas las delegaciones (requiere permiso acorde).

### Buscar cartas

1. Menu **Buscar**.
2. Filtrar por poblacion, pais, region y/o delegacion.
3. Pulsar buscar; resultados en `#resultados`.

## Revision Pendiente

- Confirmar permiso `cr` y nombres visibles por rol.
- Enlazar flujos auxiliares `poblaciones`, `ubis` si se usan desde el formulario.
