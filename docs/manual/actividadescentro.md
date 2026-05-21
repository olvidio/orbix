---
tipo: manual_usuario
modulo: actividadescentro
flujos: 8
estado_revision: revisado_parcial
---

# Manual De Usuario - actividadescentro

Asignacion de **centros encargados** (organizadores) a actividades.

## Acceso Por Menu (rol 8, 10, 15, 20)

Todas las entradas apuntan a `frontend/actividadescentro/controller/activ_ctr.php` con distinto `tipo`:

| Texto en menu | tipo (aprox.) |
|---------------|---------------|
| **Asignar centros** | (default) |
| **Activ sg** | `sg` |
| **Activ sr** | `sr` |
| **Sv n y agd** | `nagd` |
| **Sf s y sg** | `sfsg` |
| **Sf sr** | `sfsr` |
| **Sf n, nax y agd** | `sfnagd` |
| **Sss+** | `sssc` |
| **Actividades-centros** (rol 10) | `sg` |

## Asignar Centros Encargados

### Para Que Sirve

Ver actividades en un periodo y gestionar **centros (ubi) encargados**: asignar, reordenar prioridad o quitar.

### Tareas Habituales

1. Abrir entrada de menu acorde al tipo de actividad.
2. Filtrar periodo si aplica.
3. Revisar listado actividad → centros actuales.
4. **Asignar** centro desde desplegable lateral (tipos de centro segun actividad).
5. **+/- prioridad** para reordenar encargados.
6. **Quitar** centro de la actividad.

### Errores Frecuentes

- Centro no valido para tipo actividad — elegir otro del desplegable.
- Alert **respuesta:** tras AJAX — leer mensaje del servidor.

## Modulos Relacionados

- **actividades** — listado y filtros periodo/tipo
- **ubis** — centros (`id_ubi`)
- **encargossacd** — contexto encargos (dominio relacionado)

## Revision Pendiente

- Dossier 3010 (`Info3010`) si se expone en UI.
- Permisos por tipo actividad y rol.
