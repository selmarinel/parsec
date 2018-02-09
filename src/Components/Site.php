<?php

namespace Parsec\Components;


class Site
{
    public $linkFrom;
    public $linkTo;
    public $anchor;
    public $vendor;
    public $status;

    const LIVE = 'LIVE';
    const NOT_FOUND = 'NOT_FOUND';
    const ANCHOR_MISMATCH = 'ANCHOR MISMATCH';
    const EXCEPTION = 'EXCEPTION';

    public $links = [];



}