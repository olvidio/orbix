TRUNCATE TABLE "public".aux_metamenus RESTART IDENTITY;
COPY "public".aux_metamenus FROM '/var/www/orbix/log/menus/comun.sql';
TRUNCATE TABLE "public".ref_grupmenu RESTART IDENTITY;
COPY "public".ref_grupmenu FROM '/var/www/orbix/log/menus/refgrupmenu.sql';
TRUNCATE TABLE "public".ref_grupmenu_rol RESTART IDENTITY;
COPY "public".ref_grupmenu_rol FROM '/var/www/orbix/log/menus/refgrupmenu_rol.sql';
TRUNCATE TABLE "public".ref_menus RESTART IDENTITY;
COPY "public".ref_menus FROM '/var/www/orbix/log/menus/refmenus.sql';
