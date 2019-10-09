@php
    $counter = 0;
@endphp
<div id="gifts" @include('crud::inc.field_wrapper_attributes') >
    @forelse ($field['gifts'] as $gift)
        <div class="form-inline gift">
            <div class="form-group">
                <input type="text" autocomplete="off" class="form-control" value="{{$gift->product['title']}}" name="ajax_product">
                <input type="hidden" name="gift_ar[{{$counter}}][product_id]" value="{{$gift['product_id']}}" class="form-controll">
                <ul style="margin:0;" class="product_list"></ul>
            </div>
            @if (!empty($gift['gift_product_id']))
                <div class="form-group">
                    <select class="form-control" name="checker">
                        <option value="product_gift" selected>Подарок из базы</option>
                        <option value="text_gift">Подарок не из базы</option>
                    </select>
                </div>
                <div class="form-group gift_inp product_gift">
                    <input type="text" value="{{$gift->gift_product['title']}}" autocomplete="off" class="form-control" name="ajax_product">
                    <input type="hidden" name="gift_ar[{{$counter}}][gift_id]" value="{{$gift['gift_product_id']}}" class="form-controll">
                    <ul style="margin:0;" class="product_list"></ul>
                </div>
            @else
                <div class="form-group">
                    <select class="form-control" name="checker">
                        <option value="product_gift">Подарок из базы</option>
                        <option value="text_gift" selected>Подарок не из базы</option>
                    </select>
                </div>
                <div class="form-group gift_inp text_gift">
                    <input type="text" value="{{$gift['name']}}" class="form-control" name="gift_ar[{{$counter}}][gift_name]">
                </div>
                <div class="form-group image gift_inp text_gift">
                    <div>
                        <img src="{{url($gift['image'])}}" width="70" id="mainImage" height="70">
                    </div>
                    <div class="btn-group">
                        <label class="btn btn-primary btn-file">
                            Выбрать файл <input type="file" accept="image/*" id="uploadGiftImage" class="hide">
                            <input type="hidden" value="{{$gift['image']}}" id="hiddenImage" name="gift_ar[{{$counter}}][gift_image]" value="">
                        </label>
                        <button class="btn btn-danger" id="remove" type="button"><i class="fa fa-trash"></i></button>
                    </div>
                </div>
            @endif
            <div class="form-group">
                <button class="btn btn-sm btn-default addBlock" type="button"><i class="fa fa-plus"></i></button>
                <button class="btn btn-sm btn-default removeBlock" type="button"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        @php
            $counter++;
        @endphp 
    @empty
        <div class="form-inline gift">
            <div class="form-group">
                <input type="text" autocomplete="off" class="form-control" name="ajax_product">
                <input type="hidden" name="gift_ar[0][product_id]" class="form-controll">
                <ul style="margin:0;" class="product_list"></ul>
            </div>
            <div class="form-group">
                    <select class="form-control" name="checker">
                        <option value="product_gift">Подарок из базы</option>
                        <option value="text_gift">Подарок не из базы</option>
                    </select>
            </div>
            <div class="form-group gift_inp product_gift">
                <input type="text" autocomplete="off" class="form-control" name="ajax_product">
                <input type="hidden" name="gift_ar[0][gift_id]" class="form-controll">
                <ul style="margin:0;" class="product_list"></ul>
            </div>
            <div class="form-group gift_inp text_gift" style="display:none;">
                <input type="text" class="form-control" name="gift_ar[0][gift_name]">
            </div>
            <div class="form-group image gift_inp text_gift" style="display:none;">
                <div>
                    <img width="70" id="mainImage" height="70">
                </div>
                <div class="btn-group">
                        <label class="btn btn-primary btn-file">
                            Выбрать файл <input type="file" accept="image/*" id="uploadGiftImage" class="hide">
                            <input type="hidden" id="hiddenImage" name="gift_ar[0][gift_image]" value="">
                        </label>
                        <button class="btn btn-danger" id="remove" type="button"><i class="fa fa-trash"></i></button>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-sm btn-default addBlock" type="button"><i class="fa fa-plus"></i></button>
                <button class="btn btn-sm btn-default removeBlock" type="button"><i class="fa fa-minus"></i></button>
            </div>
        </div>
    @endforelse
</div>

