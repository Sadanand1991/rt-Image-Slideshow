/**
* Theme Admin JS
*/

jQuery(document).ready(function($) {
        
    var _orig_send_attachment = wp.media.editor.send.attachment;    

    $('body').on('click', '.upload_image_button', function(e) {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var frame = wp.media( {
            title       : 'Image Uploader',
            multiple    : false,
            button      : { text : 'Insert Image' },
            editable:   false,
            allowLocalEdits: false,
            displaySettings: false,
            displayUserSettings: false,
            filterable: 'uploaded',
            library:   wp.media.query(),
        } );
        var button = $(this).parents('.inner').find('input.rt-image-value');
        var img = $(this).parents('.inner').find('img');
        $(this).parents('.rt-image-uploader').addClass('active');
        _custom_media = true;
        wp.media.editor.send.attachment = function(props, attachment){
            if ( _custom_media ) {
                button.val(attachment.url);
                img.attr('src', attachment.url);
                _custom_media = false;
            } else {
              return _orig_send_attachment.apply( this, [props, attachment] );
            };
        }

        wp.media.editor.open(button);
        return false;
    });
    
    $('body').on('click', '.rt-image-uploader .rt-button-delete', function( e ){
        e.preventDefault();
        $(this).parents('.rt-image-uploader').removeClass('active').find('input.rt-image-value').val();    
    });
    
    $(document).on('click', '.repeater .repeater-footer .add-row-end', function( e ){
        e.preventDefault();
        var $this = $(this);
        sortElements.set({ $el : $(this).closest('.repeater') }).add( false, $this);
        $(this).blur();
    });
    

    $(document).on('click', '.repeater td.remove .add-row-before', function( e ){
        e.preventDefault();
        sortElements.set({ $el : $(this).closest('.repeater') }).add(false, $(this).closest('tr') );
        $(this).blur();
    });
    
    
    $(document).on('click', '.repeater td.remove .rt-button-remove', function( e ){
        e.preventDefault();
        sortElements.set({ $el : $(this).closest('.repeater') }).remove( $(this).closest('tr') );
        $(this).blur();
    });
    
    $( "#rt-event_venue .repeater" ).sortable({
        items  : 'tr.row',
        handle : 'td.order',
        stop: function() {
            sortElements.render();
        }
    });
    
    var sortElements = {
        $el : null,	
        o : {},
        set : function( o ){
            // merge in new option
            $.extend( this, o );
            // add row_count
            this.o.row_count = this.$el.find('> table > tbody > tr.row').length;
            // return this for chaining
            return this;
        },
        
        init : function(){
	    this.render();
        },
        
        render: function() {
            // update row_count
            console.log(this);
            this.o.row_count = this.$el.find('> table > tbody > tr.row').length;
            var attrName = this.$el.find('> table').attr('data-input-name');
            // update order numbers
            this.$el.find('> table > tbody > tr.row').each(function(i){
                $(this).children('td.order').html( i+1 );
                $(this).find('input.rt-image-value').attr('name', attrName+'['+(i+1)+']');
            });


            // empty?
            if( this.o.row_count == 0 )
            {
                this.$el.addClass('empty');
            }
            else
            {
                this.$el.removeClass('empty');
            }


            // row limit reached
            if( this.o.row_count >= this.o.max_rows )
            {
                this.$el.addClass('disabled');
                this.$el.find('> .repeater-footer .rt-button').addClass('disabled');
            }
            else
            {
                this.$el.removeClass('disabled');
                this.$el.find('> .repeater-footer .rt-button').removeClass('disabled');
            }
        },
        
        add: function($before, $this) {
                console.log($this);
                var total_elements = parseInt($this.parents('.repeater').find('.row').length);
                total_elements = total_elements+1;
                
                var new_field_html = this.$el.find('> table > tbody > tr.row-clone').html().replace(/(=["]*[\w-\[\]]*?)(rtcloneindex)/g, '$1' + total_elements);
               
//                new_field_html = this.$el.find('> table > tbody > tr.row-clone').find('input').attr('name', 'slider_image['+total_elements+']');
                var new_field = $('<tr class="row"></tr>').append( new_field_html );
                new_field.find('input.rt-image-value').attr('name','slider_image['+total_elements+']');
                if( ! $before )
                {
                    $before = this.$el.find('> table > tbody > .row-clone');
                }

                $before.before( new_field );
                

                // trigger mouseenter on parent repeater to work out css margin on add-row button
                this.$el.closest('tr').trigger('mouseenter');


                // update order
                this.render();
                
//                console.log(total_elements);
//                $(this).parents('.repeater').find('.row-clone td.order').text(total_elements);
//                var insData = '<tr class="row">';
//                insData += $(this).parents('.repeater').find('.row-clone').html();
//                insData += '</tr>';
//                $(this).parents('.row').before(insData);
        },
        
        remove: function($tr) {
            
            var _this = this;
            
            $tr.addClass('rt-remove-item');
            setTimeout(function(){

                    $tr.remove();


                    // trigger mouseenter on parent repeater to work out css margin on add-row button
                    _this.$el.closest('tr').trigger('mouseenter');


                    // render
                    _this.render();

            }, 400);
            
        }
    };

});