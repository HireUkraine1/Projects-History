$(document).ready(function () {
    $.fn.modal.Constructor.prototype.enforceFocus = function () {
    };

    var createFormNewTemplate = false;
    var editFormNewTemplate = false;

    //Init datatable
    if ($('#page-datatable').length > 0) {

        $(document).on('click', '#template-create', function () {
            templateCreationShow();
        });

        $(document).on('click', '#template-cancel', function () {
            templateCreationHide();
        });

        function templateCreationShow() {
            if ($('#form-create-page').length > 0) {
                createFormNewTemplate = true;
            } else {
                editFormNewTemplate = true;
            }

            $('#template-create-block').show();
            $('#template-chose').hide();
            $('#template-create').hide();

            editAreaLoader.init({
                id: "t-body"
                , start_highlight: true
                , allow_resize: "no"
                , allow_toggle: true
                , language: "ru"
                , syntax: "html"
                , show_line_colors: true
            });
        }

        function templateCreationHide() {
            if ($('#form-create-page').length > 0) {
                createFormNewTemplate = false;
            } else {
                editFormNewTemplate = false;
            }

            $('#template-create-block').hide();
            $('#template-chose').show();
            $('#template-create').show();
        }

        function initSelect2() {
            $("#template_id").select2({
                ajax: {
                    url: "/adminpanel/template",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            'draw': params.page ? params.page : 1,
                            'columns[0][data]': 'id',
                            'columns[0][name]': '',
                            'columns[0][searchable]': true,
                            'columns[0][orderable]': true,
                            'columns[0][search][value]': '',
                            'columns[0][search][regex]': false,
                            'columns[1][data]': 'name',
                            'columns[1][name]': '',
                            'columns[1][searchable]': true,
                            'columns[1][orderable]': true,
                            'columns[1][search][value]': '',
                            'columns[1][search][regex]': false,
                            'start': (params.page > 1) ? 10 * (params.page - 1) : 0,
                            'length': 10,
                            'search[value]': params.term,
                            'search[regex]': false,
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page * 10) < data.recordsTotal
                            }
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) {
                    return markup;
                },
                minimumInputLength: 1,
                templateResult: function (repo) {
                    if (repo.loading) return repo.text;
                    return repo.name;
                },
                templateSelection: function (repo) {
                    $('#template-edit').attr('href', "/adminpanel/template/" + repo.id + "/edit").show();
                    return repo.name || repo.text;
                },
                width: '100%'
            });
        }

        var $dt = $('#page-datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "/adminpanel/page",
            "columns": [
                {"data": "id"},
                {"data": "url"},
                {"data": "h1"},
                {"data": "title"},
                {"data": "template"},
                {"data": "action"},
            ]
        });
    }

// Render Create form
    $('#create_page').on('click', function () {
        drawForm('/adminpanel/page/create');
    });

// Render Edit form
    $('#page-datatable').on('click', '.edit', function ($event) {
        var pageId = $($event.target).data('page-id');
        drawForm('/adminpanel/page/' + pageId + '/edit');
    });

// Store new record
    $(document).on('click', '#save-page', function ($event) {
        $("textarea#criticalcss").val(editAreaLoader.getValue('criticalcss'));
        $("textarea#t-body").val(editAreaLoader.getValue('t-body'));
        var $form = $('#form-create-page');

        if (createFormNewTemplate) {
            return createTemplate('/adminpanel/page', $form, createFormNewTemplate, $event);
        }

        var formData = getPageFormData($form);

        formData.create_template = createFormNewTemplate;

        return persist('/adminpanel/page', formData, $event);
    });

// Update record
    $('#modal-window').on('click', '#update-page', function ($event) {
        $("textarea#criticalcss").val(editAreaLoader.getValue('criticalcss'));
        $("textarea#t-body").val(editAreaLoader.getValue('t-body'));

        var $form = $('#form-edit-page');
        var url = '/adminpanel/page/' + $form.data('page-id');

        if (editFormNewTemplate) {
            return createTemplate(url, $form, editFormNewTemplate, $event);
        }

        var formData = getPageFormData($form);

        formData.create_template = editFormNewTemplate;

        return persist(url, formData, $event);
    });

    function createTemplate(url, $form, createTemplate, $event) {
        var templateData = {};

        templateData._token = $('#template-create-block').find('[name="_token"]').val();
        templateData.name = $('#template-create-block').find('[name="name"]').val();
        templateData.virtualroot = $('#template-create-block').find('[name="virtualroot"]').val();
        templateData.body = $('#template-create-block').find('[name="body"]').val();

        $.post('/adminpanel/template', templateData).success(function (data) {
            data = JSON.parse(data);
            $.get('/adminpanel/template/' + data.id, function (response) {
                templateCreationHide();
                var newOption = new Option(response.name, response.id, true, true);
                $('#template_id').append(newOption).trigger('change');

                var formData = getPageFormData($form);

                formData.create_template = createTemplate;

                persist(url, formData, $event);
            });
        }).fail(function (error) {
            if (error.status === 422) {
                var printErrors = makeErrorMessage(error.responseJSON, 1);
                $("#error").removeClass("hide").html(printErrors);
            } else {
                swal(error.responseText);
            }
        });
    }

    function getPageFormData($form) {
        var formData = {};

        if (method = $form.find('[name="_method"]').val()) {
            formData._method = method;
        }

        formData._token = $form.find('[name="_token"]').val();
        formData.url = $form.find('[name="url"]').val();
        formData.h1 = $form.find('[name="h1"]').val();
        formData.title = $form.find('[name="title"]').val();
        formData.description = $form.find('[name="description"]').val();
        formData.keywords = $form.find('[name="keywords"]').val();
        formData.template_id = $form.find('[name="template_id"]').val();
        formData.sitemappriority = $form.find('[name="sitemappriority"]').val();
        formData.criticalcss = $form.find('[name="criticalcss"]').val();
        formData.active = $form.find('[name="active"]').is(':checked');

        return formData;
    }

// Delete record
    $('#page-datatable').on('click', '.delete', function ($event) {
        var deleteButton = $($event.target);
        var pageId = deleteButton.data('page-id');
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

                $.post('/adminpanel/page/' + pageId, data, function (response) {
                    $dt.draw();
                    swal(deleteButton.data('trans-success'));
                }).fail(function (error) {
                    swal(error.responseText);
                });
            }
        );
    });

// Form persist
    function persist(url, data, $event) {
        $.post(url, data, function (response) {
            $('#modal-window').modal('toggle');
            $dt.draw();
        })
            .success(function (data) {
                swal($($event.target).data('trans-success'));
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

//Modal window actions
    $('#admin-pagecreation #modal-window').on('hidden.bs.modal', function (e) {
        editAreaLoader.delete_instance("criticalcss");
    })

    $('#admin-pagecreation #modal-window').on('show.bs.modal', function (e) {
        setTimeout(function () {
            editAreaLoader.init({
                id: "criticalcss"
                , start_highlight: true
                , allow_resize: "no"
                , allow_toggle: true
                , language: "ru"
                , syntax: "css"
                , show_line_colors: true
            });
        }, 500);
    })


// Modal form AJAX render
    function drawForm(url) {
        $.get(url, function (response) {
            $('#modal-window').html(response);
            $('#modal-window').modal('toggle');
            initSelect2();
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
})
;