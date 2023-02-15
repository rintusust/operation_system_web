<?php

namespace App\modules\operation\Requests;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Class OperationVDPInfoRequest
 * @package App\modules\operation\Requests
 */
class OperationVDPInfoRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->is('/api/')){
            switch ($this->method()){
                case 'POST':
                    return [
                        'ansar_name_bng'=>'required',
                        'father_name_bng'=>'required',
                        'designation'=>'required',
                        'date_of_birth'=>'required',
                        'mobile_no_self'=>'required|unique:operation.tbl_vdp_ansar_info,mobile_no_self|regex:/^(\+88)?[0-9]{11}$/',
                        'blood_group_id'=>'required',
                        'division_id'=>'required|numeric|min:1',
                        'union_word_text'=>'required',
                        'thana_id'=>'required|numeric|min:1',
                        'geo_id'=>'sometimes|unique:operation.tbl_vdp_ansar_info',

                    ];
                case 'PATCH':
                    return [
                        'ansar_name_bng'=>'required',
                        'father_name_bng'=>'required',
                        'designation'=>'required',
                        'date_of_birth'=>'required',
                        'mobile_no_self'=>'required|regex:/^(\+88)?[0-9]{11}$/|unique:operation.tbl_vdp_ansar_info,mobile_no_self,'.$this->route()->parameters()['info'],
                        'blood_group_id'=>'required',
                        'division_id'=>'required|numeric|min:1',
                        'unit_id'=>'required|numeric|min:1',
                        'thana_id'=>'required|numeric|min:1',
                        'union_id'=>'required|numeric|min:1',
                        'union_word_id'=>'required|numeric|min:1',
                        'geo_id'=>'sometimes|unique:operation.tbl_vdp_ansar_info,geo_id,'.$this->route()->parameters()['info']
                    ];
            }
        } else{
            switch ($this->method()){
                case 'POST':
                    return [
                        'ansar_name_bng'=>'required',
                        'father_name_bng'=>'required',
                        'designation'=>'required',
                        'date_of_birth'=>'required',
                        'mobile_no_self'=>'required|regex:/^(\+88)?[0-9]{11}$/|unique:operation.tbl_vdp_ansar_info,mobile_no_self',
                        'blood_group_id'=>'required',
                        'division_id'=>'required|numeric|min:1',
                        'unit_id'=>'required|numeric|min:1',
                        'thana_id'=>'required|numeric|min:1',
                        'union_word_text'=>'required',
                    ];
                case 'PATCH':
                    return [
                        'ansar_name_bng'=>'required',
                        'father_name_bng'=>'required',
                        'designation'=>'required',
                        'date_of_birth'=>'required',
                        'mobile_no_self'=>'required|regex:/^(\+88)?[0-9]{11}$/|unique:operation.tbl_vdp_ansar_info,mobile_no_self,'.$this->route()->parameters()['info'],
                        'blood_group_id'=>'required',
                        'division_id'=>'required|numeric|min:1',
                        'unit_id'=>'required|numeric|min:1',
                        'thana_id'=>'required|numeric|min:1',
                        'union_word_text'=>'required',
                    ];
            }
        }
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'division_id.min'=>'Invalid division',
            'unit_id.min'=>'Invalid unit',
            'thana_id.min'=>'Invalid thana',
            'union_id.min'=>'Invalid union',
        ];
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        throw new HttpResponseException(response()->json($errors,JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

    protected function getValidatorInstance()
    {
        $data = $this->all();
        if(isset($data['geo_id'])&&$data['geo_id']&&strlen($data['geo_id'])>5) $data['geo_id'] = substr($data['geo_id'],-5);
        $this->getInputSource()->replace($data);
        return parent::getValidatorInstance();
    }


}
