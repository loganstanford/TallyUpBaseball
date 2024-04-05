$(function() {
        $('.hitters-pos').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "order": [8, 'desc'],
            "info": false,
            "autoWidth": false,
            "responsive": true,
            "columnDefs": [{
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                },
            ]
        });
        $('#hitters-all').removeAttr('width').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "order": [[-1, 'desc'], [2, 'desc']],
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "fixedColumns": true,
            "scrollX": true,
            "columnDefs": [{
                    width: 120,
                    targets: 0
                },
                {
                    width: 300,
                    targets: 1
                }
            ]
        });
        $('#pitching').removeAttr('width').DataTable({
            "paging": false,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "order": [6, 'desc'],
            "info": false,
            "autoWidth": false,
            "responsive": false,
            "fixedColumns": true,
            "scrollX": true,
            "columnDefs": [{
                    width: 200,
                    targets: 0
                },
                {
                    width: 300,
                    targets: 1
                }
            ]
        });
    });