<?php

require_once('classes/ExporterFormats/BaseExport.php');

Class JSONExport extends BaseExport {

    public static function provide($data) {
        header('Content-type: application/json');

        return json_encode($data->all());
    }
}