<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\MediaImage;
use App\MediaImageVersion;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;
use NovaVoip\Traits\SearchablePaginate;

class MediaController extends Controller
{
    use SearchablePaginate;
    /**
     * @param string $name
     * @param int|null $width
     * @param int|null $height
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function compilePublicImage(string $name, ?int $width, ?int $height)
    {

        if (function_exists('debugbar')) {
            debugbar()->disable();
        }
        if (!Storage::disk('public_media')->exists(MediaImage::PUBLIC_MEDIA_IMAGE_DIRECTORY . '/' . $name)) {
            return response('', 404);
        }
        /** @var InterventionImage $img */
        $img = Image::make(Storage::disk('public_media')->path(MediaImage::PUBLIC_MEDIA_IMAGE_DIRECTORY . '/' . $name));
        $img->resize($width, $height, function (Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        /** @var MediaImage $mediaImage */
        $mediaImage = MediaImage::where('name', $img->filename)->where('extension', $img->extension)->first();
        if ($mediaImage) {
            $mediaImageVersion = new MediaImageVersion();
            $mediaImageVersion->width = $width;
            $mediaImageVersion->height = $height;
            $mediaImage->versions()->save($mediaImageVersion);
        }
        $img->save(Storage::disk('public_media')->path(MediaImage::PUBLIC_MEDIA_IMAGE_DIRECTORY . '/' . MediaImage::generateFileName($img->filename, $img->extension, $width, $height)));
        return $img->response($img->extension);
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function gallery(Request $request)
    {
        $queryBuilder = MediaImage::with(['versions']);

        return $this->paginate($request, $queryBuilder)
            ->view('dashboard.admin.gallery.index')
            ->setCollectionName('images')
            ->setSearchableFields(['name'])
            ->render();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function showUploadImageForm()
    {
        return view('dashboard.admin.gallery.upload-image');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'name' => ['required', 'alpha_dash'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,gif'],
        ]);


        /** @var UploadedFile $uploadedImage */
        $uploadedImage = $request->file('image');

        $request->validate([
            'name' => [
                Rule::unique('media_images')->where(function ($query) use ($uploadedImage) {
                    return $query->where('extension', MediaImage::MIMES[$uploadedImage->getMimeType()]);
                }),
            ],
        ]);

        $mediaImage = MediaImage::createFromUploadedFile($uploadedImage, $request->name);
        if (isset($mediaImage)) {
            flash()->success(__('Image :name added to gallery successfully', ['name' => $request->name]));
            return redirect()->route('dashboard.admin.gallery.index');
        }

        flash()->error(__('An error happened please try again later'));
        return back();
    }

    public function deleteImage()
    {
        flash()->error('Feature is not implemented!');
        return back();
    }
}
