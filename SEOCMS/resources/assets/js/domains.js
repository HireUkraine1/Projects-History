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