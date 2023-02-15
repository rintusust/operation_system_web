<?php

namespace App\modules\operation\Controllers;
use App\Http\Controllers\Controller;
use App\modules\operation\Models\District;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\ExportDataToExcel;

class HrmController extends Controller
{
    use ExportDataToExcel;


    function getTemplate($key)
    {
        return View::make('operation::Partial_view.' . $key . '_list');
    }








}
