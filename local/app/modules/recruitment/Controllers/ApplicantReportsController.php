<?php

namespace App\modules\recruitment\Controllers;

use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\recruitment\Models\JobApplicantMarks;
use App\modules\recruitment\Models\JobApplicantQuota;
use App\modules\recruitment\Models\JobAppliciant;
use App\modules\recruitment\Models\JobCircular;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantReportsController extends Controller {

    public function admitcard() {
        $applicants = JobAppliciant::where('job_circular_id', 56)
                ->where('applicant_id', 'TL1RMD56918')
                ->where('applicant_password', 'puwqdz')
                ->first();
        //echo $applicants->applicant_name_bng;
        return view('recruitment::reports.admitcard', ['applicants' => $applicants]);
    }

    public function applicantStatusReport(Request $request) {
        if (strcasecmp($request->method(), 'post') == 0) {
            $rules = [
                'circular' => 'required|regex:/^[0-9]+$/',
                'status' => 'required'
            ];
            $this->validate($request, $rules);
            $applicants = JobAppliciant::with(['division', 'district', 'thana', 'marks'])->where('status', $request->status)
                    ->where('job_circular_id', $request->circular);
            if ($request->exists('range') && $request->range != 'all') {
                $applicants->where('division_id', $request->range);
            }
            if ($request->exists('unit') && $request->unit != 'all') {
                $applicants->where('unit_id', $request->unit);
            }
            if ($request->exists('thana') && $request->thana != 'all') {
                $applicants->where('thana_id', $request->thana);
            }
            return view('recruitment::reports.data', ['applicants' => $applicants->paginate(300), 'status' => $request->status]);
        }
        return view('recruitment::reports.applicants_status_report');
    }

    public function applicantAcceptedListReport(Request $request) {
        if (strcasecmp($request->method(), 'post') == 0) {
            $rules = [
                'range' => 'regex:/^[0-9]+$/',
                'unit' => 'regex:/^[0-9]+$/',
                'circular' => 'required|regex:/^[0-9]+$/',
            ];
            $this->validate($request, $rules);
            $category_type = JobCircular::find($request->circular)->category->category_type;
            $applicants = JobApplicantMarks::with(['applicant' => function ($q) {
                            $q->with(['appliciantEducationInfo' => function ($q) {
                                    $q->with('educationInfo');
                                }, 'district', 'thana']);
                        }])->whereHas('applicant', function ($q) use ($request) {

                        $q->whereHas('accepted', function () {
                            
                        })->where('status', 'accepted')->where('job_circular_id', $request->circular);
                        if ($request->unit) {
                            $q->where('unit_id', $request->unit);
                        }
                        if ($request->range) {
                            $q->where('division_id', $request->range);
                        }
                    })->select(DB::raw('*,(IFNULL(written,0)+IFNULL(viva,0)+IFNULL(physical,0)+IFNULL(edu_training,0)+IFNULL(edu_experience,0)+IFNULL(physical_age,0)) as total_mark'))->orderBy('is_bn_candidate', 'desc')->orderBy('specialized', 'desc')->orderBy('total_mark', 'desc');
            if ($request->unit) {
                $pdf = SnappyPdf::loadView('recruitment::reports.accepted_list', [
                            'applicants' => $applicants->get(),
                            'unit' => District::find($request->unit),
                            'type' => $category_type
                        ])
                        ->setPaper('a4')
                        ->setOption('footer-left', url('/'))
                        ->setOption('footer-right', Carbon::now()->format('d-M-Y H:i:s'))
                        ->setOrientation('landscape');
            } else {
                $pdf = SnappyPdf::loadView('recruitment::reports.accepted_list', [
                            'applicants' => $applicants->get(),
                            'range' => Division::find($request->range),
                            'type' => $category_type
                        ])
                        ->setPaper('a4')
                        ->setOption('footer-left', url('/'))
                        ->setOption('footer-right', Carbon::now()->format('d-M-Y H:i:s'))
                        ->setOrientation('landscape');
            }
            return $pdf->download();
        }
        return view('recruitment::reports.applicant_accepted_report');
    }

    public function applicantMarksReport(Request $request) {
        if (strcasecmp($request->method(), 'post') == 0) {
            $rules = [
                'circular' => 'required|regex:/^[0-9]+$/',
            ];
            $this->validate($request, $rules);
            DB::enableQueryLog();
            $markDistribution = JobCircular::find($request->circular)->markDistribution;
            $applicants = JobAppliciant::with(['district', 'circular' => function ($q) {
                            $q->select('id')->with('markDistribution');
                        }, 'thana'])->whereHas('marks', function ($q) {
                        
                    })->where('job_circular_id', $request->circular);
            if ($request->exists('unit') && $request->unit != 'all') {
                $applicants->where('unit_id', $request->unit);
            }
            if ($request->exists('range') && $request->range != 'all') {
                $applicants->where('division_id', $request->range);
            }
            $applicants = $applicants->select('applicant_id', 'applicant_name_bng', 'division_id', 'unit_id', 'thana_id', 'job_circular_id')->orderBy('unit_id')->orderBy('thana_id')->get();
            if ($request->exists('unit') && $request->unit == 'all') {
                $applicants = collect($applicants)->groupBy('district.unit_name_eng')->all();
                $files = [];
                foreach ($applicants as $key => $applicant) {
                    $excel = Excel::create($key . '_applicant_marks', function ($excel) use ($applicant) {
                                $excel->sheet('sheet1', function ($sheet) use ($applicant) {
                                    $sheet->loadView('recruitment::reports.marks_list', [
                                        'applicants' => $applicant
                                    ]);
                                });
                            })->store('xls', storage_path('exports'), true);
                    array_push($files, $excel);
                }
                $zip_archive_name = "applicants_marks_report.zip";
                $zip = new \ZipArchive();
                if ($zip->open(public_path($zip_archive_name), \ZipArchive::CREATE) === true) {
                    foreach ($files as $file) {
                        if (is_array($file))
                            $zip->addFile($file["full"], $file["file"]);
                    }
                    $zip->close();
                }
                foreach ($files as $file) {
                    unlink($file["full"]);
                }
                return response()->download(public_path($zip_archive_name));
            } else {
                $excel = Excel::create('applicant_marks', function ($excel) use ($applicants, $markDistribution) {
                            $excel->sheet('sheet1', function ($sheet) use ($applicants, $markDistribution) {
                                $sheet->loadView('recruitment::reports.marks_list', compact('applicants', 'markDistribution'));
                            });
                        });
                return $excel->download('xls');
            }
        }
        return view('recruitment::reports.applicant_marks_report');
    }

    public function exportData(Request $request) {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');


        $rules = [
            'circular' => 'required|regex:/^[0-9]+$/',
            'status' => 'required',
            'page' => 'regex:/^[0-9]+$/'
        ];
        $this->validate($request, $rules);
        $circular = JobCircular::find($request->circular);
        $category_type = $circular->category->category_type;
        //$category_type = 'battalion_ansar';
        //$applicants = JobAppliciant::with(['division', 'district', 'thana', 'present_division', 'present_district', 'present_thana'])->where('status', $request->status)
        //    ->where('job_circular_id', $request->circular);
        // $applicants = JobAppliciant::with(['division', 'district', 'thana', 'ansar'])->where('status', $request->status)
        // ->where('job_circular_id', $request->circular)->orderBy('id', 'ASC');  
        $applicants = JobAppliciant::with(['division', 'district', 'thana', 'ansar', 'designationdata', 'freezlist', 'blocklist', 'blacklist','govQuota', 'circular'])->where('status', $request->status)
                        ->where('job_circular_id', $request->circular)->orderByRaw("CAST(roll_no as UNSIGNED) ASC");
	    //echo '<pre>';print_r($applicants); exit;

        if ($request->exists('range') && $request->range != 'all') {
            $applicants->where('division_id', $request->range);
        }
        if ($request->exists('unit') && $request->unit != 'all') {
            $applicants->where('unit_id', $request->unit);
        }
        if ($request->exists('thana') && $request->thana != 'all') {
            $applicants->where('thana_id', $request->thana);
        }


        if (!$category_type) {
            if ($request->exists('page')) {
                Excel::create('applicant_list(' . $request->status . ')', function ($excel) use ($applicants, $request, $category_type) {
                    $excel->sheet('sheet1', function ($sheet) use ($applicants, $request, $category_type) {
                        $sheet->setColumnFormat(array(
                            'G' => '@'
                        ));
                        $sheet->setAutoSize(false);
                        $sheet->setWidth('A', 5);
                        $sheet->loadView('recruitment::reports.excel_data', ['index' => ((intval($request->page) - 1) * 300) + 1, 'applicants' => $applicants->skip((intval($request->page) - 1) * 300)->limit(300)->get(), 'status' => $request->status, 'ctype' => $category_type]);
                    });
                })->download('xls');
            }
        } else if ($category_type == "other" || !$category_type) {
            if ($request->exists('page')) {
                Excel::create($circular->circular_name, function ($excel) use ($applicants, $request, $category_type) {
                    $excel->sheet('sheet1', function ($sheet) use ($applicants, $request, $category_type) {
                        $sheet->setColumnFormat(array(
                            'G' => '@'
                        ));
                        $sheet->setAutoSize(false);
                        $sheet->setWidth('A', 5);
                        $sheet->loadView('recruitment::reports.excel_data_other', ['index' => ((intval($request->page) - 1) * 300) + 1, 'applicants' => $applicants->skip((intval($request->page) - 1) * 300)->limit(300)->get(), 'status' => $request->status, 'ctype' => $category_type]);
                    });
                })->download('xls');
            } else if ($applicants->count() <= 20000) {
                $file_name = public_path();
                Excel::create(str_replace("/", "", implode("_", explode(" ", $circular->circular_name))), function ($excel) use ($applicants, $request, $category_type) {
                    $i = 1;
                    $applicants->chunk(15000, function ($data) use ($excel, $request, $category_type, $i) {
                        $excel->sheet('sheet' . $i, function ($sheet) use ($data, $request, $category_type) {
                            $sheet->setColumnFormat(array(
                                'G' => '@'
                            ));

                            $sheet->getStyle('A1:R1')->getAlignment()->setWrapText(true);
                            $sheet->getStyle('A2:R2')->getAlignment()->setWrapText(true);


                            $sheet->setAutoSize(true);
                            $sheet->setWidth('A', 5);
                            $sheet->loadView('recruitment::reports.excel_data_other_ict3', ['index' => 1, 'applicants' => $data, 'status' => $request->status, 'ctype' => $category_type]);
                        });
                        $i++;
                    });
                })->save('xls', $file_name, true);
                return response()->json(['status' => true, 'message' => str_replace("/", "", implode("_", explode(" ", $circular->circular_name))) . ".xls"]);
            } else {
                $unit = "";
                $range = "";
                if ($request->exists('unit') && $request->unit != 'all') {
                    $unit = District::find($request->unit);
                }
                if ($request->exists('range') && $request->range != 'all') {
                    $range = Division::find($request->range);
                }
                try {
                    ini_set('memory_limit', '-1');
                    ob_implicit_flush(true);
                    ob_end_flush();
                    echo "Start Processing....";
                    $c = $applicants->get()->groupBy('present_division_id');
                    $total = count($c);
                    $counter = 1;
                    $file_path = storage_path('exports');
                    if (!File::exists($file_path))
                        File::makeDirectory($file_path);
                    $files = [];
                    $c->each(function ($applicant_list, $key) use ($category_type, $request, $total, &$counter, $file_path, &$files, $circular) {
                        sleep(1);
                        $file_name = Division::find($key)->division_name_eng;
                        $file = Excel::create($file_name, function ($excel) use ($applicant_list, $request, $category_type) {
                                    $excel->sheet('sheet1', function ($sheet) use ($applicant_list, $request, $category_type) {
                                        $sheet->setColumnFormat(array(
                                            'G' => '@'
                                        ));
                                        $sheet->setAutoSize(false);
                                        $sheet->setWidth('A', 5);
                                        $sheet->loadView('recruitment::reports.excel_data_other', ['index' => 1, 'applicants' => $applicant_list, 'status' => $request->status, 'ctype' => $category_type]);
                                    });
                                })->save('xls', $file_path, true);
                        array_push($files, $file);
                        echo "Processed $counter of $total";
                        $counter++;
                    });
                    if ($range) {
                        $zip_archive_name = $range->division_name_eng . time() . ".zip";
                    } else if ($unit) {
                        $zip_archive_name = $unit->unit_name_eng . time() . ".zip";
                    } else {
                        $zip_archive_name = str_replace("/", "", implode("_", explode(" ", explode("|", $circular->circular_name)[0]))) . ".zip";
                    }
                    $zip = new \ZipArchive();
                    if ($zip->open(public_path($zip_archive_name), \ZipArchive::CREATE) === true) {
                        foreach ($files as $file) {
                            $zip->addFile($file["full"], $file["file"]);
                        }
                        $zip->close();
                    } else {
                        throw new \Exception("Can`t create file");
                    }
                    foreach ($files as $file) {
                        unlink($file["full"]);
                    }
                    return response()->json(['status' => true, 'message' => $zip_archive_name]);
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => $e->getMessage()]);
                }
            }
        } else {
            $category_type = $circular->category->category_type;
            $unit = "";
            $range = "";
            if ($request->exists('unit') && $request->unit != 'all') {
                $unit = District::find($request->unit);
            }
            if ($request->exists('range') && $request->range != 'all') {
                $range = Division::find($request->range);
            }
            try {
                ini_set('memory_limit', '-1');
                ini_set('max_execution_time', '0');
				
				

                ob_implicit_flush(true);
                ob_end_flush();
                echo "Start Processing....";
                $c = $applicants->get()->groupBy('unit_id');

                $total = count($c);
                $counter = 1;
                $file_path = storage_path('exports');
                if (!File::exists($file_path))
                    File::makeDirectory($file_path);
                $files = [];
                $c->each(function ($applicant_list, $key) use ($category_type, $request, $total, &$counter, $file_path, &$files) {
                    sleep(1);
					
					//print_r($applicant_list);  exit;      

                    $file_name_district = District::find($key)->unit_name_eng;

                    if (isset($request->export_template) && $request->export_template == "2") {
                        //echo 'rrrtttt';
                        //$file = Excel::create('applicant_list_' . $counter, function ($excel) use ($applicant_list, $request, $category_type, $counter) {
							
						if($category_type == 'battalion_ansar')	{
							
							$file = Excel::create($file_name_district, function ($excel) use ($applicant_list, $request, $category_type, $counter) {
										$excel->getDefaultStyle()
												->getAlignment()
												->applyFromArray(array(
													'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
													'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
													'wrap' => TRUE
										));
										$excel->sheet('sheet1', function ($sheet) use ($applicant_list, $category_type, $counter, $request) {
											$sheet->setColumnFormat(array(
												'G' => '@'
											));
											
											$sheet->setAutoSize(array(
													'B','E', 'F', 'L', 'Q', 
												));
											$sheet->getRowDimension(1)->setRowHeight(50);
											$sheet->getStyle('A1:T1')->getAlignment()->setHorizontal('center');
											$sheet->getStyle('A1:T1')->getAlignment()->setVertical('center');
											
											$sheet->setWidth('A', 5);
											$sheet->loadView('recruitment::reports.excel_data_battalion', ['index' => (($counter - 1) * 300) + 1, 'applicants' => $applicant_list, 'status' => $request->status, 'ctype' => $category_type]);
											//$sheet->loadView('recruitment::reports.excel_data_batt', ['index' => (($counter - 1) * 300) + 1, 'applicants' => $applicant_list, 'status' => $request->status, 'ctype' => $category_type]);
										});
									})->store('xls', $file_path, true);
							
							
						}else{
							
							$file = Excel::create($file_name_district, function ($excel) use ($applicant_list, $request, $category_type, $counter) {
										$excel->getDefaultStyle()
												->getAlignment()
												->applyFromArray(array(
													'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
													'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
													'wrap' => TRUE
										));
										$excel->sheet('sheet1', function ($sheet) use ($applicant_list, $category_type, $counter, $request) {
											$sheet->setColumnFormat(array(
												'G' => '@'
											));
											$sheet->setWidth('A', 5);
											//$sheet->loadView('recruitment::reports.excel_data_battalion', ['index' => (($counter - 1) * 300) + 1, 'applicants' => $applicant_list, 'status' => $request->status, 'ctype' => $category_type]);
											//$sheet->loadView('recruitment::reports.excel_data_ansar_marks', ['index' => (($counter - 1) * 300) + 1, 'applicants' => $applicant_list, 'status' => $request->status, 'ctype' => $category_type]);
											$sheet->loadView('recruitment::reports.excel_data_other', ['index' => (($counter - 1) * 300) + 1, 'applicants' => $applicant_list, 'status' => $request->status, 'ctype' => $category_type]);
										});
									})->store('xls', $file_path, true);
						}
								
                    } else {
                        //echo $category_type;
                        //print_r($applicant_list); exit;
                        // echo $c->post_office_name_bng;     
                        $file = Excel::create('applicant_list_' . $counter, function ($excel) use ($applicant_list, $request, $category_type, $counter) {
                                    $excel->getDefaultStyle()
                                            ->getAlignment()
                                            ->applyFromArray(array(
                                                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                                'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
                                                'wrap' => TRUE
                                    ));
                                    $excel->sheet('sheet1', function ($sheet) use ($applicant_list, $category_type, $counter, $request) {
                                        $sheet->setColumnFormat(array(
                                            'G' => '@'
                                        ));
                                        //$sheet->mergeCells("K1:N1");
                                        // $sheet->mergeCells("L1:O1");
                                        $sheet->mergeCells("A1:A2");
                                        $sheet->mergeCells("B1:B2");
                                        $sheet->mergeCells("C1:C2");
                                        $sheet->mergeCells("D1:D2");
                                        $sheet->mergeCells("E1:E2");
                                        $sheet->mergeCells("F1:F2");
                                        $sheet->mergeCells("G1:G2");
                                        $sheet->mergeCells("H1:H2");
                                        $sheet->mergeCells("I1:I2");
                                        $sheet->mergeCells("J1:J2");
                                        $sheet->mergeCells("K1:K2");
                                        $sheet->mergeCells("L1:L2");
                                        $sheet->mergeCells("M1:M2");
                                        $sheet->mergeCells("N1:N2");
                                        $sheet->mergeCells("O1:O2");
                                        // $sheet->mergeCells("O1:O2");
                                        $sheet->mergeCells("P1:P2");
                                        $sheet->mergeCells("Q1:Q2");
                                        $sheet->mergeCells("R1:R2");
                                        $sheet->mergeCells("S1:S2");
                                        $sheet->mergeCells("T1:T2");
                                        $sheet->mergeCells("U1:U2");
                                        $sheet->setWidth('A', 100);
                                        $sheet->loadView('recruitment::reports.excel_data_batt', ['index' => (($counter - 1) * 300) + 1, 'applicants' => $applicant_list, 'status' => $request->status, 'ctype' => $category_type]);
                                    });
                                })->store('xls', $file_path, true);
                    }
                    array_push($files, $file);
                    echo "Processed $counter of $total";
                    $counter++;
                });
                if ($range) {
                    $zip_archive_name = $range->division_name_eng . time() . ".zip";
                } else if ($unit) {
                    $zip_archive_name = $unit->unit_name_eng . time() . ".zip";
                } else {
                    $zip_archive_name = "applicant_list" . time() . ".zip";
                }
                $zip = new \ZipArchive();
                if ($zip->open(public_path($zip_archive_name), \ZipArchive::CREATE) === true) {
                    foreach ($files as $file) {
                        $zip->addFile($file["full"], $file["file"]);
                    }
                    $zip->close();
                } else {
                    throw new \Exception("Can`t create file");
                }
                foreach ($files as $file) {
                    unlink($file["full"]);
                }
                return response()->json(['status' => true, 'message' => $zip_archive_name]);
            } catch (\Exception $e) {

                return response()->json(['status' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function exportDataAsPdf(Request $request) {
        $rules = [
            'circular' => 'required|regex:/^[0-9]+$/',
            'status' => 'required',
            'page' => 'regex:/^[0-9]+$/'
        ];
        $this->validate($request, $rules);
        $circular = JobCircular::find($request->circular);
        $applicants = JobAppliciant::where('status', $request->status)
                ->where('job_circular_id', $request->circular);
        if ($request->exists('range') && $request->range != 'all') {
            $applicants->where('division_id', $request->range);
        }
        if ($request->exists('unit') && $request->unit != 'all') {
            $applicants->where('unit_id', $request->unit);
        }
        if ($request->exists('thana') && $request->thana != 'all') {
            $applicants->where('thana_id', $request->thana);
        }
        $applicants = $applicants->get();
        ob_implicit_flush(true);
        ob_end_flush();
        echo "Start Processing....";
        $zip = new \ZipArchive();
        $z_file_name = str_replace("/", "", implode("_", explode(" ", $circular->circular_name))) . ".zip";
        $downloaded_folder = "download";
        if (!File::exists(public_path($downloaded_folder))) {
            File::makeDirectory(public_path($downloaded_folder), 0775, true);
        }
        $zip_name = public_path($downloaded_folder . "/" . $z_file_name);
        $zip->open($zip_name, \ZipArchive::CREATE);
        $files = [];
        $counter = 1;
        $total = collect($applicants)->count();
        $path = storage_path('temp' . Carbon::now()->timestamp);
        if (!File::exists($path))
            File::makeDirectory($path, 0775, true);
        foreach ($applicants as $applicant) {
            $file_name = $applicant->applicant_id . '.pdf';
            SnappyPdf::loadView('recruitment::reports.applicant_detail_view', compact('applicant'))->save($path . '/' . $file_name);
            $zip->addFile($path . '/' . $file_name, $file_name);
            array_push($files, $path . '/' . $file_name);
            echo "Processed $counter of $total";
            $counter++;
        }
        $zip->close();
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($path);
        return response()->json(['status' => false, 'message' => $z_file_name]);
    }

    public function applicantDetailsReport() {
        $circulars = JobCircular::pluck('circular_name', 'id');
        $circulars->prepend('--Select a circular--', '');
        return view('recruitment::reports.applicant_details_report', compact('circulars'));
    }

    public function exportApplicantDetailReport(Request $request) {
        $quota = [
            "son_of_freedom_fighter" => "মুক্তিযোদ্ধার সন্তান",
            "grandson_of_freedom_fighter" => "মুক্তিযোদ্ধার সন্তানের সন্তান",
            "member_of_ansar_or_vdp" => "আনসার - ভিডিপি সদস্য",
            "orphan" => "এতিম",
            "physically_disabled" => "শারীরিক প্রতিবন্ধী",
            "tribe" => "উপজাতি"
        ];
        $applicants = JobAppliciant::with(['circular' => function ($q) {
                        $q->select('id', 'circular_name', 'end_date', 'job_category_id', 'start_date');
                        $q->with('category');
                    }, 'govQuota', 'division', 'district', 'thana', 'circular.trainingDate', 'appliciantEducationInfo' => function ($q) {
                        $q->with('educationInfo');
                    }])->where('job_circular_id', $request->circular_id)->whereIn('status', $request->status)->get();
        $path = storage_path('exports');
        $files = [];
        foreach ($applicants as $applicant) {
            $file_path = $path . '/' . ($applicant->roll_no ? $applicant->roll_no : $applicant->applicant_name_eng) . '.pdf';
            if (File::exists($file_path)) {
                array_push($files, ['path' => $file_path, 'name' => $applicant->roll_no . '.pdf']);
                continue;
            }
            $pdf = SnappyPdf::loadView('recruitment::reports.applicant_details_download', ['ansarAllDetails' => $applicant, 'quota' => $quota])
                    ->setOption('encoding', 'UTF-8')
                    ->setOption('zoom', 0.9)
                    ->save($file_path);
            array_push($files, ['path' => $file_path, 'name' => $applicant->roll_no . '.pdf']);
            echo $applicant->roll_no . '.pdf-->done<br>';
            ob_flush();
            flush();
        }
        $zip_path = public_path('applicant_detail_' . $request->circular_id . '.zip');
        $zip = new \ZipArchive();
        $zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($files as $file) {
            $zip->addFile($file['path'], $file['name']);
            echo "adding to zip --" . $file['name'] . "<br>";
            ob_flush();
            flush();
        }
        $zip->close();
        foreach ($files as $file) {
            @unlink($file['path']);
            echo "delete --" . $file['name'] . "<br>";
            ob_flush();
            flush();
        }
        return redirect()->back();
    }

    public function download(Request $request) {
        return response()->download(public_path($request->file_name))->deleteFileAfterSend(true);
    }

    public function applicantFormDownload(Request $request) {
        ini_set('max_execution_time', '0');
        if (strcasecmp($request->method(), 'post') == 0) {
            $applicants = JobAppliciant::with(['circular.constraint', 'district', 'thana', 'present_district', 'present_thana', 'quotaType', 'education']);
            if ($request->exists('applicant_id') && !empty($request->applicant_id)) {
                $applicants->where('applicant_id', $request->applicant_id);
            } else {
                if ($request->exists('job_circular_id') && !empty($request->job_circular_id)) {
                    $applicants->where('job_circular_id', $request->job_circular_id);
                } else {
                    return response()->json(['error' => "Please select a circular."]);
                }
                if ($request->exists('status') && !empty($request->status)) {
                    $applicants->where('status', $request->status);
                } else {
                    return response()->json(['error' => "Please select a status."]);
                }
            }
            ob_implicit_flush(true);
            ob_end_flush();
            $zip = new \ZipArchive();
            $savePath = public_path('download/applicant_form');
            $downloadPath = public_path('download/applicant_form_download');
            if (!File::exists($savePath))
                File::makeDirectory($savePath, 0775, true);
            if (!File::exists($downloadPath))
                File::makeDirectory($downloadPath, 0775, true);
            $files = [];
            $applicants = $applicants->get();
            $zipPath = "download/applicant_form_download/circular_" . $applicants[0]->circular->id . ".zip";
            $zip->open(public_path($zipPath), \ZipArchive::CREATE);
            foreach ($applicants as $applicant) {
                $tempFilePath = $savePath . '/' . $applicant->applicant_id . '.pdf';
                try {
                    SnappyPdf::loadView('recruitment::reports.app_form_template', compact('applicant'))
                            ->setOrientation('portrait')->setOption('page-size', 'a4')
                            ->save($tempFilePath);
                    array_push($files, $tempFilePath);
                    $zip->addFile($tempFilePath, $applicant->applicant_id . '.pdf');
                } catch (\Exception $e) {
                    Log::info('[' . $e->getCode() . '] "' . $e->getMessage() . '" on line ' . $e->getTrace()[0]['line'] . ' of file ' . $e->getTrace()[0]['file']);
                }
            }
            $zip->close();
            foreach ($files as $file) {
                unlink($file);
            }
            return response()->json(['file' => 'applicant_forms.zip', 'download' => '/' . $zipPath]);
        }
        return view('recruitment::reports.applicant_form_download');
    }

}
