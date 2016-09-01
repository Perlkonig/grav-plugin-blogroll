<?php
namespace Grav\Plugin;

use Grav\Common\GravTrait;

class Blogroll
{
    use GravTrait;

    /**
     * @var array
     */
    protected $blogroll;

    /**
     * Get taxonomy list.
     *
     * @return array
     */
    public function get($tags, $combinator, $orderby, $asc)
    {
        if (!$this->blogroll) {
            $this->build($tags, $combinator, $orderby, $asc);
        }
        return $this->blogroll;
    }

    /**
     * @internal
     */

    private static function cmp_link($a, $b) {
        return strcmp($a['link'], $b['link']);
    }

    private static function cmp_description($a, $b) {
        return strcmp($a['description'], $b['description']);
    }

    private static function cmp_name($a, $b) {
        return strcmp($a['name'], $b['name']);
    }

    private static function cmp_sortkey($a, $b) {
        return strcmp($a['sortkey'], $b['sortkey']);
    }

    protected function build($tags, $combinator, $orderby, $asc)
    {
        //Initialize the return value
        $links = [];

        //Get all the links from the config file
        $all_links = self::getGrav()['config']->get('plugins.blogroll.links');

        //Iterate over each provided tag and gather the links indexed by tag
        $links_by_tag = [];
        foreach ($tags as $tag) {
            $links_by_tag[$tag] = [];
            foreach ($all_links as $link) {
                if (in_array($tag, $link['tags'])) {
                    $links_by_tag[$tag][$link['link']] = $link;
                }
            }
        }

        //Apply the combinator
        if ($combinator === 'and') {
            $links = array_shift($links_by_tag);
            foreach ($links_by_tag as $key => $value) {
                $links = array_intersect_assoc($links, $value);
            }
        }
        //Anything other than 'and' is interpreted as 'or'
        else {
            foreach ($links_by_tag as $key => $value) {
                foreach ($value as $link) {
                    $links[$link['link']] = $link;
                }
            }
        }

        //Sort the links
        if ($orderby === 'link') {
            usort($links, array($this, 'cmp_link'));
            if(! $asc) {
                $links = array_reverse($links);
            }
        } elseif ($orderby === 'description') {
            usort($links, array($this, 'cmp_description'));
            if(! $asc) {
                $links = array_reverse($links);
            }
        } elseif ($orderby === 'name') {
            usort($links, array($this, 'cmp_name'));
            if(! $asc) {
                $links = array_reverse($links);
            }
        } elseif ($orderby === 'sortkey') {
            usort($links, array($this, 'cmp_sortkey'));
            if(! $asc) {
                $links = array_reverse($links);
            }
        }
        //Anything else is interpreted as 'asis', meaning no sort performed

        $this->blogroll = $links;
    }
}

