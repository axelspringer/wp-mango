<?php

namespace Wp\Mango;

/**
 * Class MangoSettingsField
 */
class MangoSettingsField
{

    private $args;

    /**
     * MangoSettingsField constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $defaults = array(
            'id' => NULL,
            'title' => NULL,
            'page' => NULL,
            'section' => NULL,
            'description' => NULL,
            'type' => 'text', // text, textarea, password, checkbox
            'sanitize_callback' => NULL,
            'option_group' => NULL,
        );
        $this->args = wp_parse_args($args, $defaults);
        $this->register_field();
    }

    /**
     * register_field function.
     *
     * @access private
     * @return void
     */
    private function register_field()
    {
        add_settings_field(
            $this->args['id'],
            '<label for="' . $this->args['id'] . '">' . $this->args['title'] . '</label>',
            array($this, 'output_callback'),
            $this->args['page'],
            $this->args['section']
        );
        register_setting($this->args['option_group'], $this->args['id'], isset($this->args['sanatize_callback']) ? $this->args['sanatize_callback'] : NULL);
    }

    /**
     * output_callback function.
     *
     * @access public
     * @return void
     */
    public function output_callback()
    {
        $t = $this->args['type'];
        if ($t == "text"):
            ?>
            <fieldset>
                <input type="text" class="all-options" name="<?= $this->args['id'] ?>" id="<?= $this->args['id'] ?>"
                       value="<?= get_option($this->args['id']) ?>">
                <p class="description">
                    <?php echo $this->args['description']; ?>
                </p>
            </fieldset>
        <?php
        elseif ($t == "textarea"):
            ?>
            <fieldset>
                <textarea class="all-options" name="<?= $this->args['id'] ?>"
                          id="<?= $this->args['id'] ?>"><?= get_option($this->args['id']) ?></textarea>
                <p class="description">
                    <?php echo $this->args['description']; ?>
                </p>
            </fieldset>
        <?php
        elseif ($t == "password"):
            ?>
            <fieldset>
                <input type="password" class="all-options" name="<?= $this->args['id'] ?>" id="<?= $this->args['id'] ?>"
                       autocomplete="off" value="<?= get_option($this->args['id']) ?>">
                <p class="description">
                    <?php echo $this->args['description']; ?>
                </p>
            </fieldset>
        <?php
        elseif ($t == "checkbox"):
            ?>
            <fieldset>
                <label for="<?= $this->args['id'] ?>">
                    <input type="checkbox" class="" name="<?= $this->args['id'] ?>" id="<?= $this->args['id'] ?>"
                           autocomplete="off" value="1" <?php checked(get_option($this->args['id'])); ?>>
                    <?php echo $this->args['description']; ?>
                </label>
            </fieldset>
        <?php
        elseif ($t == "category"):
            ?>
            <fieldset>
                <?php
                $args = array(
                    "name" => $this->args['id'],
                    "id" => $this->args['id'],
                    "selected" => get_option($this->args['id']),
                    "show_option_none" => __("Not selected", 'afb'),
                );
                wp_dropdown_categories($args); ?>
                <p class="description">
                    <?php echo $this->args['description']; ?>
                </p>
            </fieldset>
        <?php
        elseif ($t == "callback"):
            call_user_func($this->args['callback'], $this->args);
        endif;
    }

}
