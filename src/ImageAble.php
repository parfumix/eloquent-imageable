<?php

namespace Eloquent\ImageAble;

interface ImageAble {

    /**
     * Upload images .
     *
     * @param array $images
     * @return mixed
     */
    public function upload(array $images = array());

    /**
     * Get all images by specific attributes .
     *
     * @param array $attributes
     * @param callable $callback
     * @return mixed
     */
    public function images(array $attributes = array(), \Closure $callback = null);

}