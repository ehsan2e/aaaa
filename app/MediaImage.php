<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;
use function NovaVoip\supervisedTransaction;

class MediaImage extends Model
{
    const MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
    ];
    const PUBLIC_MEDIA_IMAGE_DIRECTORY = 'image';

    protected $table = 'media_images';

    public function getUrl(int $width = null, int $height = null): string
    {
        return url('/media/' . self::PUBLIC_MEDIA_IMAGE_DIRECTORY . '/' . self::generateFileName(...array_merge([$this->name, $this->extension], func_get_args())));
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MediaImageVersion::class, 'media_image_id', 'id');
    }

    /**
     * @param UploadedFile $uploadedImage
     * @param null|string $name
     * @return MediaImage|null
     * @throws \Exception
     */
    public static function createFromUploadedFile(UploadedFile $uploadedImage, string $name = null): ?MediaImage
    {
        $instance = new self();
        /** @var InterventionImage $img */
        $img = Image::make($uploadedImage->getRealPath());
        $instance->name = $name ?? (Str::snake(explode('.', $uploadedImage->getClientOriginalName())[0]));
        $instance->width = $img->width();
        $instance->height = $img->height();
        $instance->extension = self::MIMES[$img->mime()];

        return supervisedTransaction(function () use ($instance, $uploadedImage): ?MediaImage {
            if (!$instance->save()) {
                return null;
            }

            $result = Storage::disk('public_media')->putFileAs(self::PUBLIC_MEDIA_IMAGE_DIRECTORY, $uploadedImage, self::generateFileName($instance->name, $instance->extension), 'public');
            return $result === false ? null : $instance;
        }, null);
    }

    public static function generateFileName(string $name, string $extension, int $width = null, int $height = null): string
    {
        switch (func_num_args()) {
            case 2:
                return sprintf('%s.%s', $name, $extension);
            case 4:
                return sprintf('%s.%dx%d.%s', $name, $width, $height, $extension);
            default:
                throw new \InvalidArgumentException('Invalid number of parameters. function should be called with either 2 or 4 parameters but it is called with ' . func_num_args() . ' parameter/s');
        }
    }
}
