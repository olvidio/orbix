-- aux_metamenus: pantallas devel_db_admin en frontend y limpieza de URLs apps
-- del módulo devel (comun, datos).
-- Idempotente: reutiliza la fila apps/devel (o apps/devel_db_admin) si la URL
-- frontend aún no existe, para conservar id_metamenu que ya usan aux_menus.

DO $$
DECLARE
    v_id_mod int;
    r record;
BEGIN
    SELECT id_mod INTO v_id_mod FROM public.m0_modulos WHERE nom = 'devel' LIMIT 1;
    v_id_mod := COALESCE(v_id_mod, 2);

    FOR r IN
        SELECT *
        FROM (VALUES
            ('frontend/devel_db_admin/controller/apptables.php', 'apps/devel/controller/apptables.php', 'Manage tablas de Apps'),
            ('frontend/devel_db_admin/controller/db_absorber_esquema_que.php', 'apps/devel/controller/db_absorber_esquema_que.php', 'DB absorber esquema'),
            ('frontend/devel_db_admin/controller/db_cambiar_nombre_que.php', 'apps/devel/controller/db_cambiar_nombre_que.php', 'DB cambiar nombre dl'),
            ('frontend/devel_db_admin/controller/db_crear_esquema_que.php', 'apps/devel/controller/db_crear_esquema_que.php', 'DB crear esquema'),
            ('frontend/devel_db_admin/controller/db_eliminar_esquema_que.php', 'apps/devel/controller/db_eliminar_esquema_que.php', 'DB eliminar esquema'),
            ('frontend/devel_db_admin/controller/db_mover_que.php', 'apps/devel/controller/db_mover_que.php', 'DB mover tabla')
        ) AS t(url_nueva, url_antigua, descripcion)
    LOOP
        UPDATE public.aux_metamenus
        SET url = r.url_nueva,
            descripcion = r.descripcion,
            id_mod = v_id_mod
        WHERE url IN (
                r.url_antigua,
                replace(r.url_antigua, 'apps/devel/', 'apps/devel_db_admin/')
            )
          AND NOT EXISTS (
                SELECT 1 FROM public.aux_metamenus x WHERE x.url = r.url_nueva
            );

        UPDATE public.aux_metamenus
        SET descripcion = r.descripcion,
            id_mod = v_id_mod
        WHERE url = r.url_nueva;

        INSERT INTO public.aux_metamenus (id_mod, url, descripcion)
        SELECT v_id_mod, r.url_nueva, r.descripcion
        WHERE NOT EXISTS (
            SELECT 1 FROM public.aux_metamenus x WHERE x.url = r.url_nueva
        );
    END LOOP;

    DELETE FROM public.aux_metamenus
    WHERE id_mod = v_id_mod
      AND url LIKE 'apps%';
END $$;
