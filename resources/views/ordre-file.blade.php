<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@php
use Illuminate\Support\Facades\Request as FacadesRequest;
@endphp
<ul class="list-group" role="ordre-file">

    <!-- here where the file will be added -->

    <!-- hidden inputs to exchanging data between php & js -->
    <input type="text" name="ordre_context" value="{{ FacadesRequest::segment(2) }}"  hidden>
    <input type="text" name="ordre_id" value="{{ FacadesRequest::segment(3) }}"  hidden>
    <input type="text" name="ordre_file_id" hidden>
</ul>



<script type="text/javascript">

    $(document).ready(function() {
        let context = $("input[name=ordre_context]").val();
        let id = $("input[name=ordre_id]").val();
        $url_last_segment = $(location).attr('href').substring($(location).attr('href').lastIndexOf('/') + 1);

        //get file
        $.ajax({
            url: '/get-file?id='+id+'&context='+context,
            type: "GET",
            success: function(response) {
                if(response) {
                    let file = response[0];
                    if ($url_last_segment == 'show') {
                        $("ul[role=ordre-file]").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center" role="ordre_file"><a href="/download/'+btoa(file.nom)+'">'+file.nom+'</a></li>');
                    }else{
                        $("ul[role=ordre-file]").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center" role="ordre_file"><a href="/download/'+btoa(file.nom)+'">'+file.nom+'</a><button class="btn btn-pill btn-danger" type="button"  id="'+file.id+'"><i class="la la-trash"></i></button></li>');
                    }
                }
            }
        });

        //remove file and add id to input (ordre_file_id)
        $("ul[role=ordre-file]").on("click",".btn-danger",function(){

            $("input[name=ordre_file_id]").val($(this).attr('id'));
            $(this).parents("li[role=ordre_file]").remove();
            $('input[name=of]').prop("disabled", false);
        });
    });

</script>
