<?php

namespace jarda256sparktech\NovaImageCropper;

use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Image;

class ImageCropper extends Image
{

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-image-cropper';

    public $disk;

    public function aspectRatio($ratio)
    {
        return $this->withMeta(['aspectRatio' => $ratio]);
    }

    public function __construct($name, $attribute = null, $disk = 'public', $storageCallback = null)
    {
        parent::__construct($name, $attribute);

        $this->disk = $disk;

        $this->thumbnail(function () {
            $parsedValue = json_decode($this->value, true);
            return $parsedValue ? Storage::disk($this->disk)->url($parsedValue['imageSrc']) : null;
        })->preview(function () {
            $parsedValue = json_decode($this->value, true);
            return $parsedValue ? Storage::disk($this->disk)->url($parsedValue['cropSrc']) : null;
        })->download(function ($request, $model) {
            $name = $this->originalNameColumn ? $model->{$this->originalNameColumn} : null;

            return Storage::disk($this->disk)->download($this->value, $name);
        })->delete(function () {
            if ($this->value) {
                Storage::disk($this->disk)->delete($this->value);

                return $this->columnsThatShouldBeDeleted();
            }
        });
    }
}
