<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class BlogrollPlugin
 * @package Grav\Plugin
 */
class BlogrollPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0]
        ];
    }

    public function onTwigTemplatePaths()
    {
        //Load the built-in twig unless overridden
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onTwigSiteVariables()
    {
        //Load the custom PHP for generating the lists
        require_once __DIR__ . '/classes/blogroll.php';

        //Pass a reference to the custom object to the templates
        $this->grav['twig']->twig_vars['blogroll'] = new Blogroll();

        //Load the correct CSS based on the `built_in_css` field
        if ($this->config->get('plugins.blogroll.built_in_css')) {
            $this->grav['assets']
                ->add('plugin://blogroll/assets/blogroll.css');
        } else {
            $this->grav['assets']
                -> add('theme://assets/blogroll.css');
        }
    }
}
