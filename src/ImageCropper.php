<?php

namespace jarda256sparktech\NovaImageCropper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Http\Requests\NovaRequest;

class ImageCropper extends Image
{

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-image-cropper';

    public function __construct($name, $attribute = null, $disk = 'public')
    {
        parent::__construct($name, $attribute, $disk, function (Request $request, $model) {

            $decodedJson = json_decode($request->{$this->name});
            if ($decodedJson->modified) {
                if (!empty($decodedJson->binaryImg) && !empty($decodedJson->binaryCrop)
                    && !empty($decodedJson->cropBoxData)) {

                    if(!empty($decodedJson->imgSrc) && !empty($decodedJson->cropSrc)){
                        \Storage::disk($this->disk)->delete([$decodedJson->imgSrc, $decodedJson->cropSrc]);
                    }

                    $base64_image = $decodedJson->binaryImg;
                    $imageName = (string) Str::uuid().'.png';
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $data = base64_decode($data);
                        \Storage::disk($this->disk)->put($imageName, $data);
                    }
                    $cropName = '_'.$imageName;
                    $base64_image = $decodedJson->binaryCrop;
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $data = base64_decode($data);
                        Storage::disk($this->disk)->put($cropName, $data);
                    }
                    $toSave = [
                        'imgSrc' => $imageName, 'cropSrc' => $cropName, 'cropBoxData' => $decodedJson->cropBoxData,
                    ];

                    return[
                        'image' => json_encode($toSave),
                    ];

                } else {
                    \Storage::disk($this->disk)->delete([$decodedJson->imgSrc, $decodedJson->cropSrc]);

                    return[
                        'image' => null,
                    ];
                }

            }

        });
        $this->withMeta(['aspectRatio' => 1.5]);
        $this->thumbnail(function () {
            $parsedValue = json_decode($this->value, true);

            return $parsedValue ? \Storage::disk($this->disk)->url($parsedValue['cropSrc']) : null;
        })->preview(function () {
            $parsedValue = json_decode($this->value, true);

            return $parsedValue ? \Storage::disk($this->disk)->url($parsedValue['imgSrc']) : null;
        });
    }

    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        $result = call_user_func($this->storageCallback, $request, $model);
        if ($result === true) {
            return;
        }
        if (!is_array($result)) {
            return $model->{$attribute} = $result;
        }
        foreach ($result as $key => $value) {
            $model->{$key} = $value;
        }
        if ($this->isPrunable()) {
            return function () use ($model, $request) {
                call_user_func(
                    $this->deleteCallback,
                    $request,
                    $model,
                    $this->getStorageDisk(),
                    $this->getStoragePath()
                );
            };
        }
    }
}
