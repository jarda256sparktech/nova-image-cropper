<?php

namespace jarda256sparktech\NovaImageCropper;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public $disk;

    public function __construct($name, $attribute = null, $disk = 'public')
    {
        parent::__construct($name, $attribute, $disk, function (Request $request, $model) {

            $decodedJson = json_decode($request->{$this->name});
            \Log::info('decoded');
            if ($decodedJson->modified) {
                \Log::info('is modified bI bC cBd', [!empty($decodedJson->binaryImg), !empty($decodedJson->binaryCrop),
                    !empty($decodedJson->cropBoxData)]);
                if (!empty($decodedJson->binaryImg) && !empty($decodedJson->binaryCrop)
                    && !empty($decodedJson->cropBoxData)) {

                    \Log::info('go save');
                    $base64_image = $decodedJson->binaryImg;
                    $imageName = $this->uuid().'.png';
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $data = base64_decode($data);
                        \Storage::disk('public')->put($imageName, $data);
                    }
                    $cropName = '_'.$imageName;
                    $base64_image = $decodedJson->binaryCrop;
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $data = base64_decode($data);
                        Storage::disk('public')->put($cropName, $data);
                    }
                    $toSave = [
                        'imgSrc' => $imageName, 'cropSrc' => $cropName, 'cropBoxData' => $decodedJson->cropBoxData,
                    ];
                    $model->update([
                        'image' => json_encode($toSave),
                    ]);

                }

            }

        });
        $this->withMeta(['aspectRatio' => 1.5]);
        $this->thumbnail(function () {
            $parsedValue = json_decode($this->value, true);

            return $parsedValue ? Storage::disk($this->disk)->url($parsedValue['imageSrc']) : null;
        })->preview(function () {
            $parsedValue = json_decode($this->value, true);

            return $parsedValue ? Storage::disk($this->disk)->url($parsedValue['cropSrc']) : null;
        });
//            ->delete(function () {
//            if ($this->value) {
//                Storage::disk($this->disk)->delete($this->value);
//                return $this->columnsThatShouldBeDeleted();
//            }
//        });
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
