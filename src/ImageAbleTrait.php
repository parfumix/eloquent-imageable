<?php

namespace Eloquent\ImageAble;

trait ImageAbleTrait {

    /**
     * Upload images .
     *
     * @param array $images
     * @param null $path
     * @param array $filters
     * @return mixed
     */
    public function upload(array $images = array(), $path = null, array $filters = array()) {
        $imageProcessor = app('image-processor');

        $images = $imageProcessor->upload(
            $images, $path, $filters
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
     * Get all images by specific attributes .
     *
     * @param array $attributes
     * @param callable $callback
     * @return mixed
     */
    public function images(array $attributes = array()) {
        $sql = ($class = $this->getAttribute('imageAbleClass')) ? $class::hasMany($class) : $this->morphMany(Attachment::class, 'imageable');

        foreach ($attributes as $key => $attribute)
            $sql->where($key, $attributes);

        return $sql;
    }

}