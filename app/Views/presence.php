<?= $this->extend('layout') ?>

<?= $this->section('extraScript'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
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

<!-- Content -->
<div class="uk-container uk-margin">
    <div class="uk-text-center uk-light">
        <h1 class="tm-h2">Take Picture</h1>
    </div>
    <form class="uk-form-stacked" method="POST" action="presence/create">
        <div class="" uk-grid>
            <div class="uk-width-1-2@m uk-card uk-card-default">
                <div class="uk-flex uk-flex-center" id="my_camera">
                    <input type="hidden" name="image" class="image-tag">
                    <input type="hidden" name="geoloc" class="loc">
                    <input type="hidden" name="status" value="0" class="status">
                </div>
                <div class="uk-text-center">
                    <input type="button" value="Take Snapshot" onClick="take_snapshot()">
                </div>
            </div>
            <div class="uk-width-1-2@m uk-margin">
                <h3 class="tm-h4">Your captured image will appear here...</h3>
                <div id="results"></div>
            </div>
            <div class="uk-margin-bottom">
                <button class="uk-button uk-button-success" Onclick="klik()" id="#btn"><?=lang('Global.checkin')?></button>
                <button class="uk-button uk-button-danger"  Onclick="klik2()" id="#btn2"><?=lang('Global.checkout')?></button>
            </div>
        </div>
    </form>
</div>
<!-- End Content -->
 
<!-- Camera Script -->
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
<!-- Camera Script End -->

<?= $this->endSection() ?>