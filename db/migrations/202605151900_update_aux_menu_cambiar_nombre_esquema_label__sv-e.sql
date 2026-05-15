-- Renombrar texto de menu (grupo sistema / devel DB): alinear con la pantalla de cambio de nombre de esquema.
UPDATE *.aux_menus AS m
SET menu = 'cambiar nombre esquema'
WHERE m.id_grupmenu = 13
    AND m.menu = 'mover y cambiar nombre dl';
