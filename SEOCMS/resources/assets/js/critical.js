$(document).ready(function () {

    //Init datatable
    if ($('#critical-datatable').length > 0) {
        var $dt = $('#critical-datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "/adminpanel/critical-css",
            "columns": [
                {"data": "id"},
                {"data": "created_at"},
                {"data": "page.url"},
                {"data": "status"},
            ],
            "order": [[ 1, "desc" ]]
        });

        $('#critical-compile-update').click(function(){
            $dt.draw();
        });

        $('#critical-generate').click(function (event) {
            var resoultions = $('#resolutions').val();
            var routes = $('#routes').val();
            var process = $('#process_all').is(':checked');

            var data = {
                'resolutions': resoultions,
                'routes': routes,
                'process': process,
            };

            $.get('/adminpanel/critical-css/generate', data, function (response) {});

            swal($(event.target).data('success'));
        });
    }
});