<?php

require_once('classes/ExporterFormats/CSVExport.php');
require_once('classes/ExporterFormats/HTMLExport.php');
require_once('classes/ExporterFormats/JSONExport.php');
require_once('classes/ExporterFormats/XMLExport.php');

use Illuminate\Support\Collection;

// retrieves & formats data from the database for export
class Exporter {
    
    private $format;
    private $filter;

    public function __construct($search, $format) {
        $this->format = $format;
        $this->filter = self::populateFilters($search);
    }

    private function populateFilters($search) {
        $filterArr = [];

        foreach(Constants::SEARCH_COLUMN_MAPPING as $searchArg => $column) {
            if ($search->has($searchArg)) $filterArr[] = "{$column} = '{$search[$searchArg]}'";
        }

        return implode(' AND ', $filterArr);
    }

    function getPlayerStats() {
        $sql = "
            SELECT roster.name, player_totals.*
            FROM player_totals
                INNER JOIN roster ON (roster.id = player_totals.player_id)
            WHERE $this->filter";
        $data = query($sql) ?: [];

        // calculate totals
        foreach ($data as &$row) {
            unset($row['player_id']);

            $row['total_points']    = ($row['3pt'] * 3) + ($row['2pt'] * 2) + $row['free_throws'];
            $row['field_goals_pct'] = $row['field_goals_attempted'] ? (round($row['field_goals'] / $row['field_goals_attempted'], 2) * 100) . '%' : 0;
            $row['3pt_pct']         = $row['3pt_attempted'] ? (round($row['3pt'] / $row['3pt_attempted'], 2) * 100) . '%' : 0;
            $row['2pt_pct']         = $row['2pt_attempted'] ? (round($row['2pt'] / $row['2pt_attempted'], 2) * 100) . '%' : 0;
            $row['free_throws_pct'] = $row['free_throws_attempted'] ? (round($row['free_throws'] / $row['free_throws_attempted'], 2) * 100) . '%' : 0;
            $row['total_rebounds']  = $row['offensive_rebounds'] + $row['defensive_rebounds'];
        }

        return collect($data);
    }

    function getPlayers() {
        $sql = "
            SELECT roster.*
            FROM roster
            WHERE $this->filter";

        return collect(query($sql))->map(function($item, $key) {
            unset($item['id']);
            return $item;
        });
    }

    public function generate($data) {
        
        // return the right data format
        switch($this->format) {
            case 'xml' : return XMLExport::provide($data);  break;
            case 'json': return JSONExport::provide($data); break;
            case 'csv' : return CSVExport::provide($data);  break;
            default    : return HTMLExport::provide($data); break;
        }
    }
}

?>