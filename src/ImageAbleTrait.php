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
     * @throws PermissionException
     */
    public function upload(array $images = array(), $path = null, array $filters = array(), $placeholder = null, \Closure $closure = null) {
        $imageProcessor = app('image-processor');

        $behaviors = [];

        /**
         * When image is uploaded we have for the first to check if the user has custom configurations for uploading images.
         *  if there persist some image filters we have walk through that filters.
         *   additionally we can set custom store path for images or event set an placeholder for image name .
         */

        if( isset($this['behaviors']) )
            $behaviors = $this['behaviors'];

        if( in_array('behaviors', get_class_methods(get_class($this))) )
            $behaviors = $this->behaviors();

        /** @var Check for filters . $filters */
        if( isset($behaviors['filters']) )
            $filters = ! is_null($filters) ? $filters : $behaviors['filters'];

        /** @var Check for path . $path */
        if( isset($behaviors['path']) )
            $path = ! is_null($path) ? $path : public_path($behaviors['path']);

        /** @var Check for closure . $closure */
        if( isset($behaviors['closure']) && ( $behaviors['closure'] instanceof \Closure ) )
            $closure = ! is_null($closure) ? $closure : $behaviors['closure'];

        /** @var Check for custom placeholder . $placeholder */
        if( isset($behaviors['placeholder']) ) {
            $placeholder = ! is_null($placeholder) ? $placeholder : $behaviors['placeholder'];

            $availablePlaceholders = array_merge(
                $this->getAttributes(),
                ['date' => date('Y.m.d')],
                isset($behaviors['available']) ? $behaviors['available'] : []
            );

            foreach ($availablePlaceholders as $key => $value)
                $placeholder = str_replace('%'.$key.'%', $value, $placeholder);
        }


        /** Check for permissions if current user can upload images  */
        if( Support\isAllowed(isset($behaviors['roles']) ? $behaviors['roles'] : [], isset($behaviors['permissions']) ? $behaviors['permissions'] : []) ) {
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

        throw new PermissionException('Have no access for upload.');
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