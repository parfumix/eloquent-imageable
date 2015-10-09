<?php

namespace Eloquent\ImageAble;

use Flysap\Support;

trait ImageAbleTrait {

    /**
     * Upload images .
     *
     * @param array $images
     * @param null $path
     * @param array $filters
     * @param null $placeholder
     * @param callable $closure
     * @return mixed
     */
    public function upload(array $images = array(), $path = null, array $filters = array(), $placeholder = null, \Closure $closure = null) {
        $imageProcessor = app('image-processor');

        $images = $imageProcessor->upload(
            $images, $path, $filters, $placeholder, $closure
        );

        $attachments = [];
        array_walk($images, function($image) use( & $attachments) {

            if( $class = $this->getAttribute('imageAbleClass') ) {

                $attachments[] = (new $class)->fill([
                    str_singular($this->getTable()) . '_id' => $this->id,
                    'title' => $image->basename,
                    'full_path' => $image->basePath(),
                ])->save();

            } else {
                $attachments[] = Attachment::create([
                    'imageable_id' => $this->id,
                    'imageable_type' => $this->getMorphClass(),
                    'title' => $image->basename,
                    'path' => $image->relative_path,
                    'full_path' => $image->basePath(),
                    'extension' => $image->extension,
                ]);
            }
        });

        return $attachments;
    }

    /**
     * Delete image by id
     *
     * @param $imageIds
     */
    public function deleteImage($imageIds) {
        if(! is_array($imageIds))
            $imageIds = (array)$imageIds;

        $images = $this->images()
            ->whereIn('id', $imageIds);

        array_walk($images, function($image) {
            if( Support\is_path_exists($image->fullpath) )
                Support\remove_paths($image->fullpath);

            $image->delete();
        });
    }

    /**
     * Get all images by specific attributes .
     *
     * @param array $attributes
     * @return mixed
     * @internal param callable $callback
     */
    public function images(array $attributes = array()) {
        $sql = ($class = $this->getAttribute('imageAbleClass')) ? $class::hasMany($class) : $this->morphMany(Attachment::class, 'imageable');

        foreach ($attributes as $key => $attribute)
            $sql->where($key, $attributes);

        return $sql;
    }

    /**
     * Get image class .
     *
     * @return mixed
     */
    public function imageClass() {
        if(! $class = $this->getAttribute('imageAbleClass'))
            $class = Attachment::class;

        return $class;
    }
}