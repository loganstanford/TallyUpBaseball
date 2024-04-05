<?php
session_start();
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Florida Rotisserie Fantasy Baseball</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.2.2/css/fixedColumns.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.3.2/css/fixedHeader.dataTables.min.css">
    <!-- DatePicker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">

    <style>
    .body {
        font-size: 12px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
        color: #2a2a2a;
    }

    .dataTable>thead>tr>th[class*="sort"]:after {
        content: '' !important;
    }

    .dataTable>thead>tr>th[class*="sort"]:before {
        content: '' !important;
    }

    table.dataTable thead tr>.dtfc-fixed-left {
        background-color: #343a40 !important;
    }

    table.dataTable tbody tr>.dtfc-fixed-left {
        background-color: #343a40 !important;
    }

    table.fixedHeader-floating {
        background-color: #343a40 !important;
    }

    /* td:before {
        content: none !important;
    } */

    table.dataTable thead>tr>th.sorting_asc,
    table.dataTable thead>tr>th.sorting_desc,
    table.dataTable thead>tr>th.sorting,
    table.dataTable thead>tr>td.sorting_asc,
    table.dataTable thead>tr>td.sorting_desc,
    table.dataTable thead>tr>td.sorting {
        padding: 2px !important;
    }

    table.dataTable {
        font-size: 14px !important;
    }

    table.dataTable tr>td {
        padding: 3px !important;
    }

    .box-stats {
        text-align: center;
        vertical-align: middle !important;
    }

    /* .position { 
        font-size: .8em;
    }
    */
    table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
        margin-left: -24px;
    }

    .position thead,
    tr,
    td {
        padding: 2px;
    }

    .player-bio {
        font-size: 12px;


    }

    .bio-team {}

    .bio-pos,
    .total {
        font-weight: 600;
    }

    .box-stats,
    .player-name {
        font-size: 14px !important;
    }

    .weather-icon {
        width: 50px;
    }

    .weather,
    .starter {
        font-size: 12px;
    }


    .lineup_status.in-lineup {
        background-color: #d4f7e6;
        border-bottom: 1px solid #a0eec7
    }

    .lineup_status.not-in-lineup {
        background-color: red;
        opacity: 0.5;
        border-bottom: 1px solid #a0eec7
    }

    a {
        color: white;
    }

    .daterangepicker .ranges li.active {
        background-color: #3f6791 !important;
    }

    .container-fluid {
        max-width: 1200px;
    }


    </style>
</head>