<style>
@media print {
    @page {
        /* size: A4 portait; */
        sheet-size: A4;
        margin-top: 1.5cm;
        margin-bottom: 1.5cm;
        margin-left: 1.5cm;
        margin-right: 1.5cm;
    }

    .no_print {
        visibility: hidden;
    }

    table {
        autosize: 1;
        border-style: hidden;
        page-break-inside: auto;
    }

    th {
        border-style: none;
        font-size: 14pt;
    }

    td {
        page-break-before: auto;
        border-style: none;
    }

    table.lista {
        autosize: 1;
        border-style: none;
        page-break-inside: auto;
    }

    th.lista {
        border-style: none;
    }

    td.lista {
        page-break-before: auto;
        border-style: none;
        border-bottom-style: solid;
        border-color: gray;
        border-width: 0.5mm;
    }

    div.seccion {
        page-break-inside: auto;
        counter-reset: dan -3; /*al contar tbodys, debo descontar los primeros de las tablas anidadas */
        page-break-after: always;
        font-size: 10pt;
    }

    div.pie {
        margin-top: 0.5cm;
        font-weight: normal;
        text-align: left;
    }

    div.pageFooter {
        margin-top: 0.5cm;
    }

    div.pageFooter:after {
        content: counter(dan);
    }

    tbody {
        counter-increment: dan;
        autosize: 1;
        border-style: none;
        page-break-inside: auto;
    }
}


@media screen {
    table {
        border: 1px solid black;
        page-break-inside: auto;
        autosize: 1;
    }

    table {
        autosize: 1;
        page-break-inside: auto;
        empty-cells: show;
        width: 100%;
        border-collapse: collapse;
        font-size: 10pt;
    }

    th {
        font-weight: bold;
        text-align: center;
        font-size: 12pt;
    }

    td {
        border: none;
        font-size: 10pt;
        page-break-before: auto;
    }

    td.check {
        text-align: right;
        height: 5px;
        width: 3%;
    }

    td.id {
        text-align: right;
        width: 5%;
    }

}

</style>