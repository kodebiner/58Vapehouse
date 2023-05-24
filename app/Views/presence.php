<?= $this->extend('layout') ?>

<?= $this->section('extraScript'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
<style type="text/css">
    #results { padding:20px; border:1px solid; background:#ccc; }
</style>
<?= $this->endSection(); ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <?= view('Views/Auth/_message_block') ?>

    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@m">
            <h3 class="tm-h3"><?=lang('Global.presence')?></h3>
        </div>
    </div>
</div>
<!-- End Of Page Heading -->

<!-- content -->

<div class="container">
    <h1 class="text-center">Take Picture</h1>
    <form method="POST" action="presence/create">
        <div class="row">
            <div class="col-md-6">
                <div id="my_camera"></div>
                <br/>
                <input type=button value="Take Snapshot" onClick="take_snapshot()">
                <input type="hidden" name="image" class="image-tag">
                <input type="hidden" name="geoloc" class="loc">
                <input type="hidden" name="status" value="0" class="status">
            </div>
            <div class="col-md-6">
                <div id="results">Your captured image will appear here...</div>
            </div>
            <div class="col-md-12 text-center">
                <br/>
                <button class="btn btn-success" Onclick="klik()" id="#btn">Check In</button>
                <button class="btn btn-danger"  Onclick="klik2()" id="#btn2">Check Out</button>
            </div>
        </div>
    </form>
</div>

<!-- End Content -->
 

<!-- Search Engine Script -->
<script>

    Webcam.set({
        width: 490,
        height: 390,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
  
    Webcam.attach( '#my_camera' );


  
    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        } );

        // $(".status").val(1);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else { 
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
        

        function showPosition(position) {
            var pos = position.coords.latitude +','+position.coords.longitude;
            $(".loc").val(pos);
        }

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                x.innerHTML = "User denied the request for Geolocation."
                break;
                case error.POSITION_UNAVAILABLE:
                x.innerHTML = "Location information is unavailable."
                break;
                case error.TIMEOUT:
                x.innerHTML = "The request to get user location timed out."
                break;
                case error.UNKNOWN_ERROR:
                x.innerHTML = "An unknown error occurred."
                break;
            }
        }       
    }

    function klik(){
        $(".status").val("1");
        
    }

    function klik2(){
        $(".status").val("0");
        
    }

</script>
<!-- Search Engine Script End -->

<?= $this->endSection() ?>