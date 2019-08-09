<?php
namespace jarda256sparktech\NovaImageCropper;

use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
class FieldController extends BaseController
{
    /**
     * upload selected images
     *
     * @param Request $request
     * @return arrayt
     */
    public function upload(Request $request)
    {
        $disk = 'local';
        $path = '/';
        $images = $request->images;
        $data = array();
        foreach ($images as $image)
        {
            $savedImage = Storage::disk($disk)
                ->putFile($path, $image);
            $data[] = [
                'image' => $image->getClientOriginalName(),
//                'image_crop' => $image->getClientOriginalName(),
//                'image_crop_define' => $image->getClientOriginalName(),
                'url' => Storage::url($savedImage)
            ];
        }
        return $data;
    }
    public function delete($image)
    {
        Storage::delete($image);
        return "success";
    }
}
