TRUNCATE TABLE "public".aux_metamenus RESTART IDENTITY;
COPY "public".aux_metamenus FROM '/home/dani/orbix_local/orbix/log/menus/comun.sql';
TRUNCATE TABLE "public".ref_grupmenu RESTART IDENTITY;
COPY "public".ref_grupmenu FROM '/home/dani/orbix_local/orbix/log/menus/refgrupmenu.sql';
TRUNCATE TABLE "public".ref_grupmenu_rol RESTART IDENTITY;
COPY "public".ref_grupmenu_rol FROM '/home/dani/orbix_local/orbix/log/menus/refgrupmenu_rol.sql';
TRUNCATE TABLE "public".ref_menus RESTART IDENTITY;
COPY "public".ref_menus FROM '/home/dani/orbix_local/orbix/log/menus/refmenus.sql';
