<?php

namespace edrard\Bconf\Saver;

use edrard\Log\MyLog;


class Diff
{
    static public function diff($base,$new){
        return xdiff_string_diff($base,$new);
    }
}