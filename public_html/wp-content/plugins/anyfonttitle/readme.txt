=== AnyFontTitle ===
Contributors: chang2034
Author URI: http://chang2034.wordpress.com
Plugin URI: http://chang2034.worpress.com 
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10350102
Tags: title, change, font, color, size
Requires at least: 1.5
Tested up to: 2.8
Stable tag: trunk

change font, size and color of the titles. 
Cambia el tipo de fuente, el tamaño y el color de los titulos de las entradas.

== Description ==

English:

This plugin change the text of your titles for an image dinamicaly created that have the same title but changed in size, type and color( base and typo).        


Spanish:

este plugin cambia el texto de los titulos por una imagen creada dinamicamente que continene el mismo texto pero cambiado en color (Tanto la base como la fuente), tamaño y en tipo de letra.






== Installation ==

English:

1. Upload folder `anyfonttitle` to the `/wp-content/plugins/` directory

2. Activate the plugin through the 'Plugins' menu in WordPress

3. Edit localy or through in your theme editor in control panel and Change the call to `the_title()` for `gesttit_converter()` in:

	* index.php (in your theme folder) for change the titles in the home page.
	* search.php for change the titles in search result.
	* single.php for change the title in entire entries.
	* archive.php for change the titles archives result.

4. in options. the submenu `management titles` is for change the values of the titles. You can upload fonts, change the size, color and alternate whith others font that you are upload earlier.

   in this submenu you can view your longest title for adjust the properly size.


Spanish:

1. Sube la carpeta `anyfonttitle` a tu directorio de plugins `/wp-content/plugins/`  

2. Activalo desde el panel de administraci&oacute;n de WP.

3. edita localmente o a traves de aspecto-->editor en tu panel de control y canbia la llamada a `the_title()` por `gesttit_converter()` en:

	* index.php (en la carpeta de tu tema activo) para cambiar los titulos en la pagina principal.
	* search.php para cambiar los titulos en la pagina de resultados.
	* single.php para las entradas individuales.
	* archive.php para los resultados de archivos.

4. en opciones. El submenu `management titles`es para cambiar los valores del plugin. Puedes subir fuentes, cambiar el tamaño, color y cambiar el tipo de letra a mostrar.

en el submenu puedes ver tu titulo mas largo para ajustar apropiadamante el tamaño de la fuente.

change log:

v0.6:
- Some bug fixed.
- best instructions.

v0.5:
- You can change alse the color of the base (if your blog color is diferent to white)

v0.4:
- various errors fixed. Thanks to the warning by JHMorales.

v0.3:
- Supports color change (256 color palette).

v0.2a:
- The first version.


== Frequently Asked Questions ==

only show one line.

if you have any simbol in your title, the plugin can no function properly.



no corta las palabras y ni las pone en un segundo renglon.

si tienes simbolos en el titulo, el plugin puede no funcionar apropiadamente.

== Screenshots ==

1. Plugin in action
2. Plugin options


