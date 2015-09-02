<?php

namespace Eloquent\ImageAble;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model {

    public $table = 'attachments';

    protected $fillable = ['imageable_id', 'imageable_type', 'title', 'path', 'full_path', 'extension'];
}
