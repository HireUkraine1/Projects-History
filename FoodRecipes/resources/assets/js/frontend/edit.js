/*!
 * Editor inline
 *
 * @copyright 2015 MoodleFreak.com
 * @author Luuk Verhoeven
 **/
CKEDITOR.dtd.$editable.span = 1;
CKEDITOR.dtd.$editable.a = 1;
CKEDITOR.dtd.$editable.img = 1;
CKEDITOR.dtd.$editable.li = 1;

CKEDITOR.on('instanceCreated', function (event) {
    var editor  = event.editor,
        element = editor.element;
    log('instanceCreated:' + element.data('trans'));
    // Customize editors for headers and tag list.
    // These editors do not need features like smileys, templates, iframes etc.
    if (element.is('h1', 'h2', 'h3', 'a') || element.getAttribute('id') == 'taglist') {
        // Customize the editor configuration on "configLoaded" event,
        // which is fired after the configuration file loading and
        // execution. This makes it possible to change the
        // configuration before the editor initialization takes place.
        editor.on('configLoaded', function () {
            // Remove redundant plugins to make the editor simpler.
            editor.config.removePlugins = 'colorbutton,find,flash,font,' +
                'forms,iframe,image,newpage,removeformat' +
                'smiley,specialchar,stylescombo,templates';

            // Rearrange the toolbar layout.
            editor.config.toolbarGroups = [
                {
                    name  : 'editing',
                    groups: ['basicstyles', 'links']
                },
                {name: 'undo'},
                {
                    name  : 'clipboard',
                    groups: ['selection', 'clipboard']
                },
            ];
        });
    }
    var wto;
    editor.on('change', function () {

        // delay save request to the server for 3 seconde
        clearTimeout(wto);
        wto = setTimeout(function () {
            var trans = element.data('trans');
            log('change:' + trans);
            log('lang:' + language);
            log('file:' + translatefile);
            var data = {
                'group' : translatefile,
                'locale': language,
                'name'  : trans,
                '_token': csrf_token,
                'value' : editor.getData()
            };
            //noinspection JSUnresolvedVariable
            $.ajax({
                type    : "POST",
                url     : language_store_path,
                data    : data,
                success : function (r) {
                    if(r !== 'OK'){
                        alert('Error saving!!');
                    }
                },
                dataType: 'text'
            });

            //@todo we also need to check if same translation is on the page and replace this also

        }, 3000);
    });
});
CKEDITOR.disableAutoInline = true;
jQuery(document).ready(function ($) {
    log('Edit mode');

    $('a').each(function () {
        //$(this).attr('href', '#');
        $(this).removeAttr('data-scroll');
    })

    $('[data-trans]').each(function () {
        if ($(this).is('title' , 'img')) {
            // skip not supported
            return;
        }

        // Make sure its editable
        $(this).attr( 'contenteditable', true );
        // Add the editor
        CKEDITOR.inline(this);
    });
});
