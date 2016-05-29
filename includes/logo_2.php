<?php
function theme_logo_2(){
?>
<?php
    $logoAlt = get_option('blogname');
    $logoSrc = theme_get_option('theme_logo_url');
    $logoLink = theme_get_option('theme_logo_link');
?>

<a class=" bd-logo-2 hidden-sm hidden-xs" href="<?php
    if (!theme_is_empty_html($logoLink)) {
        echo $logoLink;
    } else {
        ?>http://okoest.dk<?php
    }
?>">
<img class=" bd-imagestyles"<?php
                if (!theme_is_empty_html($logoSrc)) {
                    echo ' src="' . $logoSrc . '"';
                } else {
                    ?>
 src="<?php echo theme_get_image_path('images/6396cee8731569f2d1a5500ba0672c47_okoest.png'); ?>"<?php
                } ?> alt="<?php echo $logoAlt ?>">
</a>
<?php
}