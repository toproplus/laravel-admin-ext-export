<?php

namespace Toproplus\Export\Http\Controllers;

use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;

class ExportController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('Title')
            ->description('Description')
            ->body(view('export::index'));
    }
}