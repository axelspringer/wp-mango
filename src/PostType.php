<?php

namespace AxelSpringer\WP\Mango;

/**
 * Class PostType
 *
 */
abstract class PostType
{
    const Post          = 'post';
    const Page          = 'page';
    const Attachment    = 'attachment';
    const Revision      = 'revision';
    const NavigationMenuItem = 'nav_menu_item';
    const CustomCSS     = 'custom_css';
    const ChangeSets    = 'customize_changeset';
    const Any           = 'any';
}
