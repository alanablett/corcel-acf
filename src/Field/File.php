<?php

namespace Corcel\Acf\Field;

use App\PostTypes\As3cfItem;
use Corcel\Acf\FieldInterface;
use Corcel\Model\Post;

/**
 * Class File.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class File extends BasicField implements FieldInterface
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $filename;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $caption;

    /**
     * @var string
     */
    public $mime_type;

    /**
     * @var Post
     */
    public $attachment;

    /**
     * @param string $field
     */
    public function process($field)
    {
        $value = $this->fetchValue($field);

        $connection = $this->post->getConnectionName();

        if ($file = Post::on($connection)->find(intval($value))) {
            $this->fillFields($file);
        }
    }

    /**
     * @return File
     */
    public function get()
    {
        return $this;
    }

    /**
     * @param Post $file
     */
    protected function fillFields(Post $file)
    {
        $this->url = $file->guid;

        $s3item = As3cfItem::where('source_id', $file->ID)->first();
        if($s3item) {
            $this->url = "https://{$s3item->bucket}.s3.{$s3item->region}.amazonaws.com/{$s3item->path}";
        }

        $this->mime_type = $file->post_mime_type;
        $this->title = $file->post_title;
        $this->description = $file->post_content;
        $this->caption = $file->post_excerpt;
        $this->filename = basename($this->url);
        $this->attachment = $file;
    }
}
