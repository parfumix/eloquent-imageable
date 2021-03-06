<?php

namespace Eloquent\ImageAble;

use Eloquent\Sortable\Sortable;
use Eloquent\Sortable\SortableTrait;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model implements Sortable {

    use SortableTrait;

    public $table = 'attachments';

    protected $fillable = ['is_main', 'position', 'imageable_id', 'imageable_type', 'title', 'path', 'full_path', 'extension'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function imageable() {
        return $this->morphTo();
    }

    /**
     * Get attachment presenter .
     *
     * @return AttachmentPresenter
     */
    public function present() {
        return new AttachmentPresenter($this);
    }

    /**
     * Check if image is main .
     *
     * @return mixed
     */
    public function isMain() {
        return $this->is_main;
    }

    /**
     * Set attachemnt as main .
     *
     */
    public function setMain() {
        $this->is_main = 1;
        $this->save();
    }
}
