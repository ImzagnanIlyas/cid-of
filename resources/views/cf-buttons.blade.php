<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="row col-md-8 mb-2">
    <div class="col-md-6">
        <a class="btn btn-success btn-lg btn-block text-white" type="button" href="{{ backpack_url('facture/create?ordre_id='.$widget['id']) }}">Accepter</a>
    </div>
    <div class="col-md-6">
        <a class="btn btn-danger btn-lg btn-block text-white" type="button" onclick="refuser()">Rejeter</a>
    </div>

    <!-- hidden inputs to exchanging data between php & js -->
    <input type="text" name="ordre_id" value="{{ $widget['id'] }}"  hidden>
    <input type="text" name="url" value="{{ backpack_url('historique') }}"  hidden>
</div>

<script>
    let id = $("input[name=ordre_id]").val();
    let _token   = $('meta[name="csrf-token"]').attr('content');
    let url = $("input[name=url]").val();
    function refuser(){
        swal({
            icon: "warning",
            title: "Donnez le motif",
            // text: "Once deleted, you will not be able to recover this imaginary file!",
            content: {
                element: "input",
                attributes: {
                    placeholder: "Motif",
                    required : "required",
                },
            },
            buttons: ["Annuler", "Rejeter"],
            dangerMode: true,
        })
        .then((value) => {
            if (value) {
                $.ajax({
                    url: '/regeter',
                    type: "POST",
                    data:{
                        ordre_id:id,
                        motif:value,
                        _token: _token
                    },
                    success: function(response) {
                        if(response) {
                            window.location.replace(url);
                        }
                    },
                    error: function(response){
                        swal({
                            icon: "error",
                            text: "Une erreur s'est produite",
                            dangerMode: true,
                        });
                    }
                });
            } else {
                if (value == '') {
                    swal("Veuillez remplir le champ");
                }
            }
        });
    }
</script>
