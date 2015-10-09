<?php

namespace Eloquent\ImageAble;

use Eloquent\Sortable\Sortable;
use Eloquent\Sortable\SortableTrait;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model implements Sortable {

    use SortableTrait;

    public $table = 'attachments';

    protected $fillable = ['position', 'imageable_id', 'imageable_type', 'title', 'path', 'full_path', 'extension'];

    public function imageable() {
        return $this->morphTo();
    }

    /**
     * Check if image is main .
     *
     * @return mixed
     */
    public function isMain() {
        return $this->is_main;
    }
}
