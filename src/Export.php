<?php

namespace Toproplus\Export;

use Encore\Admin\Extension;

class Export extends Extension
{
    public $name = 'export';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'Export',
        'path'  => 'export',
        'icon'  => 'fa-cloud-download',
    ];
}