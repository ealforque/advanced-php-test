<?php

class Constants
{
    public const SEARCH_ARGS = [
        'player', 
        'playerId', 
        'team', 
        'position', 
        'country'
    ];
    
    public const SEARCH_COLUMN_MAPPING = [
        'player'   => 'roster.id', 
        'playerId' => 'roster.name', 
        'team'     => 'roster.team_code', 
        'position' => 'roster.pos', 
        'country'  => 'roster.nationality',
    ];
}