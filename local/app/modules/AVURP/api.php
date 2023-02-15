<?php

Route::group(['prefix' => 'AVURP/api', 'namespace' => '\App\modules\AVURP\Controllers', 'middleware' => ['auth.api','checkUserType']], function () {
    Route::post('info/all', ['as' => 'AVURP.api.index', 'uses' => 'ApiController@index']);
    Route::get('info/show/{id}', ['as' => 'AVURP.api.show', 'uses' => 'ApiController@show']);
    Route::get('info/image/{id}', ['as' => 'AVURP.api.image', 'uses' => 'ApiController@image']);
    Route::get('info/edit/{id}', ['as' => 'AVURP.api.edit', 'uses' => 'ApiController@edit']);
    Route::post('info/store', ['as' => 'AVURP.api.store', 'uses' => 'ApiController@store']);
    Route::patch('info/update/{info}', ['as' => 'AVURP.api.update', 'uses' => 'ApiController@update']);
    Route::post('info/verify/{id}', ['as' => 'AVURP.info.verify', 'uses' => 'ApiController@verifyVDP']);
    Route::post('info/approve/{id}', ['as' => 'AVURP.info.approve', 'uses' => 'ApiController@approveVDP']);
    Route::post('info/verify_approve/{id}', ['as' => 'AVURP.info.verify_approve', 'uses' => 'ApiController@verifyAndApproveVDP']);

});