-- Metamenu para pantalla «absorber esquema» (devel_db_admin), tipo controlador (id_mod = 2).
-- Migracion sv-e 202605151844 copia desde "H-dlpv".aux_menus: en la base sv-e hace falta una fila
-- plantilla alli (menu = 'absorber esquema', id_metamenu = este id) antes de replicar a otros esquemas *v.
INSERT INTO public.aux_metamenus (id_mod, url, parametros, descripcion)
SELECT
    2,
    'frontend/devel_db_admin/controller/db_absorber_esquema_que.php',
    NULL,
    'DB absorber esquema'
WHERE NOT EXISTS (
        SELECT 1
        FROM public.aux_metamenus AS m
        WHERE m.url = 'frontend/devel_db_admin/controller/db_absorber_esquema_que.php'
    );
