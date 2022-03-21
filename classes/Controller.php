<?php

require_once('classes/Exporter.php');
require_once('Enums/Constants.php');

class Controller {

    private $search;
    private $format;
    private $exporter;

    public function __construct($args) {
        $this->search = self::populateSearch($args);
        $this->format = $args->pull('format') ?: 'html';

        $this->exporter = new Exporter($this->search, $this->format);
    }

    public function exportPlayerStats() {
        $data = $this->exporter->getPlayerStats();
        return $this->export($data);
    }

    public function exportPlayers() {
        $data = $this->exporter->getPlayers($this->search);
        return $this->export($data);
    }

    private function populateSearch($args) {
        return $args->filter(function($value, $key) {
            return in_array($key, Constants::SEARCH_ARGS);
        });
    }

    private function export($data) {
        if (!$data) {
            exit("Error: No data found!");
        }
        
        return $this->exporter->generate($data);
    }
}