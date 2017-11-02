/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */
(function($,sr){
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
      var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap)
                    func.apply(obj, args); 
                timeout = null; 
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100); 
        };
    };

    // smartresize 
    jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');
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
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var CURRENT_URL = window.location.href.split('#')[0].split('?')[0],
    $BODY = $('body'),
    $MENU_TOGGLE = $('#menu_toggle'),
    $SIDEBAR_MENU = $('#sidebar-menu'),
    $SIDEBAR_FOOTER = $('.sidebar-footer'),
    $LEFT_COL = $('.left_col'),
    $RIGHT_COL = $('.right_col'),
    $NAV_MENU = $('.nav_menu'),
    $FOOTER = $('footer');

// Sidebar
$(document).ready(function() {
    // TODO: This is some kind of easy fix, maybe we can improve this
    var setContentHeight = function () {
        // reset height
        $RIGHT_COL.css('min-height', $(window).height());

        var bodyHeight = $BODY.outerHeight(),
            footerHeight = $BODY.hasClass('footer_fixed') ? -10 : $FOOTER.height(),
            leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= $NAV_MENU.height() + footerHeight;

        $RIGHT_COL.css('min-height', contentHeight);
    };

    $SIDEBAR_MENU.find('a').on('click', function(ev) {
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active active-sm');
            $('ul:first', $li).slideUp(function() {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!$li.parent().is('.child_menu')) {
                $SIDEBAR_MENU.find('li').removeClass('active active-sm');
                $SIDEBAR_MENU.find('li ul').slideUp();
            }
            
            $li.addClass('active');

            $('ul:first', $li).slideDown(function() {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    $MENU_TOGGLE.on('click', function() {
        if ($BODY.hasClass('nav-md')) {
            $SIDEBAR_MENU.find('li.active ul').hide();
            $SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
        } else {
            $SIDEBAR_MENU.find('li.active-sm ul').show();
            $SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
        }

        $BODY.toggleClass('nav-md nav-sm');

        setContentHeight();

        $('.dataTable').each ( function () { $(this).dataTable().fnDraw(); });
    });

    // check active menu
    $SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');

    $SIDEBAR_MENU.find('a').filter(function () {
        return this.href == CURRENT_URL;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function() {
        setContentHeight();
    }).parent().addClass('active');

    // recompute content when resizing
    $(window).smartresize(function(){  
        setContentHeight();
    });

    setContentHeight();

    // fixed sidebar
    if ($.fn.mCustomScrollbar) {
        $('.menu_fixed').mCustomScrollbar({
            autoHideScrollbar: true,
            theme: 'minimal',
            mouseWheel:{ preventDefault: true }
        });
    }
});
// /Sidebar

// Panel toolbox
$(document).ready(function() {
    $('.collapse-link').on('click', function() {
        var $BOX_PANEL = $(this).closest('.x_panel'),
            $ICON = $(this).find('i'),
            $BOX_CONTENT = $BOX_PANEL.find('.x_content');
        
        // fix for some div with hardcoded fix class
        if ($BOX_PANEL.attr('style')) {
            $BOX_CONTENT.slideToggle(200, function(){
                $BOX_PANEL.removeAttr('style');
            });
        } else {
            $BOX_CONTENT.slideToggle(200); 
            $BOX_PANEL.css('height', 'auto');  
        }

        $ICON.toggleClass('fa-chevron-up fa-chevron-down');
    });

    $('.close-link').click(function () {
        var $BOX_PANEL = $(this).closest('.x_panel');

        $BOX_PANEL.remove();
    });
});
// /Panel toolbox

// Tooltip
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });
});
// /Tooltip

// Progressbar
$(document).ready(function() {
	if ($(".progress .progress-bar")[0]) {
	    $('.progress .progress-bar').progressbar();
	}
});
// /Progressbar

// Switchery
$(document).ready(function() {
    if ($(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html, {
                color: '#26B99A'
            });
        });
    }
});
// /Switchery

