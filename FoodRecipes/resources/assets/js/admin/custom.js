function getQueryParams(qs) {
    qs = qs.split('+').join(' ');

    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }

    return params;
}
jQuery(document).ready(function($) {  
       
    $('body').on('click', '.delete-item', function (e) {
        var href = this.href;
        href = href.substring(0, href.length - 1)+'/destroy/'+$(this).data('id');
        deleteSet(href, 'User');
        e.preventDefault();
    });	
    
    $('body').on('click', '.delete-role', function (e) {
        var href = this.href;
        href = href.substring(0, href.length - 1)+'/destroy/'+$(this).data('id');
        deleteSet(href, 'Role');
        e.preventDefault();
    });	
  
    $('body').on('click', '.delete-permission', function (e) {
        var href = this.href;
        href = href.substring(0, href.length - 1)+'/destroy/'+$(this).data('id');
        deleteSet(href, 'Permission');
        e.preventDefault();
    });	
    
    $('body').on('click', '.delete-setting', function (e) {
        var href = this.href;
        href = href.substring(0, href.length - 1)+'/destroy/'+$(this).data('id');
        deleteSet(href, 'Setting');
        e.preventDefault();
    });	   
    
    function deleteSet(href, set){
        
        $.SmartMessageBox({
                title : "Delete "+set,
                content : "Do you really want to delete "+set+"?",
                buttons : '[No][Yes]'
        }, function(ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    location.href = href;
                }
                if (ButtonPressed === "No") {      
                        $.smallBox({
                                title : "Сancel",
                                content : "<i class='fa fa-clock-o'></i> <i>"+set+" wasn't deleted</i>",
                                color : "#659265",
                                iconSmall : "fa fa-times fa-2x fadeInRight animated",
                                timeout : 4000
                        });                   
                }
        });
    }
}); 