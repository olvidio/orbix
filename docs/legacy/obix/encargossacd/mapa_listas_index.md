```mermaid
flowchart TD
    %% Estils de nodes
    classDef controller fill:#f9f,stroke:#333,stroke-width:2px;
    classDef vista fill:#bbf,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;
    classDef error fill:#ff9999,stroke:#cc0000,stroke-width:2px;

    apps_encargossacd_controller_listas_index_php(["listas_index.php"]):::controller --> apps_encargossacd_view_listas_index_html_twig[["listas_index.html.twig"]]:::vista
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_cl_php(["listas_cl.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_a_php(["listas_a.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_b_php(["listas_b.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_c_php(["listas_c.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_d_php(["listas_d.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_exigencia_ctr_php(["listas_exigencia_ctr.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_com_sacd_php(["listas_com_sacd.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_com_ctr_php(["listas_com_ctr.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_listas_com_txt_php(["listas_com_txt.php"]):::controller
    apps_encargossacd_view_listas_index_html_twig --> apps_encargossacd_controller_comprobaciones_php(["comprobaciones.php"]):::controller
    apps_encargossacd_controller_listas_a_php(["listas_a.php"]):::controller --> apps_encargossacd_view_listas_html_twig[["listas.html.twig"]]:::vista
    apps_encargossacd_controller_listas_b_php(["listas_b.php"]):::controller --> apps_encargossacd_view_listas_html_twig[["listas.html.twig"]]:::vista
    apps_encargossacd_controller_listas_c_php(["listas_c.php"]):::controller --> apps_encargossacd_view_listas_html_twig[["listas.html.twig"]]:::vista
    apps_encargossacd_controller_listas_exigencia_ctr_php(["listas_exigencia_ctr.php"]):::controller --> apps_encargossacd_view_listas_html_twig[["listas.html.twig"]]:::vista
    apps_encargossacd_controller_listas_com_sacd_php(["listas_com_sacd.php"]):::controller --> apps_encargossacd_view_listas_com_sacd_phtml[["listas_com_sacd.phtml"]]:::vista
    apps_encargossacd_controller_listas_com_ctr_php(["listas_com_ctr.php"]):::controller --> apps_encargossacd_view_listas_com_ctr_phtml[["listas_com_ctr.phtml"]]:::vista
    apps_encargossacd_controller_listas_com_txt_php(["listas_com_txt.php"]):::controller --> apps_encargossacd_view_listas_com_txt_html_twig[["listas_com_txt.html.twig"]]:::vista
    apps_encargossacd_view_listas_com_txt_html_twig --> apps_encargossacd_controller_listas_com_txt_ajax_php(["listas_com_txt_ajax.php"]):::controller
```