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

        $paths = $imageProcessor->upload(
            $images, $path, $filters
        );

        $attachments = [];
        array_walk($paths, function($path) use( & $attachments) {

            if( $class = $this->getAttribute('imageAbleClass') ) {

                $attachments[] = (new $class)->fill([
                    str_singular($this->getTable()) . '_id' => $this->id,
                    'title' => 'title',
                    'path' => $path,
                ])->save();

            } else {

                $attachments[] =Attachment::create([
                    'imageable_id' => $this->id,
                    'imageable_type' => $this->getMorhpClass(),
                    'title' => 'title',
                    'path' => $path,
                    'extension' => 'jpeg',
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
    public function images(array $attributes = array(), \Closure $callback = null) {
        if( $class = $this->getAttribute('imageAbleClass') )
            $sql = $class::where(str_singular($this->getTable()) . '_id' , $this->id);
         else
            $sql = $class::where('imageable_id' , $this->id)
                ->where('imageable_type', $this->getMorhpClass());

        if( !is_null($callback) )
            $sql = $callback($sql, $this);

        foreach ($attributes as $key => $attribute)
            $sql->where($key, $attributes);

        return $sql->get();
    }
}