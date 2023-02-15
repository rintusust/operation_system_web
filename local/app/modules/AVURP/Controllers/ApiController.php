<?php

namespace App\modules\AVURP\Controllers;

use App\modules\AVURP\Models\VDPAnsarInfo;
use App\modules\AVURP\Repositories\VDPInfo\OperationVDPInfoInterface;
use App\modules\AVURP\Requests\VDPInfoRequest;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ApiController extends Controller
{
    private $infoRepository;

    /**
     * ApiController constructor.
     * @param OperationVDPInfoInterface $infoRepository
     */
    public function __construct(OperationVDPInfoInterface $infoRepository)
    {
        $this->infoRepository = $infoRepository;
    }

    public function index(Request $request)
    {
        $limit = $request->limit?$request->limit:-1;
        return response()->json($this->infoRepository->getInfos($request->all(), $limit,$request->action_user_id,$request->is("AVURP/api/*")));
    }

    public function show(Request $request,$id)
    {
        $info = $this->infoRepository->getInfo($id,$request->action_user_id);
        if(!$info) return response()->json(['message'=>'Not found'],404);
        return response()->json($info);
    }

    public function edit(Request $request,$id)
    {

        $info = $this->infoRepository->getInfoForEdit($id,$request->action_user_id);
        if(!$info) return response()->json(['message'=>'Not found'],404);
        return response()->json($info);
    }

    public function store(VDPInfoRequest $request)
    {
        $response = $this->infoRepository->create($request,$request->action_user_id);
        return response()->json($response);

    }
    public function update(VDPInfoRequest $request,$id)
    {
        $response = $this->infoRepository->update($request,$id,$request->action_user_id);
        return response()->json($response);

    }
    public function verifyVDP($id)
    {
        $response = $this->infoRepository->verifyVDP($id);
        return $response;
    }

    public function approveVDP($id)
    {
        $response = $this->infoRepository->approveVDP($id);
        return $response;
    }

    public function verifyAndApproveVDP($id)
    {
        $response = $this->infoRepository->verifyAndApproveVDP($id);
        return $response;
    }
    public function image($id)
    {
        $path = storage_path('avurp/profile_pic');
        $image = VDPAnsarInfo::find($id);
        if($image&&$image->profile_pic){
            $path.="/".$image->profile_pic;
        }else{
            $path = public_path('dist/img/nimage.png');
        }
        Log::info("image-path".$path);
        if(File::exists($path)){
            return Image::make($path)->response();
        } else{
            return Image::make(public_path('dist/img/nimage.png'))->response();
        }
    }
}
