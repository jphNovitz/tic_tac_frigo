<?php

namespace App\Enums;

enum UnitLabel: string
{
    case Piece = 'pièce';
    case Portion = 'portion';
    case Slice = 'tranche';
    case Bottle = 'bouteille';
    case Can = 'canette';
    case Jar = 'pot';
    case Box = 'boîte';
    case Bag = 'sachet';
    case Pack = 'paquet';
    case Tray = 'barquette';
    case Brick = 'brique';
    case Tube = 'tube';
}