@push('crud_fields_styles')
<link href="{{ asset('css/custom.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('crud_fields_scripts')
        {{-- YOUR JS HERE --}}
        <script>
            $(document).on('click','.addBlock',function(){
                var counter = $('.gift').length;
                new_block = $(this).parents(".gift").clone();
                new_block.find('input').each(function(){
                    name = $(this).attr('name');
                    $(this).attr('name',name.replace(/(gift_ar\[)([0-9]+)(.*)/,'$1' + counter + '$3'));
                    $(this).val('');
                });
                new_block.find('img').attr('src','');
                $("#gifts").append(new_block);
            });

            $(document).on('click', '.removeBlock', function(){
                setBlock = $(this).closest('.gift');
                setBlock.remove();
            })

            $(document).on('change','select[name="checker"]',function(){
                var t = $(this).val();
                var parent_div = $(this).parents('.gift');
                $(this).find('option').each(function(){
                    $(this).removeAttr('selected');
                })
                $(this).find('option:selected').attr('selected','selected');
                parent_div.find('.gift_inp').hide();
                parent_div.find('.' + $(this).val()).show();
            });
        </script>

        <script>
            $(document).on('change','#uploadGiftImage',function(){
                var parent_div = $(this).parents(".gift");
                var $mainImage = parent_div.find('#mainImage');
                var $uploadImage = parent_div.find("#uploadGiftImage");
                var $hiddenImage = parent_div.find("#hiddenImage");
                var $remove = parent_div.find("#remove");

                var options = {
                        viewMode: 2,
                        checkOrientation: false,
                        autoCropArea: 1,
                        responsive: true,
                        preview : $(this).attr('data-preview'),
                        aspectRatio : $(this).attr('data-aspectRatio')
                    };
                var crop = $(this).attr('data-crop');

                // Hide 'Remove' button if there is no image saved
                if (!$mainImage.attr('src')){
                        $remove.hide();
                    }
                    // Initialise hidden form input in case we submit with no change
                $hiddenImage.val($mainImage.attr('src'));

                var fileReader = new FileReader(),
                    files = this.files,
                    file;

                    if (!files.length) {
                        return;
                    }
                    file = files[0];

                    if (/^image\/\w+$/.test(file.type)) {
                        fileReader.readAsDataURL(file);
                        fileReader.onload = function () {
                            $uploadImage.val("");
                            if(crop){
                                $mainImage.cropper(options).cropper("reset", true).cropper("replace", this.result);
                                // Override form submit to copy canvas to hidden input before submitting
                                $('form').submit(function() {
                                    var imageURL = $mainImage.cropper('getCroppedCanvas').toDataURL(file.type);
                                    $hiddenImage.val(imageURL);
                                    return true; // return false to cancel form action
                                });
                                $reset.click(function() {
                                    $mainImage.cropper("reset");
                                });
                                $remove.show();

                            } else {
                                $mainImage.attr('src',this.result);
                                $hiddenImage.val(this.result);
                                $remove.show();
                            }
                        };
                    } else {
                            alert("Please choose an image file.");
                    }
            });

            $(document).on('click', '#remove', function(){
                var parent_div = $(this).parents(".gift");
                var $mainImage = parent_div.find('#mainImage');
                var $hiddenImage = parent_div.find("#hiddenImage");
                var $remove = parent_div.find("#remove");

                $mainImage.attr('src','');
                $hiddenImage.val('');
                $remove.hide();
               });
        </script>
    <script>
        $(document).on('input','input[name="ajax_product"]',function(){
            var search_row = $(this);
            var product_list = $(this).closest('.form-group').find('ul');
            $.ajax({
                type: "get",
                url: "{{url('api/product')}}",
                data: {'q':search_row.val()},
                success: function (data) {
                    products_options = [];
                    for(var k in data){
                        products_options.push(
                            $('<li/>',{
                                class: 'p_item',
                                'data-id': data[k].product_id,
                                text: data[k].product_name
                            })
                        );
                    }
                        
                    product_list.html(products_options);
                        
                }
            });
        });
        
        $(document).on('click', '.p_item', function(){
            var item = $(this);
            item.closest('.form-group').find('input[type="hidden"]').val(item.data('id'));
            item.closest('.form-group').find('input[name="ajax_product"]').val(item.text());
            item.parent().empty();
        });
    </script>

    @endpush