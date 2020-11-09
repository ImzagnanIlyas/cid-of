<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="card-body">
    <!-- Button trigger modal-->
    <button class="btn btn-secondary mb-1" type="button" onclick="swal('Hello world!');">Launch demo modal</button>
    <button class="btn btn-secondary mb-1" type="button" data-toggle="modal" data-target="#largeModal">Launch large modal</button>
    <button class="btn btn-secondary mb-1" type="button" data-toggle="modal" data-target="#smallModal">Launch small modal</button>
    <hr>
    <button class="btn btn-primary mb-1" type="button" data-toggle="modal" data-target="#primaryModal">Primary modal</button>
    <button class="btn btn-success mb-1" type="button" data-toggle="modal" data-target="#successModal">Success modal</button>
    <button class="btn btn-warning mb-1" type="button" data-toggle="modal" data-target="#warningModal">Warning modal</button>
    <button class="btn btn-danger mb-1" type="button" data-toggle="modal" data-target="#dangerModal">Danger modal</button>
    <button class="btn btn-info mb-1" type="button" data-toggle="modal" data-target="#infoModal">Info modal</button>
</div>
<div class="row col-md-8 mb-2">
    <div class="col-md-6">
        <a class="btn btn-success btn-lg btn-block text-white" type="button" href="{{ backpack_url('facture/create?ordre_id='.$widget['id']) }}">Accepter</a>
    </div>
    <div class="col-md-6">
        <a class="btn btn-danger btn-lg btn-block text-white" type="button">Rejeter</a>
    </div>
</div>
<div class="modal fade" id="dangerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-danger" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Modal title</h4>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">
            <p>One fine body…</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            <button class="btn btn-danger" type="button">Save changes</button>
        </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
<script>

</script>
