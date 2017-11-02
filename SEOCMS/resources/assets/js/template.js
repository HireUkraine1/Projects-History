$(document).ready(function () {

    //Init datatable
    if($('#template-datatable').length >0){
        var $dt = $('#template-datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "/adminpanel/template",
            "columns": [
                {"data": "id"},
                {"data": "name"},
                {"data": "virtualroot"},
                {"data": "action"},
            ]
        });
    }

    // Render Create form
    $('#create_template').on('click', function () {
        drawForm('/adminpanel/template/create');
    });

    // Store new record
    $(document).on('click', '#save-template', function ($event) {

        $("textarea#t-body").val( editAreaLoader.getValue('t-body'));
        var data = $('#form-create-template').serialize();
        var createButton = $($event.target);

        persist('/adminpanel/template', data, createButton);
    });

    // Render Edit form
    $('#template-datatable').on('click', '.edit', function ($event) {
        var templateId = $($event.target).data('template-id');
        drawForm('/adminpanel/template/' + templateId + '/edit', templateId);

    });

    // Update new record
    $('#modal-window').on('click', '#update-template', function ($event) {
        $("textarea#t-body").val( editAreaLoader.getValue('t-body'));
        var $form = $('#form-edit-template');
        var updateButton = $($event.target);
        persist('/adminpanel/template/' + $form.data('template-id'), $form.serialize(), updateButton);
    });

    // Delete record
    $('#template-datatable').on('click', '.delete', function ($event) {
        var deleteButton = $($event.target);
        var templateId = deleteButton.data('template-id');
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
                $.post('/adminpanel/template/' + templateId, data, function (response) {
                    $dt.draw();
                    swal(deleteButton.data('trans-success'));
                }).fail(function (error) {
                    swal(error.responseText);
                });
            }
        );
    });

    //Modal window actions
    $('#admin-template-page #modal-window').on('hidden.bs.modal', function (e) {
        editAreaLoader.delete_instance("t-body");
    })
    $('#admin-template-page #modal-window').on('show.bs.modal', function (e) {
        setTimeout(function () {
            editAreaLoader.init({
                id: "t-body"
                ,start_highlight: true
                ,allow_resize: "no"
                ,allow_toggle: true
                ,language: "ru"
                ,syntax: "html"
                ,show_line_colors: true
            });
        }, 500);
    })



    // Modal form AJAX render
    function drawForm(url) {
        $.get(url, function (response) {
            $('#modal-window').html(response);
            $('#modal-window').modal('toggle');
        })
        .fail(function (error) {
            swal(error.responseText);
        });
    }

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