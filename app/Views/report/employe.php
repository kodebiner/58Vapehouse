<?= $this->extend('layout') ?>

<?= $this->section('extraScript') ?>
<script src="js/ajax.googleapis.com_ajax_libs_jquery_3.6.4_jquery.min.js"></script>
<script src="js/cdn.datatables.net_1.13.4_js_jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>


<!-- Page Heading -->
<div class="tm-card-header uk-light uk-margin-bottom">
    <div uk-grid class="uk-child-width-1-2@m uk-child-width-auto uk-flex-middle">
        <div>
            <h3 class="tm-h3"><?=lang('Global.employereport')?></h3>
        </div>
        
        <!-- Button Trigger Modal export -->
        <div class="uk-text-right@m">
            <a
                type="button"
                class="uk-button uk-button-primary uk-preserve-color uk-margin-right-remove"
                target="_blank"
                href="<?= base_url('export/employe') . '?' . http_build_query([
                    'daterange' => $daterange,
                    'search'    => $search
                ]) ?>">
                
                <?=lang('Global.export')?>
            </a>
        </div>
    </div>
</div>

<div class="uk-margin">
    <form id="filterForm" action="report/employe" method="GET">
        <!-- Filter -->
        <div uk-grid class="uk-child-width-1-3@m uk-child-width-auto uk-flex-between@m uk-flex-middle">
            <div class="uk-inline">
                <span class="uk-form-icon uk-form-icon-flip" uk-icon="calendar"></span>
                <input
                    type="hidden"
                    name="daterange"
                    id="daterange-hidden"
                    value="<?= esc($daterange) ?>"
                >
                <input
                    type="text"
                    id="daterange-display"
                    class="uk-input"
                >
            </div>

            <div class="uk-text-right@l">
                <!-- Search Filter -->
                <div class="uk-search uk-search-default"
                    style="background-color:#fff;border-radius:7px;">
                    <span uk-search-icon style="color:#000;"></span>
                    <input
                        class="uk-search-input"
                        type="search"
                        placeholder="Search"
                        name="search"
                        value="<?= esc($search ?? '') ?>"
                        style="border-radius:7px;"
                    >
                </div>
            </div>

            <div class="uk-hidden@l uk-text-right">
                <button type="submit" class="uk-button uk-button-primary" style="border-radius: 10px;">Cari</button>
            </div>
        </div>
    </form>
</div>

<table class="uk-table uk-table-divider uk-table-responsive uk-margin-top">
    <thead>
        <tr>
            <th class="uk-text-large uk-text-bold"><?=lang('Global.name')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.accessLevel')?></th>
            <th class="uk-text-center uk-text-large uk-text-bold"><?=lang('Global.value')?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($employetrx)) {
            foreach ($employetrx as $employe) { ?>
                <tr>
                    <td style="color:white;"><?=$employe['name']?></td>
                    <td class="uk-text-center" style="color:white;"><?=$employe['role']?></td>
                    <td class="uk-text-center" style="color:white;"><?php echo "Rp. ".number_format($employe['value'],0,',','.');" ";?></td>
                </tr>
            <?php }
        } else { echo '<tr><td colspan="3" class="uk-text-center" style="color:white;">'.'Data Tidak Tersedia'.'</td></tr>'; } ?>
    </tbody>
</table>

<!-- End Of Page Heading -->
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });

    $(function () {
        let range = $('#daterange-hidden').val();
        let start = moment().startOf('day');
        let end   = moment().endOf('day');

        if (range) {
            const [startStr, endStr] = range.split(' - ');

            start = moment(startStr, 'YYYY-MM-DD');
            end   = moment(endStr, 'YYYY-MM-DD');
        }

        $('#daterange-display').daterangepicker({
            startDate: start,
            endDate: end,
            maxDate: new Date(),
            autoUpdateInput: true,
            locale: {
                format: 'MM/DD/YYYY'
            }
        });

        $('#daterange-display').on('apply.daterangepicker', function(ev, picker) {

            $('#daterange-hidden').val(
                picker.startDate.format('YYYY-MM-DD')
                + ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );

            $('#filterForm').submit();
        });
    });

    let timer;

    $('input[name="search"]').on('keyup', function() {
        clearTimeout(timer);

        timer = setTimeout(function() {
            $('#filterForm').submit();
        }, 500);
    });
</script>
<?= view('Views/Auth/_message_block') ?>

<?= $this->endSection() ?>