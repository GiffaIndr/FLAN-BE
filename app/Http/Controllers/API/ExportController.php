<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use App\Exports\HouseExport;
use App\Exports\ActivityExport;

class ExportController extends Controller
{
 
	public function export_user()
	{
		return Excel::download(new UserExport, 'user.xlsx');
	}
	public function export_activity()
	{
		return Excel::download(new ActivityExport, 'activity.xlsx');
	}
	public function export_house()
	{
		return Excel::download(new HouseExport, 'house.xlsx');
    }
}
