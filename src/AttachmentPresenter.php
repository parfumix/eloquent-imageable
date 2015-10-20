<?php

namespace Eloquent\ImageAble;

use Robbo\Presenter\Presenter;

class AttachmentPresenter extends Presenter  {

    /**
     * Render image .
     *
     * @param array $attributes
     * @return string
     */
    public function render(array $attributes = array()) {
        $attributes = array_merge($attributes, [
            'title' => $this->title
        ]);

        $html = '<img ';

        foreach ($attributes as $key => $value)
            $html .= $key . '="' . $value .'" ';

        $html .= 'src="' . $this->path . '">';

        return $html;
    }
}
