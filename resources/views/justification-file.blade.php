@php
use Illuminate\Support\Facades\Request as FacadesRequest;
@endphp
<ul class="list-group" role="justification-file">

    <!-- here where the files will be added -->

    <!-- hidden inputs to exchanging data between php & js -->
    <input type="text" name="ordre_context" value="{{ FacadesRequest::segment(2) }}"  hidden>
    <input type="text" name="ordre_id" value="{{ FacadesRequest::segment(3) }}"  hidden>
    <input type="text" name="justification_file_id" hidden>
</ul>



<script type="text/javascript">

    $(document).ready(function() {
        let context = $("input[name=ordre_context]").val();
        if (context == 'facture') {
            context = 'reception'
        }else{
            context = 'justification';
        }
        let id = $("input[name=ordre_id]").val();
        let ids = [];
        $url_last_segment = $(location).attr('href').substring($(location).attr('href').lastIndexOf('/') + 1);

        //get files list
        $.ajax({
            url: '/get-file?id='+id+'&context='+context,
            type: "GET",
            success: function(response) {
                if(response) {
                    if (response.length) {
                        for (let index = 0; index < response.length; index++) {
                            const element = response[index];
                            if ($url_last_segment == 'show'){
                                $("ul[role=justification-file]").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center" role="justification_file'+element.id+'"><a href="/download/'+btoa(element.nom)+'">'+element.nom+' <i class="la la-external-link"></i></a></li>');
                            }else{
                                $("ul[role=justification-file]").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center" role="justification_file'+element.id+'"><a href="/download/'+btoa(element.nom)+'">'+element.nom+' <i class="la la-external-link"></i></a><button class="btn btn-pill btn-danger" type="button"  id="'+element.id+'"><i class="la la-trash"></i></button></li>');
                            }
                        }
                    }else{
                        $("ul[role=justification-file]").append('<li class="list-group-item d-flex list-group-item-action justify-content-between align-items-center"> Il n\'y a aucune justification</li>');
                    }

                }
            }
        });

        //remove file from list and add id to input (justification_file_id)
        $("ul[role=justification-file]").on("click",".btn-danger",function(){
            ids.push($(this).attr('id'));
            $("input[name=justification_file_id]").val(ids);
            $(this).parents("li[role=justification_file"+$(this).attr('id')+"]").remove();
        });
    });

</script>
