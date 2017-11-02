$(document).ready(function () {

    //Init datatable
    if($('#redirect-datatable').length >0) {
        var $dt = $('#redirect-datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "/adminpanel/redirect",
            "columns": [
                {"data": "id"},
                {"data": "oldurl"},
                {"data": "newurl"},
                {"data": "coderedirect"},
                {"data": "action"},
            ]
        });
    }

    // Render Create form
    $('#create_redirect').on('click', function () {
        drawForm('/adminpanel/redirect/create');
    });

    // Render Edit form
    $('#redirect-datatable').on('click', '.edit', function ($event) {
        var redirectId = $($event.target).data('redirect-id');
        drawForm('/adminpanel/redirect/' + redirectId + '/edit');
    });

    // Store new record
    $(document).on('click', '#save-redirect', function ($event) {
        var data = $('#form-create-redirect').serialize();
        var createButton = $($event.target);
        persist('/adminpanel/redirect', data, createButton);
    });

    // Update new record
    $('#modal-window').on('click', '#update-redirect', function ($event) {
        var $form = $('#form-edit-redirect');
        var updateButton = $($event.target);
        persist('/adminpanel/redirect/' + $form.data('redirect-id'), $form.serialize(), updateButton);
    });

    // Delete record
    $('#redirect-datatable').on('click', '.delete', function ($event) {
        var deleteButton = $($event.target);
        var redirectId = deleteButton.data('redirect-id');
        var _token = $("meta[name='_token']").attr("content");
        swal({
                title: deleteButton.data('trans-question'),
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            },
            function () {
                var data = {'_token': _token, '_method': 'DELETE'};

                $.post('/adminpanel/redirect/' + redirectId, data, function (response) {
                    $dt.draw();
                    swal(deleteButton.data('trans-success'));
                }).fail(function (error) {
                    swal(error.responseText);
                });
            }
        );
    });

    // Form persist
    function persist(url, data, button) {
        $.post(url, data, function (response) {
            $('#modal-window').modal('toggle');
            $dt.draw();
        })
        .success(function(data) {
            swal(button.data('trans-success'));
        })
        .fail(function (error) {
            if (error.status === 422) {
                var printErrors = makeErrorMessage(error.responseJSON, 1);
                $("#error").removeClass("hide").html(printErrors);
            } else {
                swal(error.responseText);
            }
        });
    }

    // Modal form AJAX render
    function drawForm(url) {
        $.get(url, function (response) {
            $('#modal-window').html(response);
            $('#modal-window').modal('toggle');
        }).fail(function (error) {
            swal(error.responseText);
        });
    }

    // TODO: move to common admin .js
    // Error message format
    function makeErrorMessage(msg, flag) {
        var errorText = '';

        $.each(msg, function (key, value) {
            if (Array.isArray(value)) {
                for (error in value) {
                    if (flag === 1) {
                        errorText += '<p>' + value[error] + '</p>';
                    } else {
                        errorText += value[error] + '\n';
                    }
                }
            }
        });

        return errorText;
    }
});