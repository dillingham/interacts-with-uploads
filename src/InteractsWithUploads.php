<?php

namespace Dillingham;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait InteractsWithUploads
{
    /**
     * Upload and update validated with path.
     *
     * @param string $key
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function upload(string $key, $path = 'uploads', $disk = 'public'): ?string
    {
        if(Str::contains($key, '.')) {
            [$routeKey, $key] = explode('.', $key);
            $original = data_get($this->route($routeKey), $key);
            ray($original, $routeKey, $this->route(), $key);
            return $this->updateUpload($original, $key, $path, $disk);
        }

        if(!$this->hasFile($key)) {
            return null;
        }

        $name = $this->file($key)->store($path, $disk);

        if(Str::startsWith(
            config("filesystems.disks.$disk.url"),
            config('app.url')
        )) {
            $name = "/$name";
        }

        $this->addValidated($key, $name);

        return $name;
    }

    /**
     * Update the upload if present and remove soriginal.
     *
     * @param mixed $original
     * @param string $key
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function updateUpload($original, string $key, $path = 'uploads', $disk = 'public'): ?string
    {
        if(is_object($original)) {
            $original = data_get($original, $key);
        }

        if(is_null($original)) {
            return null;
        }

        if(! $this->hasFile($key)) {
            $this->addValidated($key, $original);

            return $original;
        }

        Storage::disk($disk)->delete($original);

        return $this->upload($key, $path, $disk);
    }

    /**
     * Add to the validated() data.
     *
     * @param $key
     * @param $value
     */
    function addValidated($key, $value)
    {
        $validator = $this->getValidatorInstance();

        $data = array_merge($validator->getData(), [$key => $value]);

        $validator->setData($data);
    }
}
