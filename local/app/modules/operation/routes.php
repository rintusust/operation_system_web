<?php

use App\Helper\Facades\GlobalParameterFacades;
use App\Helper\GlobalParameter;
use App\Jobs\RearrangePanelPositionGlobal;
use App\Jobs\RearrangePanelPositionLocal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

if(version_compare(PHP_VERSION, '7.2.0', '>=')) {
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}


Route::group(['prefix' => 'operation', 'middleware' => 'manageDatabase', 'namespace' => '\App\modules\operation\Controllers'], function () {

});


Route::group(['prefix' => 'operation', 'middleware' => ['hrm']], function () {
    Route::group(['namespace' => '\App\modules\operation\Controllers', 'middleware' => ['auth', 'manageDatabase', 'checkUserType', 'permission']], function () {
        Route::get('/', ['as' => '', 'uses' => 'OperationController@opDashboard']);
        Route::get('image_import',['as'=>'rintu','uses'=>'AnsarVDPInfoController@importImage']);
      
        Route::get('/getalldesignation', ['as' => 'operation.vdpdesignationlist', 'uses' => 'AnsarVDPInfoController@getAllVdpDesignation']);

        //HRM

        Route::get('/template_list/{key}', ['as' => 'template_list', 'uses' => 'HrmController@getTemplate']);



        //GENERAL SETTING
        Route::get('/thana_form', ['as' => 'operation.thana_form', 'uses' => 'GeneralSettingsController@thanaIndex']);
        Route::get('/thana_view', ['as' => 'operation.thana_view', 'uses' => 'GeneralSettingsController@thanaView']);
        Route::get('/thana_view_details', ['as' => 'operation.thana_details', 'uses' => 'GeneralSettingsController@thanaViewDetails']);
        Route::post('/thana_entry', ['as' => 'operation.thana_entry', 'uses' => 'GeneralSettingsController@thanaEntry']);
        Route::get('/thana_edit/{id}', ['as' => 'operation.thana_edit', 'uses' => 'GeneralSettingsController@thanaEdit'])->where('id', '[0-9]+');
        Route::get('/thana_delete/{id}', ['as' => 'operation.thana_delete', 'uses' => 'GeneralSettingsController@thanaDelete']);
        Route::post('/thana_update', ['as' => 'operation.thana_update', 'uses' => 'GeneralSettingsController@updateThana']);


       Route::get('unit/all-units', ['as' => 'all-units', 'uses' => 'UnitController@allUnit']);
        Route::resource('unit', 'UnitController');
        Route::resource('range', 'DivisionController');
        Route::get('union/showall', ['as' => 'HRM.union.showall', 'uses' => 'UnionController@showAll']);
        Route::resource('union', 'UnionController');


        route::get('getBloodName', ['as' => 'blood_name', 'uses' => 'FormSubmitHandler@getBloodName']);

        //END GENERAL SETTING

        //REPORT
        Route::get('DistrictName', ['as' => 'district_name', 'uses' => 'FormSubmitHandler@DistrictName']);
        Route::get('DivisionName', ['as' => 'division_name', 'uses' => 'FormSubmitHandler@DivisionName']);
        Route::get('ThanaName', ['as' => 'thana_name', 'uses' => 'FormSubmitHandler@ThanaName']);

        Route::resource('info','AnsarVDPInfoController');
        Route::get('infos/import',['as'=>'operation.info.import','uses'=>'AnsarVDPInfoController@import']);
        Route::post('infos/import',['as'=>'operation.info.import_upload','uses'=>'AnsarVDPInfoController@processImportedFile']);
        Route::get('infos/import/download/{file_name}',['as'=>'operation.info.import.download','uses'=>'AnsarVDPInfoController@downloadFile']);

        Route::get('infos/image_import',['as'=>'operation.info.abcd','uses'=>'AnsarVDPInfoController@importImage']);
        Route::get('infos/image_import',['as'=>'operation.info.image_import_upload','uses'=>'AnsarVDPInfoController@processImportedImageFile']);


        Route::post('info/verify/{id}',['as'=>'operation.info.verify','uses'=>'AnsarVDPInfoController@verifyVDP']);
        Route::post('info/approve/{id}',['as'=>'operation.info.approve','uses'=>'AnsarVDPInfoController@approveVDP']);
        Route::post('info/verify_approve/{id}',['as'=>'operation.info.verify_approve','uses'=>'AnsarVDPInfoController@verifyAndApproveVDP']);
        Route::get('info/image/{id}',['as'=>'operation.info.image','uses'=>'AnsarVDPInfoController@loadImage']);
        Route::get('info/sign_image/{id}',['as'=>'operation.info.sign_image','uses'=>'AnsarVDPInfoController@loadSignImage']);


        // Services
        Route::get('print_card_id_view',['as'=>'operation.print_card_id_view','uses'=>'AnsarVDPInfoController@operationPrintIdCardView']);
        Route::post('/print_card_id', ['as' => 'operation.print_card_id', 'uses' => 'AnsarVDPInfoController@printIdCard']);
        Route::get('/id_card_history', ['as' => 'operation.id_card_history', 'uses' => 'AnsarVDPInfoController@getMemberIDHistory']);

        // Report
        Route::get('/print_id_list', ['as' => 'operation.print_id_list', 'uses' => 'AnsarVDPInfoController@printIdList']);
        Route::get('/get_print_id_list', ['as' => 'operation.get_print_id_list', 'uses' => 'AnsarVDPInfoController@getPrintIdList']);

        Route::post('/change_ansar_card_status', ['as' => 'operation.change_ansar_card_status', 'uses' => 'AnsarVDPInfoController@memberCardStatusChange']);

        Route::get('chunkverify', ['as' => 'operation.chunk_verify', 'uses' => 'FormSubmitHandler@chunkVerify']);
        Route::get('getnotverifiedansar', ['as' => 'operation.getnotverifiedansar', 'uses' => 'FormSubmitHandler@getNotVerifiedAnsar']);
        Route::get('/image_import', ['as' => 'operation.image_import', 'uses' => 'AnsarVDPInfoController@importImage']);
        Route::post('image_import_upload',['as'=>'operation.image_import_upload','uses'=>'AnsarVDPInfoController@processImportedImageFile']);



    });
});
