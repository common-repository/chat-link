document.addEventListener('DOMContentLoaded', function() {
    var file_frame;
    document.getElementById('upload_image_button').addEventListener('click', function(event) {
        event.preventDefault();
        if (file_frame) {
            file_frame.open();
            return;
        }
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload an Image',
            button: {
                text: 'Use this image',
            },
            multiple: false
        });
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            document.getElementById('chat_link_custom_image').value = attachment.url;
            document.getElementById('chat_link_custom_image_preview').innerHTML = '<img src="' + attachment.url + '" style="max-width:100px; max-height:100px; display:inline; margin-top:10px;" /><button type="button" class="button" id="remove_image_button" style="display:inline; margin-left:10px;">Remove Image</button>';
        });
        file_frame.open();
    });

    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'remove_image_button') {
            document.getElementById('chat_link_custom_image').value = '';
            document.getElementById('chat_link_custom_image_preview').innerHTML = '';
        }
    });

    document.getElementById('chat_link_number').addEventListener('input', function() {
        var number = this.value.replace(/[^0-9]/g, '');
        if (number.length < 10 || number.length > 15) {
            document.getElementById('chat-number-description').style.display = 'block';
        } else {
            document.getElementById('chat-number-description').style.display = 'none';
        }
    });
});
