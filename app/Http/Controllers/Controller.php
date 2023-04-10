<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Config;
use App\Models\User, App\Http\Models\Order;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\OrderSendDetails, App\Mail\OrderSendDetailsAdmin;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getAdminsEmails(){
    	return DB::table('users')->where('role', '1')->get();
    }
    
    public function entrarHttp(){
        return "hola";
    }

    public function getProcessOrder($id){
        $order = Order::find($id);
        $order->o_number = $this->getOrderNumbeGenerate();
        $order->status = '1';
        $order->request_at = date('Y-m-d H:i:s');
        $order->save();
    }

    public function getOrderNumbeGenerate(){
        $orders = Order::where('status', '>', '0')->count();
        $orderNumber = $orders + 1;
        return $orderNumber;
    }

    public function postFileUpload($field, $request, $thumbnails = null){
        $data = [];
        $path = date('Y/m/d');

        $files = $request->file('img');

        if(!File::isDirectory('uploads/'.$path)){
            File::makeDirectory('uploads/'.$path, 0775, true); //creates directory
        }

        if(count($files) > 0){
            foreach($files as $file){
                $original_name = $file->getClientOriginalName();
                $final_name = Str::slug($file->getClientOriginalName().'_'.time()).'.'.trim($file->getClientOriginalExtension());
            
                if($file->storeAs($path, $final_name, 'uploads')):
                    $data[] = json_encode(['upload' => 'success', 'path' => $path, 'original_name' => $original_name, 'final_name' => $final_name]);
                endif;
    
                if($thumbnails):
                    try {
                        $file_path = Config::get('filesystems.disks.uploads.root').'/'.$path.'/'.$final_name;
                        foreach($thumbnails as $thumbnail):
                            $img = Image::make($file_path)->orientate();
                            $img->fit($thumbnail[0], $thumbnail[1], function($constraint){
                                $constraint->aspectRatio();
                            });
                            $img->save(Config::get('filesystems.disks.uploads.root').'/'.$path.'/'.$thumbnail[2].'_'.$final_name, 75);
                        endforeach;
                    } catch (\Throwable $th) {
                        dd($th);
                    }
                endif;
            }
            return $data;
        }else{
            return ['upload' => 'error'];
        }

    }

    public function getFileDelete($disk, $file, $thumbnails = null){
        
        $end_file = json_decode($file, true);
        
        $file_path = Config::get('filesystems.disks.'.$disk.'.root').'/'.$end_file['path'].'/'.$end_file['final_name'];
        if(file_exists($file_path)):

            unlink($file_path);

            if($thumbnails != null){
                foreach($thumbnails as $thumbnail):
                    $thumbnail_path = Config::get('filesystems.disks.'.$disk.'.root').'/'.$end_file['path'].'/'.$thumbnail.'_'.$end_file['final_name'];
                    if(file_exists($thumbnail_path)):
                        unlink($thumbnail_path);
                    endif;
                endforeach;
            }

        endif;
    }
}