// iCheck
$(document).ready(function() {
    if ($("input.flat")[0]) {
        $(document).ready(function () {
            $('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
});
// /iCheck

// Table
$('table input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('table input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});

var checkState = '';

$('.bulk_action input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
    checkState = 'all';
    countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
    checkState = 'none';
    countChecked();
});

function countChecked() {
    if (checkState === 'all') {
        $(".bulk_action input[name='table_records']").iCheck('check');
    }
    if (checkState === 'none') {
        $(".bulk_action input[name='table_records']").iCheck('uncheck');
    }

    var checkCount = $(".bulk_action input[name='table_records']:checked").length;

    if (checkCount) {
        $('.column-title').hide();
        $('.bulk-actions').show();
        $('.action-cnt').html(checkCount + ' Records Selected');
    } else {
        $('.column-title').show();
        $('.bulk-actions').hide();
    }
}

// Accordion
$(document).ready(function() {
    $(".expand").on("click", function () {
        $(this).next().slideToggle(200);
        $expand = $(this).find(">:first-child");

        if ($expand.text() == "+") {
            $expand.text("-");
        } else {
            $expand.text("+");
        }
    });
});

// NProgress
if (typeof NProgress != 'undefined') {
    $(document).ready(function () {
        NProgress.start();
    });

    $(window).on('load', function() {
        NProgress.done();
    });
}

$(document).ready(function () {

    //Make errors
    function makeErrorMassage (msg, flag) {
        var errorText = '';
        $.each( msg, function( key, value ) {
            if(Array.isArray( value)){
                for(error in value){
                    if(flag === 1 ){
                        errorText += '<p>' + value[error] + '</p>';
                    } else {
                        errorText += value[error] + '\n';
                    }
                }
            }
        });
        return errorText;
    }

    //Realize textaria autosize
    function textariaAutosize(that){
        while ($(that).outerHeight() < that.scrollHeight + parseFloat($(that).css("borderTopWidth")) + parseFloat($(that).css("borderBottomWidth"))) {
            $(that).height($(that).height() + 1);
        }
    }
    $(".textarea").each(function () {
        textariaAutosize(this);
    });
    $(document).on('keyup', '.domain-page .textarea', function(){
        textariaAutosize(this);
    });


    //Get form for creation new domain
    $('.domain-page #create_domain').on('click', function(){
        $.ajax({
            type: 'GET',
            url: '/adminpanel/domain/create',
            success: function (data) {
                $('#modal-window').html(data);
                $('#modal-window').modal('show');
            },
            error: function(data){
                swal(data.responseText);
            }
        });
    });

    //Save new domain
    $(document).on('click', '.domain-page #safe-domain', function($event){
        var createButton = $($event.target);
        var form = $('#form-create-domain').serialize();
        $.ajax({
            type: 'POST',
            url: '/adminpanel/domain',
            data: form,
            dataType: 'json',
            success: function (data) {
                if(data.master == 1) {
                    $('.domain-page input[name="master"]:checked').trigger('click');
                }
                $('#domains').append(data.view);
                var elem = document.querySelector('.domain-page #main-domain'+data.id);
                var init = new Switchery(elem, {
                    color: '#26B99A'
                });
                $('#modal-window').modal('hide');
                $(".textarea").each(function () {
                    textariaAutosize(this);
                });
                swal(createButton.data('trans-success'));
            },
            error: function(data){
                if(data.status == 422) {
                    var errors = data.responseJSON;
                    var printErrors = makeErrorMassage (errors, 1);
                    $("#error").removeClass("hide");
                    $('#error').html(printErrors);
                } else {
                    swal(data.responseText);
                }
            }
        });
    });

    //Domain info update
    $(document).on('click', '.domain-page .update-action', function($event){
        var id = $(this).data('id');
        var updateButton = $($event.target);
        var form = $('.domain-page #domain_' + id).serialize();
        swal({
                title:  updateButton.data('trans-question'),
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            },
            function(){
                $.ajax({
                    type: 'PUT',
                    url: '/adminpanel/domain/' + id,
                    data: form,
                    dataType: 'json',
                    success: function (data) {
                        if(data.master == 1) {
                            $('.domain-page input[name="master"]:checked').not( '.domain-page #main-domain'+id ).trigger('click');
                        }
                        swal(updateButton.data('trans-success'));
                    },
                    error: function(data){
                        if(data.status == 422) {
                            var errors = data.responseJSON;
                            var printErrors = makeErrorMassage (errors);
                            swal(printErrors);
                        } else {
                            swal(data.responseText);
                        }
                    }
                });
            }
        );
    });

    //Delete Domain
    $(document).on('click', '.domain-page .delete-action', function($event){
        var id = $(this).data('id');
        var deleteButton = $($event.target);
        var _token = $('.domain-page #domain_' + id + ' > input[name="_token"]').val();
        swal({
                title: deleteButton.data('trans-question'),
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            },
            function(){
                $.ajax({
                    type: 'DELETE',
                    url: '/adminpanel/domain/' + id,
                    data:{ _token:_token},
                    success: function (data) {
                        $( ".domain-page #domain_"+id ).remove();
                        swal(deleteButton.data('trans-success'));
                    },
                    error: function(data){
                        swal(data.responseText);
                    }
                });
            }
        );
    });
});
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