<?php


namespace App\Controllers\Example;

use App\Controllers\Controller;
use App\Exceptions\HttpMissingKeyException;
use App\Http\Request;
use App\Services\Example\ExampleService;
use App\Validators\Form;

class ExampleController extends Controller {

    public static function getService() : ExampleService {
        return self::get('App\Services\Example\ExampleService');       
    }

    public static function getAction(Request $request) : array {        
        return self::getService()->findAll();
    }

    public static function postAction(Request $request) : array {

        //Campos a serem validados.
        $requiredKeys = ['name', 'id_tenant'];

        if(!$request->validate($request, $requiredKeys)) {
            throw new HttpMissingKeyException(Form::getMessage(), 400);
        }

        return self::getService()->create($request);
    }

    public static function putAction(int $id, Request $request) {
        return self::getService()->update($id, $request);
    }

}