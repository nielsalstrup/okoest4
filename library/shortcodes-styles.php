<?php

class ThemeShortcodesStyles {

    public static function putStyleClassname($type, $style, $className, $mixinClass = '') {
        $type = strtolower($type);
        add_filter('theme_shortcodes_styles_' . strtolower($type) . '_' . $style, create_function('', "return array('$className', '$mixinClass');"));
    }
}

?>
<?php
ThemeShortcodesStyles::putStyleClassname('Blockquotes', "", "bd-blockquotes", "bd-blockquotes-1-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('Button', 'default', 'btn-default');
ThemeShortcodesStyles::putStyleClassname('Button', 'primary', 'btn-primary');
ThemeShortcodesStyles::putStyleClassname('Button', 'success', 'btn-success');
ThemeShortcodesStyles::putStyleClassname('Button', 'info', 'btn-info');
ThemeShortcodesStyles::putStyleClassname('Button', 'warning', 'btn-warning');
ThemeShortcodesStyles::putStyleClassname('Button', 'danger', 'btn-danger');
ThemeShortcodesStyles::putStyleClassname('Button', 'link', 'btn-link');
?>
<?php
ThemeShortcodesStyles::putStyleClassname('Image', 'rounded', 'img-rounded');
ThemeShortcodesStyles::putStyleClassname('Image', 'circle', 'img-circle');
ThemeShortcodesStyles::putStyleClassname('Image', 'thumbnail', 'img-thumbnail');
?>
<?php
ThemeShortcodesStyles::putStyleClassname('BulletList', "", "bd-bulletlist", "bd-bulletlist-1-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('Button', "", "bd-button", "bd-button-1-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('Image', "", "bd-imagestyles", "bd-imagestyles-1-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('inputs', "", "bd-bootstrapinput form-control input-sm", "bd-bootstrapinput-1-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('OrderedList', "", "bd-orderedlist", "bd-orderedlist-1-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('Table', "", "bd-table", "bd-table-1-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('Block', "", "bd-block", "bd-block-1-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('Carousel', "", "bd-carousel", "bd-carousel-12-mixin");
?>
<?php
ThemeShortcodesStyles::putStyleClassname('Indicators', "", "bd-indicators", "bd-indicators-17-mixin");
?>