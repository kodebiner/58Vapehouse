<?= $this->extend('layout') ?>

<?= $this->section('extraScript'); ?>
    <script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
    <script src="js/cdnjs.cloudflare.com_ajax_libs_jquery_3.3.1_jquery.min.js"></script>
    <script src="js/cdnjs.cloudflare.com_ajax_libs_webcamjs_1.0.25_webcam.min.js"></script>
    <!-- <style type="text/css">
        #results { padding:20px; border:1px solid; background:#ccc; }
    </style> -->
<?= $this->endSection(); ?>

<?= $this->section('main') ?>

<!-- Page Heading -->
<div class="tm-card-header uk-light">
    <div uk-grid class="uk-flex-middle">
        <div class="uk-width-1-2@s">
            <h3 class="tm-h3"><?=lang('Global.presence')?></h3>
        </div>
        <?php if ($outletPick != null) { ?>
            <div class ="uk-width-1-2@s uk-text-right@s">
                <a href="sop/todolist" class="uk-button uk-button-primary uk-button-default uk-preserve-color">To Do List</a>
            </div>
        <?php } ?>
    </div>
</div>
<!-- End Of Page Heading -->

<?= view('Views/Auth/_message_block') ?>

<!-- Content -->
<div class="uk-container uk-margin">
    <form class="uk-form-stacked" id="presence" method="POST" action="presence/create">
        <div class="uk-margin uk-light">
            <label class="uk-form-label" for="shift">Shift</label>
            <div class="uk-form-controls">
                <select class="uk-select" name="shift">
                    <option value="0">Pagi (09:00)</option>
                    <option value="1">Siang (12:00)</option>
                    <option value="2">Sore (16:00)</option>
                    <option value="4">Malam (00:00)</option>
                    <option value="3">UGM (10:00)</option>
                </select>
            </div>
        </div>

        <div class="uk-child-width-auto uk-flex-center uk-flex uk-flex-center uk-margin-large-top" id="gridBtn" uk-grid hidden>
            <div>
                <button  class="uk-button uk-button-success uk-button-large" id="checkin" Onclick="klik()" style="width: 223px; border-radius: 15px; font-size: 25.5px;"><?=lang('Global.checkin')?></button>
            </div>
            <div>
                <button class="uk-button uk-button-danger uk-button-large" id="checkout" Onclick="klik2()" style="width: 223px; border-radius: 15px; font-size: 25.5px;"><?=lang('Global.checkout')?></button>
            </div>
        </div>

        <!-- </?php if (empty($checkin)) { ?>
            <div class="uk-margin uk-light">
                <label class="uk-form-label" for="shift">Shift</label>
                <div class="uk-form-controls">
                    <select class="uk-select" name="shift">
                        <option value="0">Pagi (09:00)</option>
                        <option value="1">Siang (12:00 - 16:00)</option>
                        <option value="2">Sore (16:00)</option>
                    </select>
                </div>
            </div>
        </?php } ?> -->

        <div class="uk-child-width-1-2@m" uk-grid uk-height-match="target: > div > .uk-card > .uk-card-body">
            <div>
                <div class="uk-card uk-card-default uk-card-small">
                    <div class="uk-card-body">
                        <div class="uk-flex uk-flex-center">
                            <div id="my_camera"></div>
                        </div>
                    </div>
                    <div class="uk-card-footer uk-height-small uk-flex uk-flex-middle uk-flex-center">
                        <div class="uk-flex uk-flex-center">
                            <input type="hidden" name="image" class="image-tag">
                            <input type="hidden" name="geoloc" class="loc">
                            <input type="hidden" name="status" value="0" class="status">
                            <input class="uk-button uk-button-primary" id="btnTake" type="button" value="Take Snapshot" onClick="take_snapshot()">
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="uk-card uk-card-default uk-card-small">
                    <div class="uk-card-body">
                        <div class="uk-flex uk-flex-center">
                            <div id="results"></div>
                        </div>
                    </div>
                    <div class="uk-card-footer uk-height-small uk-flex uk-flex-middle uk-flex-center">
                        <div class="tm-h4 uk-text-center"><?=lang('Global.imgres')?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="uk-child-width-auto uk-flex-center uk-flex uk-flex-center uk-margin-large-top" id="gridBtn" uk-grid hidden>
            </?php if (empty($checkin)) { ?>
                <div>
                    <button  class="uk-button uk-button-success uk-button-large" id="checkin" Onclick="klik()" style="width: 223px; border-radius: 15px; font-size: 25.5px;"><?=lang('Global.checkin')?></button>
                </div>
            </?php } ?>
            </?php if (empty($checkout)) { ?>
                <div>
                    <button class="uk-button uk-button-danger uk-button-large" id="checkout" Onclick="klik2()" style="width: 223px; border-radius: 15px; font-size: 25.5px;"><?=lang('Global.checkout')?></button>
                </div>
            </?php } ?>
        </div> -->
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
            document.getElementById("gridBtn").removeAttribute("hidden"); 
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