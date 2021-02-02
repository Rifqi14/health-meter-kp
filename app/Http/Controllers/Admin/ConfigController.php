<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'config'));
        $this->middleware('accessmenu', ['except' => 'select']);
    }

    public function index()
    {
        return view('admin.config.index');
    }
    public function update(Request $request)
    {
        $fields = [
            'app_name','app_copyright','app_logo','company_name','company_email','company_phone','company_address','company_latitude','company_longitude', 'bot_icon', 'bot_username'
        ];
        $validator = Validator::make($request->all(), [
            'app_name' 	=> 'required',
            'app_copyright' => 'required',
            'app_logo' => 'mimes:png',
            'company_name' => 'required',
            'company_email' => 'required|email',
            'company_phone' => 'required',
            'company_address' => 'required',
            'bot_icon'      => 'mimes:png',
            'bot_username'  => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        foreach($fields as $field){
            switch($field){
                case 'app_logo':
                    $app_logo = $request->file('app_logo');
                    if($app_logo){
                        if(file_exists('assets/config/logo.png')){
                            unlink('assets/config/logo.png');
                        }
                        $app_logo->move('assets/config/','logo.png');
                    }
                    $config = Config::where('option',$field)->first();
                    $config = Config::find($config->id);
                    $config->value = 'assets/config/logo.png';
                    $config->save();
                    break;
                case 'bot_icon':
                    $bot_icon = $request->file('bot_icon');
                    if ($bot_icon) {
                        if (file_exists('assets/config/bot_icon.png')) {
                            unlink('assets/config/bot_icon.png');
                        }
                        $bot_icon->move('assets/config/', 'bot_icon.png');
                    }
                    $config = Config::where('option', $field)->first();
                    if ($config) {
                        $config = Config::find($config->id);
                        $config->value = 'assets/config/bot_icon.png';
                        $config->save();
                    } else {
                        $config = Config::create([
                            'option'    => $field,
                            'value'     => 'assets/config/bot_icon.png'
                        ]);
                    }
                    break;
                default:
                $config = Config::where('option',$field)->first();
                if($config){
                    $config = Config::find($config->id);
                    $config->value = $request->{$field};
                    $config->save();
                }
                else{
                    $config = Config::create([
                        'option'=> $field,
                        'value' => $request->{$field}
                    ]);
                }
                break;
            }
            
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('config.index'),
        ], 200);
    }
}