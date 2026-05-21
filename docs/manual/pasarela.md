---
tipo: manual_usuario
modulo: pasarela
flujos: 14
estado_revision: revisado_parcial
---

# Manual De Usuario - pasarela

**Pasarela** de exportacion y parametros para integracion externa (nombres, activaciones, contribuciones).

## Acceso Por Menu (rol 8 Exterior)

| Texto en menu | Controller | Uso |
|---------------|------------|-----|
| **Pasarela** (raiz) | — | Agrupador |
| **Parametros** | `parametros_menu.php` | Menu parametros pasarela |
| **Exportar actividades** | `exportar_que.php` | Exportacion actividades |

Subpantallas (forms): `nombre`, `activacion`, `contribucion_*`, excepciones por tipo actividad — ver flujos en catalogo.

## Configurar Parametros

1. **Parametros** — elegir categoria (nombre, activacion, contribucion no duerme/reserva…).
2. Editar valores por defecto y **excepciones** por tipo actividad.
3. Guardar cada formulario.

## Exportar Actividades

1. **Exportar actividades**.
2. Definir filtros (tipo actividad, periodo…).
3. Ejecutar export — genera salida segun configuracion pasarela.

## Modulos Relacionados

- **actividades** — datos exportados
- **actividadtarifas** — relacion tarifa en forms pasarela (nombre_form)

Legacy: mapas en `documentacion/Documentacion_Obix/pasarela/` si existen.
