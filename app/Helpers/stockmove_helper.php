<?php

if (!function_exists('renderStatusBadge')) {
    function renderStatusBadge($status, $origin, $destination, $originid, $destinationid, $outletPick)
    {
        $created = lang('Global.smovecreated');
        $sent    = lang('Global.smovesent');
        $pending = lang('Global.smovepending');
        $success = lang('Global.smoveaccepted');
        $cancel  = lang('Global.smovecanceled');

        $class = 'status-badge ';
        $text  = '';

        if ($status === "0") {
            $class .= 'status-created';
            $text   = $created . $origin;
        } elseif ($status === "1") {
            $class .= 'status-pending';
            if ($outletPick == $destinationid) {
                $text   = $pending . $destination;
            } elseif ($outletPick == $originid) {
                $text   = $sent . $origin;
            } else {
                $text   = $pending . $origin . ' / ' . $destination;
            }
        } elseif ($status === "2") {
            $class .= 'status-canceled';
            $text   = $cancel;
        } elseif ($status === "3") {
            $class .= 'status-accepted';
            $text   = $success . $destination;
        }

        return '<span class="' . $class . '">' . $text . '</span>';
    }
}
