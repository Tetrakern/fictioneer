# Documentation

Esta documentación trata sobre el tema Fictioneer. Si necesitas ayuda con WordPress en general, echa un vistazo a la [documentación oficial](https://wordpress.org/support/category/basic-usage/) o busca en Internet uno de los muchos tutoriales. Para la instalación, mira [aquí](INSTALLATION.md) primero y luego vuelve cuando hayas terminado.

Haga clic en el conmutador de esquema de la esquina superior derecha para ver el índice.

## Front Page

Es posible que desee configurar una página de inicio como el sitio de demostración o que sea una página de aterrizaje en caso de un sitio de un solo piso. Ambas se pueden lograr con bloques, shortcodes, y algo de CSS o HTML personalizado si es necesario. Obviamente, siempre puedes añadir una plantilla de página personalizada en tu tema hijo si tienes la habilidad para ello, que puede parecerse prácticamente a cualquier cosa.

En **Configuración > Lectura**, establece **Tu página de inicio muestra** en "Una página estática" y asigna tu **Página de inicio** y **Página de posts**. Crea nuevas páginas si aún no lo has hecho, dales nombres sensatos. Para su **Página de inicio**, elija la plantilla de página "Sin página de título" o "Página de historia".

La plantilla "Página de historia" es para sitios de una sola historia y tiene más opciones de shortcode, además de campos meta para el ID de la historia y el encabezado. Alternativamente, puede elegir la plantilla "Story Mirror", que reflejará la entrada de la historia del ID de historia especificado. Si desea tratar su página de inicio como una página única y no mostrar la entrada real de la historia, puede establecer una redirección en la historia a su dirección base (habilite los meta campos avanzados en la configuración).

Para simplificar, aquí está el contenido copiado de la [página de inicio de demostración](https://fictioneer-theme.com/) y [página de historia de demostración](https://fictioneer-theme.com/story-page/). Ponlo en la vista del editor de código y ajústalo como necesites (los IDs serán obviamente diferentes para ti). Cuando vuelvas al editor visual, todo debería estar correctamente formateado como bloques.

<detalles>
  <summary>Página de demostración</summary><br>

```html
<!-- wp:shortcode -->
[fictioneer_latest_posts count="1"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height": "24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:shortcode -->
[fictioneer_article_cards per_page="2" ignore_sticky="true"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Últimas noticias</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_stories count="10"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Últimas actualizaciones</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_updates count="6"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Últimos capítulos</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_chapter_cards count="6"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Últimas recomendaciones</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_recommendations count="6"]
<!-- /wp:shortcode -->

<!-- wp:heading {"className": "show-if-bookmarks hidden"} -->
<h2 class="wp-block-heading show-if-bookmarks hidden">Marcadores</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_bookmarks count="10"]
<!-- /wp:shortcode -->
```

</detalles>

<detalles>
  <summary>Página de historias demo</summary><br>

```html
<!-- wp:image {"id":24, "width": "300px", "sizeSlug": "large", "linkDestination": "none", "align": "right", "style":{"border":{"width": "0px", "style": "none"}}, "className": "is-style-default"} -->
<figure class="wp-block-image alignright size-large is-resized has-custom-border is-style-default"><img src="https://res.cloudinary.com/dmhr3ab5n/images/w_640,h_1024,c_scale/v1674220342/fictioneer-demo/katalepsis_cover/katalepsis_cover.jpg?_i=AA" alt="Portada Katalepsis" class="wp-image-24" style="border-style:none;border-width:0px;width:300px"/></figure>
<!-- /wp:image -->

<!-- wp:párrafo -->
<p>Las pesadillas y las alucinaciones han atormentado a Heather Morell toda su vida, reliquias de la esquizofrenia y del duelo infantil.</p> <p>La esquizofrenia es una enfermedad que afecta a todas las personas.
<!-- /wp:paragraph -->

<!-- wp:párrafo -->
<p>Hasta que conoce a Raine y a Evelyn, la autoproclamada guardaespaldas y la malhumorada maga, y descubre que no está loca. Los espíritus y monstruos que ve son demasiado reales, el dios de sus pesadillas le está enseñando a superar los límites humanos y su hermana gemela, que supuestamente nunca existió, podría seguir viva en algún lugar más allá de los muros de la realidad.
<!-- /wp:paragraph -->

<!-- wp:párrafo -->
<p>Heather se sumerge en un mundo de magia sobrenatural y fanáticos sectarios, tratando de mantenerse con vida, cuerda y lidiar con su propia atracción por mujeres peligrosas. Pero no todo es terror y peligro. A veces los monstruos llevan vestidos bonitos y se quedan a tomar el té. A veces descubres que tienes más en común con ellos de lo que crees. Tal vez esta sea la oportunidad de Heather para ser algo más que la cáscara derrotada en la que había crecido, para encontrar la verdadera amistad y el significado entre las cosas como ella - y tal vez, por ahí en el borde de lo posible, para traer a su hermana gemela de entre los muertos.</p> <p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className": "is-style-default"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-default"/>
<!-- /wp:separator -->

<!-- wp:párrafo -->
<p>Katalepsis es una palabra del griego antiguo que significa "comprensión", o quizás más exactamente, "perspicacia".<br><br>Katalepsis es una novela web por entregas sobre el horror cósmico y la fragilidad humana, fantasía urbana y romance lésbico, ambientada en una adormecida ciudad universitaria inglesa.</p> <p><strong>Próximos días</strong></strong></strong></strong></strong></strong></strong></strong>.
<!-- /wp:paragraph -->

<!-- wp:párrafo -->
<p>Actualmente, los nuevos capítulos se publican una vez a la semana, los sábados.</p> <p>Los nuevos capítulos se publican una vez a la semana, los sábados.
<!-- /wp:paragraph -->

<!-- wp:párrafo -->
<p>Si acabas de terminar el ebook o audiolibro oficial del Volumen I, la historia se reanuda&nbsp;<a href="https://katalepsis.net/2019/09/07/no-nook-of-english-ground-5-1/">aquí</a>.</p> <p>
<!-- /wp:paragraph -->

<!-- wp:párrafo -->
<p>Si te está gustando la historia y quieres ver más, por favor considera&nbsp;<a href="https://www.patreon.com/hazelyoung">donar a través de la página de Patreon!</a></p>
<!-- /wp:paragraph -->

<!-- wp:párrafo -->
<p>Cubierta por&nbsp;<a href="https://noctilia.artstation.com/">Noctilia</a>, cabecera por&nbsp;<a href="https://www.deviantart.com/yivels">Yivel</a>.</p> <p><a href="https://www.deviantart.com/yivels">Noctilia</a>.
<!-- /wp:paragraph -->

<!-- wp:párrafo -->
<p><em>Aviso legal: Tenga en cuenta que Katalepsis está dirigido a un público maduro. Al fin y al cabo, se trata de una historia de terror. Para más información, consulte las FAQ&nbsp;<a href="https://katalepsis.net/faq/">aquí</a>.</em></p> <p
<!-- /wp:paragraph -->

<!-- wp:shortcode -->
[fictioneer_story_actions story_id="13" follow="0" reminder="0"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height": "2rem"} -->
<div style="height:2rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Primer y último capítulo</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_latest_chapters count="2" post_ids="29,92" orderby="post__in" spoiler="true" vertical="true" seamless="true" aspect_ratio="4/1" type="simple" source="0" lightbox="0"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height": "1rem"} -->
<div style="height:1rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading -->
<h2 class="wp-block-heading">[fictioneer_story_data story_id="13" data="chapter_count"] Capítulos</h2>
<!-- /wp:heading -->

<!-- wp:shortcode -->
[fictioneer_chapter_list story_id="13" group="mind; correlating" heading="mind; correlating"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height": "1.5rem"} -->
<div style="height:1.5rem" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:shortcode -->
[fictioneer_chapter_list story_id="13" group="providence or atoms" heading="providence or atoms"]
<!-- /wp:shortcode -->

<!-- wp:html -->
<div style="margin-top: 1.5rem;"><a href="/katalepsis-tabla-de-contenidos/" class="button" style="width: 100%; background: var(--chapter-li-background, var(--content-li-background)); color: var(--fg-700); padding: 1.25rem; display: grid; place-content: center; border: none;">Más capítulos</a></div>
<!-- /wp:html -->

<!-- wp:shortcode -->
[fictioneer_story_comments story_id="13" header="true"]
<!-- /wp:shortcode -->
```

</detalles>

La página de la historia de demostración utiliza algunos [CSS de página personalizado](#extra-meta-campos) para cambiar el fondo de la página. También puedes hacerlo globalmente en **Apariencia > Personalizar**, pero esta es una forma de modificar páginas individuales.

```css
.main__background {
  color de fondo: var(--site-bg-color);
  box-shadow: ninguna;
}
```

**Códigos cortos de la página de historia
* [Story Actions](#story-actions-shortcode)
* [Story Section](#story-section-shortcode)
* [Comentarios de la historia](#story-comments-shortcode)
* [Story Data](#story-data-shortcode)

## Menus

El tema tiene dos ubicaciones de menú: Menú de navegación y Menú de pie de página. Puede crear y asignar menús en **Apariencia > Menú**; no existen por defecto. Para obtener instrucciones detalladas, consulte la [Guía del usuario del menú de WordPress Codex](https://codex.wordpress.org/WordPress_Menu_User_Guide). Tenga en cuenta que el menú móvil desplegará menús anidados, mientras que la barra de navegación móvil mostrará sólo los elementos de nivel superior, dependiendo del estilo móvil que haya elegido. El menú de pie de página no está configurado para elementos anidados. Demasiados elementos de nivel superior pueden quedar mal o romper el diseño.

**Clases CSS opcionales
* `not-in-mobile-menu` - Evita que los elementos del menú aparezcan en el menú móvil.
* `static-menu-item` - Hace que no se pueda hacer clic en el elemento del menú; bueno para cabeceras de submenú.
* `only-admins|-editors|-moderators|-authors` - Restringe el elemento de menú a ciertos roles.
* `hide-if-logged-in` - Oculta el elemento del menú si el usuario ha iniciado sesión.
* `hide-if-logged-out` - Oculta el elemento del menú si el usuario ha cerrado la sesión.

### Taxonomy Submenus

Además de la plantilla de página Taxonomías, también puede añadir un submenú para cada taxonomía en la navegación principal. Esto funciona para categorías, etiquetas, géneros, fandoms, personajes y advertencias. Para ello, añada un enlace personalizado como elemento de menú con `#` como enlace, luego asígnele **una** de las siguientes clases CSS desencadenantes (compruebe las opciones de pantalla si no puede ver la entrada). Esto debería funcionar en todos los niveles, pero se recomienda mantenerlo en el nivel superior. El enlace del menú y el submenú sólo serán visibles en los viewports de escritorio.

![Genres Submenu](repo/assets/genres_submenu.png?raw=true)

**Clases de menú (utilice una por elemento de menú):**
* `trigger-term-menu-categories` - Submenú para categorías.
* `trigger-term-menu-tags` - Submenú para etiquetas.
* `trigger-term-menu-genres` - Submenú para géneros.
* `trigger-term-menu-fandoms` - Submenú para fandoms.
* `trigger-term-menu-characters` - Submenú para caracteres.
* `trigger-term-menu-warnings` - Submenú para advertencias.

<detalles>
  <summary>Captura de pantalla de la interfaz del menú de administración</summary>

![Genres Submenu Setup](repo/assets/menu_custom_link_genres_submenu.png?raw=true)

</detalles>

<br>

**Clases CSS opcionales
* `columns-2|4|5` - Cambia el número de columnas a 2, 4 ó 5 (por defecto es 3).

**CSS personalizado opcional
Debido a su tamaño, el submenú de taxonomía puede causar problemas de diseño dependiendo de dónde se coloque el elemento padre. Hay demasiados casos para considerarlos individualmente, así que aquí tienes algo de CSS para que lo modifiques según necesites. Añada este CSS a la sección [Custom CSS](https://wordpress.org/documentation/article/customizer/#additional-css) en el Personalizador, y mantenga sólo las propiedades que realmente cambie.

<detalles>
  <summary>Mostrar definiciones CSS por defecto</summary>

```css
.nav-terms-submenu {
  --gap: 0px;
  --ancho: 500px;
  --columnas: 3;
  font-size: 15px;
  ancho: 1000px;
  max-width: min(calc(100vw - 20px), calc(var(--width) + var(--gap) * (var(--columns) - 1)));
  /*
  Cómo mover el menú horizontalmente:
  transformar: translateX(-25%);

  Alinear el menú a la derecha:
  derecha: 0;
  */
}

.nav-terms-submenu__note {
  text-transform: uppercase;
  font-family: var(--ff-note);
  font-size: 12px;
  font-weight: 600;
  padding: .75rem 1rem 0;
  opacidad: .5;
}
```

</detalles>

## Sidebar

Puede activar la barra lateral opcional en **Apariencia > Personalizar > Diseño**, eligiendo la alineación izquierda o derecha junto con otras opciones. Normalmente, esto también requiere algunos ajustes manuales en el diseño. Se recomienda aumentar el ancho del sitio para acomodar la nueva columna; 1036px es un buen comienzo para una barra lateral de 256px de ancho (dejando 700px de espacio para el contenido). Tenga en cuenta que la barra lateral sólo se mostrará cuando añada widgets.

Al activar la barra lateral también se reduce el relleno de página por defecto, que se puede anular más abajo con las propiedades de diseño personalizadas. Si el espacio se convierte en un problema, considera reducir el relleno horizontal de la página a cero y desactivar el fondo de la página para una apariencia de sitio abierto.

**Notas:**
* El widget de últimas entradas siempre oculta la miniatura, independientemente de la configuración del bloque.
* Utiliza la clase CSS opcional `no-theme-style` para eliminar el estilo del widget si es necesario.
* El estilo bajo **Apariencia > Widgets** NO es totalmente representativo del frontend.

## Stories

Las historias se añaden en **Historias > Añadir nueva**. Los campos obligatorios son la descripción breve, el estado y la clasificación por edades. Debes ser minucioso con la configuración, especialmente con las taxonomías si tienes más de unas pocas historias en tu sitio, porque pueden ser buscadas. Evita añadir una lista excesiva de etiquetas. Ten en cuenta también que las historias no deben usarse como capítulos, por ejemplo como oneshot, porque carecen de todas las características de los capítulos, incluidos los comentarios.

![Story Header](repo/assets/story_explanation_1.jpg?raw=true)

La maquetación se ajustará sola si se dejan vacíos ciertos campos, como la imagen de portada o las taxonomías. Si el título está vacío, se utilizarán en su lugar la fecha y la hora. Las imágenes de portada se muestran con una relación de aspecto de 2:3, aunque no es necesario que la imagen en sí siga estas dimensiones, ya que se recortará desde el centro.

![Story Chapter List](repo/assets/story_explanation_2.jpg?raw=true)

Los botones de compartir, feed y acción se muestran en función de la configuración del tema. La pestaña Blog muestra extractos de 160 caracteres de las últimas entradas asociadas a la historia por categoría. Se pueden añadir hasta cuatro páginas personalizadas como pestañas adicionales, con cualquier contenido, lo que requiere que tengan el campo de nombre corto. Los capítulos asignados a la historia pueden añadirse y ordenarse en el editor, pero los grupos de capítulos y los iconos se asignan en los capítulos.

**Suscribirse:** Abre un menú emergente con enlaces a cualquier campaña de apoyo (Patreon, Ko-fi y SubscribeStar), así como a los servicios de agregador RSS [Feedly](https://feedly.com/) e [Inoreader](https://www.inoreader.com/). No existe un sistema de suscripción por correo electrónico por defecto.

**Seguir y leer más tarde:** Estos botones pertenecen a las funciones opcionales de seguimiento y recordatorio, que permiten a los suscriptores registrados realizar un mejor seguimiento de las historias. Esto es principalmente para los sitios que albergan un gran número de historias.

![Story Cards](repo/assets/story_explanation_3.jpg?raw=true)

Las tarjetas de historias son pantallas de historias más compactas pensadas para la navegación, que se contraen aún más en ventanas pequeñas. En lugar del contenido, sólo se mostrará el *primer párrafo* de la breve descripción. Asegúrese de escribir algo atractivo, puede ser la única oportunidad que tenga su historia de llamar la atención. Las etiquetas normalmente no se muestran para ahorrar espacio, pero puedes cambiarlo en la configuración.

Las tarjetas de historias se utilizan en las Historias [plantilla de página](https://wordpress.org/support/article/pages/#page-templates), colecciones, búsqueda y listas destacadas en las entradas.

### Meta Fields

| Campo Tipo Explicación
| :-- | :-: | :--
| Descripción corta | Contenido | La descripción corta se utiliza en las tarjetas de la lista de historias.
| Capítulos | Lista | Añadir y ordenar los capítulos asignados a la historia. La asignación se realiza en los capítulos.
| Páginas personalizadas | Lista | Añadir hasta cuatro páginas como pestañas extra. Requiere que aparezca el campo de nombre corto.
| Subir Ebook | Archivo | Subir un archivo epub, mobi, ibooks, azw, azw3, kf8, kfx, pdf, iba, o txt.
| Prefacio ePUB | Contenido | Renuncias/etc. para ePUBs generados. Necesario para que aparezca el botón de descarga.
| Se añadirá después del último capítulo en los ePUBs generados.
| ePUB Personalizar CSS | Texto | Inyectar estilos personalizados en el ePUB generado. Para usuarios avanzados.
| Taxonomías (Varias) | Lista | Géneros, fandoms, personajes, advertencias, etiquetas y categorías (incluya el nombre de la historia).
| Imagen de portada | Imagen | Recortada a una relación de aspecto de 2:3 desde el centro.
| Elige entre en curso, completado, oneshot, hiatus y cancelado.
| Clasificación por edades | Seleccione | Elija entre todos, adolescente, maduro y adulto.
| Coautores (A) | Lista | Lista de coautores. Deben ser usuarios registrados, pero los dummies sirven.
| Aviso de derechos de autor | Cadena | Línea debajo del contenido para declarar los derechos de autor si es necesario.
| Enlace a Top Web Fiction | URL | Enlace a su historia en [Top Web Fiction](https://topwebfiction.com/).
| Pegar en listas | Marcar | Pegar la historia al principio de la primera página en listas.
| Ocultar historia en listas | Marcar | Ocultar la historia en listas; se puede seguir accediendo a ella con el enlace.
| Ocultar la imagen de portada en la página de la historia | Marcar | Ocultar la imagen de portada en la página pero no en las listas.
| Ocultar etiquetas en la página de la historia | Marcar | Ocultar *todas* las taxonomías excepto las advertencias en la página pero no en las listas.
| Ocultar iconos de capítulos | Marcar | Ocultar iconos de capítulos.
| Desactivar el colapso de capítulos | Comprobar | Desactivar el colapso de listas de capítulos largas (13+ como 5\|n\|5 por grupo).
| Desactivar grupos de capítulos | Marcar | Desactivar por completo los grupos de capítulos para la historia.
| Desactivar descarga ePUB | Comprobar | Desactivar descarga ePUB para la historia en todas partes.
| Personalizar CSS de la historia | Texto | Inyectar estilos personalizados para la historia y los capítulos. Para usuarios avanzados.
| Redirigir Enlace (A) | URL | Redirigir a una URL diferente cuando se accede al post. Asegúrese de saber lo que está haciendo.
| Enlaces de soporte (Varios) | URL | Enlaces a campañas de suscripción. Vuelve al perfil del autor si se deja en blanco.

<sup>**(A)** para Avanzado: Estos campos meta están ocultos a menos que marque la opción "Habilitar campos meta avanzados" en **Fictioneer > General > Compatibilidad.** La mayoría de los sitios simplemente no los necesitan.</sup>

### eBooks/ePUBs

Un eBook cargado manualmente siempre sustituirá a un ePUB generado automáticamente en el sitio, ya que se trata de una acción deliberada. Lo que también significa que usted mismo tiene que mantenerlo actualizado y que no hay estadísticas de descargas. Si quieres el ePUB generado, tienes que rellenar el contenido del Prefacio de la historia, que debe contener los derechos de autor y descargos de responsabilidad. Porque una vez que un archivo está en Internet, permanecerá en Internet. Asegúrese antes de que todo es legalmente correcto.

**Soportado:** Epubs sólo admite párrafos, encabezados, listas, tablas, citas en bloque, pullquotes, imágenes, espaciadores y HTML personalizado por tu cuenta y riesgo. Cualquier otra cosa será filtrada, como los vídeos.

**Contenido sensible:** Puede marcar el contenido sensible en los capítulos y proporcionar una alternativa, que los usuarios pueden elegir. Los ePUB generados siempre utilizan el contenido sensible (no censurado), no la alternativa si se proporciona.

#### Example Disclaimer for Originals:
> Esta es una obra de ficción. Los nombres, personajes, negocios, sucesos e incidentes son producto de la imaginación del autor. Cualquier parecido con personas reales, vivas o muertas, o sucesos reales es pura coincidencia.
>
> Copyright &#169; `AUTHOR`. Todos los derechos reservados.

#### Example Disclaimer for Fanfictions:
> Esta es una obra de fan fiction y no está escrita con ánimo de lucro. Los nombres, personajes, empresas, sucesos e incidentes son producto de la imaginación del autor. Cualquier parecido con personas reales, vivas o muertas, o sucesos reales es pura coincidencia. Todos los personajes y elementos utilizados pertenecen a sus respectivos propietarios, que no asumen responsabilidad alguna por esta obra.
>
> Contenido original Copyright &#169; `AUTHOR`. Todos los derechos reservados.

## Chapters

Los capítulos se añaden en **Capítulos > Añadir nuevo**. El único campo obligatorio es el icono del capítulo, que está preseleccionado por defecto (libro). Pero tienes que seleccionar una historia si quieres que el capítulo aparezca en la lista de capítulos de dicha historia. Esto no se limita a tus propias historias, así que puedes publicar capítulos de invitados para otros, aunque los propietarios tienen que incluirlos en la lista. Al igual que con las historias, debes ser minucioso con la configuración.

![Chapter List Item](repo/assets/chapter_explanation_1.jpg?raw=true)

La visualización de un capítulo listado en la página de una historia se controla desde dentro del capítulo. El icono, la advertencia y el grupo de capítulos se asignan aquí, aunque los iconos pueden desactivarse globalmente o por historia. Asegúrese de deletrear correctamente el grupo de capítulos cada vez, ya que no hay ayuda manual y diferentes nombres dan lugar a diferentes grupos. Los grupos también pueden hacer que los capítulos se reordenen si no están en secuencia, pero el orden dentro de un grupo sigue derivándose de la lista de capítulos de la historia. Se necesitan al menos dos grupos para que se muestren los grupos y los capítulos no agrupados se recogerán en "Sin asignar".

**Marcas de verificación:** Estos iconos pertenecen a la función opcional Marcas de verificación, que permite a los suscriptores registrados marcar capítulos e historias como leídos. Esto es principalmente para los sitios que albergan un gran número de historias.

![Chapter Screen](repo/assets/chapter_explanation_2.jpg?raw=true)

El conmutador de pantalla completa no está disponible en iOS, que en el momento de escribir este artículo no es compatible con la API de pantalla completa. Los botones de navegación se derivan de la lista de capítulos de la historia. Puedes abrir las herramientas de párrafo pulsando sobre un párrafo; antes debes activar en los ajustes los Marcadores, Sugerencias y Texto a voz (TTS). Los marcadores son por capítulo y están vinculados a un párrafo, el color es sólo un truco y _no_ indica que tienes más de uno.

**Modal de formato:** Se abre con el botón Formato. Permite a los lectores personalizar la forma en que se muestran los capítulos, incluyendo: brillo del sitio, saturación del sitio, anchura del sitio, tamaño de fuente, espaciado entre letras, altura de línea, espaciado entre párrafos, saturación de fuente, familia de fuente, color de fuente y peso de fuente, así como alternancias para sangría de texto, justificación de texto, modo claro/oscuro, herramientas de párrafo, notas de autor, comentarios y contenido sensible.

![Sensitive Content Warning](repo/assets/sensitive_content_warning.jpg?raw=true)

Este aviso aparece encima del título si añade una advertencia de capítulo, que no debe confundirse con la taxonomía de advertencia de contenido. La advertencia también aparece en la lista de capítulos de la historia. Sea breve, no hay mucho espacio. También puede cambiar el color y añadir una explicación adicional. El conmutador permite ocultar cualquier contenido sensible marcado con la clase CSS `sensitive-content` y mostrar una alternativa marcada con `sensitive-alternative` si se proporciona.

### Meta Fields

| Campo Tipo Explicación
| :-- | :-: | :--
| Historia | Seleccionar | La historia a la que pertenece el capítulo. Obligatorio si quiere que aparezca en la lista.
| Título de tarjeta/lista | Cadena | Título alternativo adecuado para tarjetas y listas con poco espacio.
| Grupo | Cadena | Asignación de grupo de capítulos. Tenga en cuenta la ortografía y el orden de los capítulos.
| Prólogo | Contenido | Prólogo renderizado encima del título del capítulo.
| Epílogo | Contenido | Epílogo renderizado debajo del contenido del capítulo.
| Nota de contraseña | Contenido | Nota opcional si hay un requisito de contraseña.
| Taxonomías (Varias) | Lista | Géneros, fandoms, personajes, advertencias, etiquetas y categorías (incluya el nombre de la historia).
| Imagen de portada del capítulo | Imagen | Recortada a una relación de aspecto de 2:3 desde el centro. Por defecto es la portada de la historia.
| Extracto | Texto | Extracto del capítulo utilizado en las tarjetas. Si está vacío, se utilizará parte del contenido.
| Icono | Cadena | Gratis [Font Awesome](https://fontawesome.com/search) cadena de clase. Por defecto es `fa-solid fa-book`.
| Icono de texto (A) | Cadena | Sustituye el icono por una cadena de texto, buena para combinar con fuentes de símbolos.
| Título corto (A) | Cadena | Título corto opcional del capítulo, no utilizado por defecto (pensado para temas hijo).
| Prefijo (A) | Cadena | Se antepone al título en las listas de capítulos. No se utiliza en los ePUB generados.
| Coautores (A) | Lista | Lista de coautores. Deben ser usuarios registrados, pero los dummies sirven.
| Clasificación por edades | Seleccione | Elija entre todos, adolescente, maduro y adulto.
| Advertencia | Cadena | Advertencia _breve_ que se muestra en las listas de capítulos y encima del título del capítulo.
| Notas de advertencia | Texto | Notas de advertencia adicionales sobre el título del capítulo.
| Ocultar el capítulo en todas las listas, pero mantenerlo accesible con el enlace.
| No contar como capítulo | Marcar | Excluir el capítulo del recuento de capítulos.
| Ocultar el título en el capítulo | Marcar | Ocultar el título y el autor en las páginas de los capítulos.
| Ocultar enlaces de soporte | Marca | Ocultar enlaces de soporte al final del capítulo.

<sup>**(A)** para Avanzado: Estos campos meta están ocultos a menos que marque la opción "Habilitar campos meta avanzados" en **Fictioneer > General > Compatibilidad.** La mayoría de los sitios simplemente no los necesitan.</sup>

### Chapter Titles

Como puede deducir de los campos meta, hay varios títulos de capítulos opcionales y campos relacionados con el título. Esto puede resultar confuso, por lo que a continuación le indicamos dónde y cómo se utilizan realmente estos campos. Obviamente, los campos en blanco no se muestran.

* **Tarjetas pequeñas (Shortcodes):** Título de la lista *o* Título
* **Tarjetas de capítulos grandes (plantillas de listas):** Título *y* Título de la lista (en móvil)
* **Tarjetas de cuentos grandes (plantillas de listas):** Título de la lista *o* Título
* **Índice de capítulos (emergente/móvil):** Título de la lista *o* Título
* **Listas de capítulos (Historia/Código corto):** Prefijo + Título

### Text-To-Speech Engine

Debe habilitarse en la configuración y se inicia desde las herramientas de párrafo. Utiliza la [Web Speech API] gratuita (https://developer.mozilla.org/en-US/docs/Web/API/Web_Speech_API) que soportan todos los navegadores modernos, que puede ser un poco confusa a veces, pero produce resultados sorprendentemente decentes. Principalmente pensada como función de accesibilidad para personas con problemas de lectura. No es en absoluto a prueba de fallos y depende del navegador y del sistema operativo; pueden ser necesarios permisos adicionales en el dispositivo de reproducción (esto está fuera de su control).

**Soportado:** Sólo se leen los hijos de primer nivel del contenedor de contenido, y sólo los párrafos y encabezados. Si quieres que se lean tablas, comillas y más, añade la salida deseada como párrafo con la clase CSS `hidden`.

**Nota:** Los navegadores sólo tienen una instancia de este motor. Eso significa que si tienes otro ejecutándose en una pestaña diferente, quizás en un sitio diferente, interferirán entre ellos. Incluso puede controlar la salida de otros sitios.

![Text-To-Speech Interface](repo/assets/tts.jpg?raw=true)

## Collections

Las colecciones se añaden en **Colecciones > Añadir nueva**. Los campos obligatorios son la descripción breve y los artículos que aparecen en la colección, que pueden incluir entradas, páginas, historias, capítulos, recomendaciones e incluso otras colecciones. El propósito es agrupar diferentes artículos con un contexto común, como secuelas o historias ambientadas en un universo compartido.

![Collection Screen](repo/assets/collection_explanation.jpg?raw=true)

### Meta Fields

| Campo Tipo Explicación
| :-- | :-: | :--
| Título de tarjeta/lista | Cadena | Título alternativo adecuado para tarjetas y listas con poco espacio.
| Añadir y ordenar entradas, páginas, historias, capítulos, recomendaciones y colecciones.
| Descripción breve | Contenido | La descripción breve se utiliza en las tarjetas de la lista de colecciones.
| Taxonomías (Varias) | Lista | Géneros, fandoms, personajes, advertencias, etiquetas y categorías (incluya el nombre de la historia).
| Imagen de portada de la colección | Imagen | Recortada a una relación de aspecto de 2:3 desde el centro.

## Recommendations

Las recomendaciones se añaden en **Recomendaciones > Añadir nuevo**. Los campos obligatorios son el autor de la historia recomendada, la URL principal, las URL generales y la abreviatura de "una frase" como descripción en las tarjetas pequeñas. Las tarjetas grandes utilizan el extracto normal. Las recomendaciones están pensadas para ser promociones personales de grandes historias de otros autores y para sacar a la luz joyas ocultas.

### Meta Fields

| Campo Tipo Explicación
| :-- | :-: | :--
| Una frase | Cadena | 150 caracteres o menos "elevator pitch" para describir la historia.
| Autor | Cadena | El autor de la historia recomendada.
| URL principal | Cadena | Enlace principal al sitio web de la recomendación o del autor.
| URLs | Texto | Lista con formato especial de enlaces a la recomendación, uno por línea.
| Apoyo | Texto | Lista con formato especial de enlaces de apoyo al autor, uno por línea.
| Taxonomías (Varias) | Lista | Géneros, fandoms, personajes, advertencias, etiquetas y categorías.
| Recomendación Imagen de Portada | Imagen | Recortada a una relación de aspecto de 2:3 desde el centro.

### Example Sentences

Piensa en la frase como en un discurso de ascensor, algo que puedas contar en pocos segundos para transmitir lo esencial. Omita los detalles, insinúe la trama, describa el concepto: la historia tiene todo el tiempo para contarse a sí misma más tarde. Porque la mayoría de las veces, los lectores sólo echan un vistazo a una historia mientras la hojean. Al fin y al cabo, las recomendaciones no ocupan un lugar destacado en _su_ sitio.

> Una heredera rebelde y su amiga genio cometen atracos de alta tecnología en una ciudad condenada al borde del mañana.

> ¡Una colegiala se reencarna en un mundo de fantasía, pero no como heroína sino como monstruo con tentáculos!

> Una estudiante embrujada descubre que sus pesadillas sobre dioses y horrores de más allá de la realidad no son alucinaciones después de todo.

> Una chica de los barrios bajos descubre su talento para la nigromancia y aprende a aceptar ser un terror existencial.

> Dos mujeres forjan un vínculo improbable y exploran una sencilla cuestión: ¿es lo mismo vender tu cuerpo que venderte a ti misma?

> Chicas reanimadas de distintas épocas vagan por las ruinas de la civilización en el cadáver cicatrizado de la Tierra.

> Chica mágica moribunda come los corazones de pesadillas vivas para engañar a la muerte.

## Pages

Las páginas funcionan igual que siempre en WordPress, sólo que con algunos campos adicionales y opciones de plantilla. [Cambia la plantilla](https://wordpress.org/support/article/pages/#page-templates) en la barra lateral de configuración. Puedes asignar estas páginas de plantilla a determinadas tareas en **Fictioneer > General > Asignación de páginas**.

### Page Templates

* **Capítulos:** Muestra una lista de todos los capítulos visibles ordenados por fecha de publicación, de forma descendente.
* **Historias:** Muestra una lista de todas las historias visibles ordenadas por fecha de publicación, de forma descendente.
* **Colecciones:** Muestra una lista de todas las colecciones visibles ordenadas por fecha de publicación, de forma descendente.
* **Recomendaciones:** Muestra una lista de todas las recomendaciones visibles ordenadas por fecha de publicación, de forma descendente.
* **Marcadores:** Muestra marcadores sin necesidad de un shortcode. Compatible con caché.
* **Librería:** Muestra listas paginadas de los Seguimientos, Recordatorios e historias terminadas de un usuario.
* **Bookshelf AJAX:** Versión de Bookshelf compatible con caché, que obtiene el contenido después de que la página se haya cargado.
**Página sin título:** Plantilla de página predeterminada pero sin el título. Bueno para una portada.
* **Story Mirror:** Renderiza la página exactamente como una historia (establecida a través del campo meta).
* **Página de historia:** Plantilla de portada para sitios de una sola historia, que permite el uso de todos los shortcodes `[fictioneer_story_*]`.
* **Índice de autores:** Muestra un índice de todos los autores ordenados por la primera letra del nombre mostrado.
**Índice de autores (avanzado)** Igual que la plantilla de página Índice de autores, pero con metadatos adicionales.
* **Índice:** Muestra un índice de todas las historias ordenadas por la primera letra del título.
* **Index (Avanzado):** Igual que la plantilla de página Índice, pero con metadatos adicionales.
* **Taxonomías:** Muestra detalles sobre todas las taxonomías utilizadas en el sitio, con recuento y definición (si se proporciona).
* **Perfil de usuario:** Perfil de cuenta frontend para mantener a los usuarios fuera del admin. ¡Nunca debe ser almacenado en caché!
* **Canvas (Page):** Renderiza el contenedor de la página en blanco sin comentarios. Destinado a ser utilizado con plugins constructor de página.
* **Canvas (Main):** Renderiza el contenedor principal sin página ni relleno. Destinado a ser utilizado con plugins constructor de página.
* **Canvas (Site):** Renderiza un sitio completamente en blanco. Destinado a ser utilizado con plugins constructor de página.

### Meta Fields

| Campo Tipo Explicación
| :-- | :-: | :--
| Nombre abreviado | Cadena | Nombre abreviado de la página necesario para las pestañas personalizadas en las historias.
| Identificador de filtro y búsqueda | Cadena | Identificador personalizado para usar con el plugin. No hace nada por sí mismo.
| ID de historia | Cadena | ID de una entrada de historia. Sólo utilizado por las plantillas de página "Story Page/Mirror".
| Mostrar cabecera de historia | Marcar | Muestra la cabecera de la historia para el ID de historia dado. Sólo para la plantilla "Página de historia".

### Customize Stories Template Query

Es posible que desee listar sólo las historias seleccionadas, por ejemplo, las que pertenecen a una determinada categoría. Aunque no existe un meta campo adecuado para ello debido a los numerosos parámetros posibles, puede personalizar la salida utilizando el filtro [fictioneer_filter_stories_query_args](FILTERS.md#apply_filters-fictioneer_filter_stories_query_args-query_args-post_id-) en un tema hijo. Asegúrese de que el nombre de su función de filtro es único, o de lo contrario.

```php
/**
 * Modifica la consulta del modelo Stories
 *
 * @desde x.x.x
 * @link https://developer.wordpress.org/reference/classes/wp_query/
 *
 * @param array $query_args Argumentos de la consulta.
 * @param int $post_id ID del post de la página.
 *
 * @return array Argumentos de consulta modificados
 */

function child_query_stories_by_category( $query_args, $post_id ) {
  // Utiliza el ID del post que se encuentra en la URL del editor para apuntar a páginas específicas
  if ( $post_id == 35 ) {
    // Añada el parámetro de consulta que desee
    $query_args['category_name'] = 'some-category-slug';
  }

  // Opcional: Añadir condiciones para otros IDs de post
  // if ( $post_id == 40 ) {
  // $query_args['tag'] = 'stuff';
  // }

  // Continuar filtro
  return $cargas_consulta;
}
add_filter( 'fictioneer_filter_stories_query_args', 'child_query_stories_by_category', 10, 2 );
```

## Shared Options

Estos campos y opciones están disponibles en la mayoría de los tipos de post, lo que no significa que tengan sentido en todas partes. Algunos requieren que ciertas características estén habilitadas y configuradas, como la integración de Patreon.

### Extra Meta Fields

| Campo Tipo Explicación
| :-- | :-: | :--
| Imagen apaisada | Imagen | Imagen alternativa para cuando la anchura renderizada es mayor que la altura.
| Imagen de cabecera | Imagen | Anula la imagen de cabecera por defecto, pasada a por capítulos en el caso de las historias.
| Personalizar CSS de página | Texto | Inyectar estilos personalizados en la página (no pasados a capítulos).
| Niveles de Patreon | Lista | Niveles de Patreon que ignoran la protección por contraseña (si está configurada).
| Patreon Amount Cents | Number | Patreon pledge threshold para ignorar la protección por contraseña (si está configurada).
| Elija una fecha y hora para eliminar automáticamente la contraseña de la entrada (configure su zona horaria).
| Desactivar nuevos comentarios | Marcar | Desactivar nuevos comentarios pero mantener visibles los actuales.
| Desactivar barra lateral | Marcar | Desactivar la barra lateral en esta entrada o página (si existe).

### SEO & Meta Tags

Metadatos para los resultados de los motores de búsqueda, gráficos de esquemas e incrustaciones en redes sociales. Si se deja en blanco, los valores predeterminados se derivarán del contenido de la entrada. Puede utilizar `{{title}}`, `{{site}}` y `{{excerpt}}` como marcadores de posición. Los títulos no deben superar los 70 caracteres, pero esto no es obligatorio. La imagen de Open Graph se configura manualmente (haciendo clic en la casilla) o por defecto es la miniatura de la entrada, la miniatura principal o el sitio por defecto en ese orden. El que estos servicios muestren realmente los datos ofrecidos depende totalmente de ellos. Después de todo, podrías escribir cualquier cosa ahí.

![SEO Appearance](repo/assets/seo_appearance.jpg?raw=true)

### Support Links

Una colección de enlaces de apoyo opcionales: Patreon, Ko-fi, SubscribeStar, PayPal, y un enlace genérico de donación para cualquier otra cosa. Se muestran en varios lugares, como debajo de cada capítulo, a menos que estén desactivados. Puedes establecer diferentes enlaces por capítulo e historia, por defecto el padre o el perfil del autor si se deja vacío.

## Additional CSS Classes

Puedes añadir clases CSS adicionales a los párrafos y otros bloques para obtener estilos y funciones adicionales. Sólo tienes que seleccionar un bloque en el editor y desplazarte hasta la sección **Avanzado** del panel [configuración de bloques](https://wordpress.org/support/article/working-with-blocks/#block-settings). Pueden ser clases propias o clases proporcionadas por el tema, que aparecen resaltadas en el editor como se muestra en la imagen.

También puede aplicar clases adicionales a palabras o frases sueltas. Cambia al editor de código en el menú de opciones (los tres puntos en la esquina superior derecha) y envuelve la parte deseada como `<span class="spoiler">palabra</span>`. Asegúrese de cerrar correctamente la etiqueta y no abarque varios bloques a menos que sepa lo que está haciendo, en cuyo caso no necesitaría esta guía.

![Additional CSS Classes](repo/assets/additional_css_classes_1.jpg?raw=true)

| Clase Efecto
| :-- | :--
| `sensitive-content` | Oculta un bloque si está activa la opción de formato de capítulos **Ocultar contenido sensible**.
| `sensitive-alternative` | Muestra un bloque si está activada la opción de formato de capítulo **Ocultar contenido sensible**.
| `spoiler` | Deja en blanco un bloque (o lapso) hasta que se haga clic para revelarlo.
| `hidden` | Oculta un bloque. Útil para la conversión de texto a voz si hay una imagen u otro elemento no legible.
| Oculta un bloque dentro de un ePUB. Combínalo con `inside-epub` para tener dos variantes.
| `inside-epub` | Oculta un bloque fuera de los ePUBs. Combínalo con `outside-epub` para tener dos variantes.
| `skip-tts` | Los bloques con esta clase serán ignorados por el motor de conversión de texto a voz. No funciona con espacios.
| Evita que las herramientas de párrafo se muevan. Puede usarse en el párrafo o en un span dentro de él.
| `show-if-bookmarks` | Debe utilizarse junto con `hidden`, que se elimina si las tarjetas de favoritos están presentes (a través de shortcode).
| `no-indent` | Suprime la sangría del texto independientemente de la configuración.
| `list` | Aplica estilos de lista si falta.
| `link` | Aplica estilos de enlace si faltan.
| `esc-link` | Impide que se apliquen los estilos de enlace.
| Evita que los espacios en blanco pasen a la línea siguiente.
| Fuerza a los bloques a ser tan anchos como el espacio lo permita. Funciona bien con tablas.
| `min-480` | Fuerza a los bloques a tener al menos 480px de ancho sin importar el espacio. Funciona bien con tablas.
| `min-640` | Obliga a los bloques a tener al menos 640px de ancho independientemente del espacio. Funciona bien con tablas.
| `min-768` | Obliga a los bloques a tener al menos 768px de ancho sin importar el espacio. Funciona bien con tablas.
| `only-admins` | Hace que el elemento sólo sea visible para los administradores.
| `only-editors` | Hace que el elemento sólo sea visible para editores o superiores.
| `only-moderators` | Hace que el elemento sólo sea visible para moderadores o superiores.
| `only-authors` | Hace que el elemento sólo sea visible para autores o superiores.
| `overflow-x` | Añade desplazamiento horizontal si un bloque es demasiado ancho. No es necesario en tablas.
| `no-auto-lightbox` | Evita que se aplique el script lightbox si se añade a un elemento `<img>`.
| `hide-below-desktop` | Oculta el elemento por debajo de anchos de ventana inferiores a 1024px.
| `hide-below-tablet` | Oculta el elemento por debajo de anchos de ventana inferiores a 768px.
| `hide-below-640` | Oculta el elemento por debajo de anchos de ventana inferiores a 640px.
| `hide-below-480` | Oculta el elemento por debajo de anchos de ventana inferiores a 480px.
| `hide-below-400` | Oculta el elemento por debajo de anchos de ventana inferiores a 400px.
| `hide-below-375` | Oculta el elemento por debajo de anchos de ventana inferiores a 375px.
| `show-below-desktop` | Mostrar sólo el elemento por debajo de anchos de ventana inferiores a 1024px.
| `show-below-tablet` | Mostrar sólo el elemento por debajo de anchos de ventana inferiores a 768px.
| `show-below-640` | Mostrar sólo el elemento por debajo de anchos de ventana inferiores a 640px.
| `show-below-480` | Mostrar sólo el elemento por debajo de anchos de ventana inferiores a 480px.
| `show-below-400` | Mostrar sólo el elemento por debajo de anchos de ventana inferiores a 400px.
| `show-below-375` | Mostrar sólo el elemento por debajo de anchos de ventana inferiores a 375px.
| `hide-if-logged-in` | Oculta el elemento si el usuario ha iniciado sesión.
| `hide-if-logged-out` | Oculta el elemento si el usuario ha cerrado la sesión.
| `no-theme-spacing` | Elimina el espaciado superior e inferior aplicado por el tema.
| `no-theme-style` | Elimina el estilo aplicado por el tema (para algunos bloques).
| `padding-[top\|right|bottom\|left]` | Aplica el relleno direccional de la página del tema.
| `bg-[50\|100\|200\|...||800\|900\|950]` | Fuerza el color de fondo del tema correspondiente.
| `fg-[100\|200\|...||800\|900\|950]` | Fuerza el color del texto del tema correspondiente.
| `max-site-width` | Aplica el ancho máximo del sitio del tema (principalmente útil para constructores de páginas).
| `header-polygon` | Aplica el clip-path de cabecera elegido en el Personalizador (si existe). No funciona con máscaras.
| `page-polygon` | Aplica la ruta de recorte de página elegida en el Personalizador (si existe). No funciona con máscaras.

## HTML Block

El bloque HTML personalizado es la mejor manera de añadir elementos especiales al contenido, como pantallas de estado en [litRPGs](https://en.wikipedia.org/wiki/LitRPG). La opción de vista previa en el editor ayuda si sólo estás haciendo pinitos. Esto se puede mejorar aún más con estilos en línea o clases CSS personalizadas, pero también hay que tener en cuenta el modo oscuro/claro y los ePUB generados. El siguiente ejemplo está integrado en el tema y seguro que funciona, sólo tienes que cambiar el contenido o eliminar lo que no necesites.

<detalles>
  <summary>HTML para caja litRPG</summary><br>
  <p>Puede utilizar con seguridad <code>h1</code>, <code>h2</code>, <code>h3</code>, <code>h4</code>, <code>h5</code>, <code>h6</code>, <code>table</code>, <code>thead</code>, <code>tbody</code>, <code>tr</code>, <code>th</code>, <code>td</code>, <code>strong</code>, <code>b</code>, <code>u</code>, <code>s</code>, <code>em</code>, <code>br</code>, <code>ins</code>, <code>del</code>, <code>sup</code>, <code>sub</code>, <code>hr</code>, <code>dl</code>, <code>dt</code>, <code>dd</code>, <code>p</code>, <code>small</code>, <code>ul</code>, <code>ol</code>, y <code>li</code>.</p>

```html
<div class="litrpg-box">
  <div class="litrpg-frame">
    <div class="litrpg-body">
      <!-- Iniciar contenido -->
      <h3>¡Cypher ha ganado 2 Puntos de Poder!</h3>
      <small style="margin: -1em 0 .25em; opacity: 0.65;"><strong>Nivel de Poder:</strong> 9 &ensp; <strong>Género:</strong> Mujer &ensp; <strong>Edad:</strong> 24</small>
      <table>
        <cuerpo>
          <tr>
            <td><strong>Fuerza</strong><br>5</strong>
            <td><strong>Resistencia</strong><br>-</td>
            <td><strong>Agilidad</strong><br>5</td>
            <td><strong>Destreza</strong><br>0</td>
          </tr>
          <tr>
            <td><strong>Lucha</strong><br>5</strong>
            <td><strong>Intelecto</strong><br>3 <ins>&#9650;1</ins></td>
            <td><strong>Conciencia</strong><br>4 <del>&#9660;1</del></td>
            <td><strong>Presencia</strong><br>2</td>
          </tr>
        </tbody>
      </table>
      <hr>
      <table>
        <cuerpo>
          <tr>
            <td><strong>Dodge</strong><br>5</td>
            <td><strong>Parry</strong><br>5</td>
            <td><strong>Fortaleza</strong><br>-</td>
            <td><strong>Will</strong><br>5</td>
            <td><strong>Dureza</strong><br>13</td>
          </tr>
        </tbody>
      </table>
      <hr>
      <dl>
        <dt>Ventajas:</dt>
        <dd>Ataque Cercano 8, Ataque a Distancia 4, Atractivo, Ataque Potente, Suerte 3, Desenfundado Rápido, Memoria Eidética</dd>
      </dl>
      <dl>
        <dt>Habilidades:</dt>
        <dd>Acrobacia 5, Atletismo 5, Perspicacia 5, Intimidación 6, Investigación 3, Percepción 7, Sigilo 5, Trato 3, Combate cuerpo a cuerpo 9, Pericia: Cibertecnología 11</dd>
      </dl>
      <dl>
        <dt>Poderes:</dt>
        <dd>Armadura (Protección 13) &bull; Cyborg (Inmunidad: Fortaleza) &bull; Ciberarmas (Fuerza mejorada 3) &bull; Ciberpiernas (Velocidad 2, Caída segura) &bull; Sangre blanca (Regeneración 2) &bull; Conjunto de sensores (Contrarresta la ocultación visual, Contrarresta las ilusiones visuales, Visión oscura, Sentido de la dirección, Visión ampliada 2, Radio)</dd>
      </dl>
      <p>Experiencia: 0/200</p>
      <small style="opacity: 0.65;"><a href="https://www.d20herosrd.com/" target="blank" rel="noopener">Contenido OGL de Mutants &amp; Masterminds</a></small>
      <!-- Fin del contenido -->
    </div>
  </div>
</div>
```

</detalles>

![LITRPG Box](repo/assets/litrpg_boxes.jpg?raw=true)

## Patreon Gate

Puede conceder a los usuarios registrados acceso a contenido protegido por contraseña a través de la membresía de Patreon, ya sea por niveles seleccionados o umbrales de compromiso o ambos. Consulte [guía de instalación](INSTALLATION.md#patreon-integration) para obtener más detalles. Los precios se almacenan en **centavos** (de ¢100 a $1), independientemente de la moneda de su campaña. Sigue siendo necesario establecer una contraseña para el puesto y las historias **no** pasan puertas abajo a los capítulos por razones técnicas.

**Caché:** Si utiliza un plugin de caché, asegúrese de que los mensajes protegidos por contraseña no se almacenan en caché o esto podría no funcionar correctamente. Los plugins LiteSpeed Cache, WP Super Cache y W3 Total Cache deberían funcionar bien, pero cualquier otro plugin podría necesitar una configuración adicional.

**Grada gratuita:** Si quieres que el contenido esté detrás de la grada gratuita (sólo seguir, no pagar), puedes añadir la grada junto a las demás. Si eso es demasiado inconveniente porque tienes demasiados niveles, puedes utilizar el umbral de compromiso para incluir cualquier nivel igual o superior a una determinada cantidad en céntimos (por ejemplo, 300 por 3,00 $), ya sea globalmente o puesto por puesto.

## Unlock Posts

Puedes conceder a los usuarios registrados acceso a contenidos protegidos por contraseña desbloqueando publicaciones específicas. Sólo tienes que abrir la página de perfil de administrador del usuario, buscar las entradas que quieres desbloquear, añadirlas y guardarlas. Los capítulos heredan el desbloqueo de la historia. Los roles que no sean administradores requieren las capacidades **Editar usuarios** y **Desbloquear entradas** para asignar entradas desbloqueadas a los usuarios, que pueden asignarse en el gestor de roles.

**Caché:** Si utiliza un plugin de caché, asegúrese de que los mensajes protegidos por contraseña no se almacenan en caché o esto podría no funcionar correctamente. Los plugins LiteSpeed Cache, WP Super Cache y W3 Total Cache deberían funcionar bien, pero cualquier otro plugin podría necesitar una configuración adicional.

**Puerta de Patreon:** Los desbloqueos de publicaciones son normalmente independientes de Patreon, pero puedes ponerles una puerta detrás de un umbral de compromiso global en centavos para limitar la función sólo a los mecenas de pago. Esto es adicional a cualquier otra puerta de Patreon.

![Unlock Posts](repo/assets/user_unlock_posts.jpg?raw=true)

## Shortcodes

[Shortcodes](https://wordpress.org/support/article/shortcode-block/) son palabras clave encerradas entre corchetes que se colocan dentro del contenido y que WordPress interpreta automáticamente como código, añadiendo funciones u objetos sin necesidad de programar. Esto debería hacerse dentro de un bloque _shortcode_, aunque también funcionaría fuera. Como la mayoría de los elementos creados por shortcodes no tienen márgenes, el bloque _spacer_ puede ser un buen añadido antes y/o después.

**Atención: las consultas de código corto se almacenan en caché como [Transients] (https://developer.wordpress.org/apis/transients/) para reducir su impacto en el rendimiento, especialmente si tiene más de una por página. Esto significa que no se actualizarán inmediatamente (excepto si tienes un plugin de caché activo, que deshabilita esta función). Por defecto, los Transients expiran a los 300 segundos (5 minutos), lo que puede cambiarse mediante la constante `FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION` en un tema hijo. Puede desactivar los Transitorios estableciendo la constante en `-1`.

### Story Actions Shortcode

Renderiza la fila de acción de la historia especificada. Todos los botones y enlaces funcionarán como si estuvieran en la entrada de la historia, excepto el modal para compartir, que siempre hace referencia a la página actual. Esto sólo funciona en páginas con la plantilla "Página de historia" y está pensado para crear una portada centrada en una sola historia.

* **story_id:** El ID de la historia.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **follow:** Si se muestra el botón Seguir (si está activado). Por defecto `true`.
**Recordatorio:** Si se muestra el botón Recordatorio (si está activado). Por defecto `true`.
* **subscribe:** Si se muestra el botón Suscribir (si está activado). Por defecto `true`.
* Descarga:** Si se muestra el botón de descarga de ePUB/eBook (si está activado). Por defecto `true`.
* **rss:** Si se muestra el enlace RSS (si está activado). Por defecto `true`.
* **compartir:** Si se muestra el botón modal Compartir (si está activado). Por defecto `true`.
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_story_actions story_id="106"]
```

```
[fictioneer_story_actions story_id="182" follow="0" reminder="0" share="0"]
```

### Story Section Shortcode

Renderiza los capítulos, grupos y pestañas de la historia especificada. Se verá igual que en la entrada de la historia. Esto sólo funciona en páginas con la plantilla "Página de historia" y está pensado para crear una portada centrada en una sola historia.

* **story_id:** El ID de la historia.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
**pestañas:** Si se muestran las pestañas (si las hay). Por defecto `false`.
* **blog:** Si se muestra la pestaña de blog. Por defecto `false`.
* **pages:** Si renderizar las pestañas de la página personalizada. Por defecto `false`.
* **programado:** Si se muestra la nota de capítulo programada. Por defecto `false`.
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_story_section story_id="106"]
```

```
[fictioneer_story_section story_id="182" tabs="true" pages="true"]
```

### Story Comments Shortcode

Renderiza el botón para cargar los comentarios colectivos hechos en los capítulos de la historia. No confundir con los comentarios que se pueden hacer en la página, que son completamente independientes. Esto sólo funciona en páginas con la plantilla "Página de historia" y está pensado para crear una portada centrada en una sola historia.

* **story_id:** El ID de la historia.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **encabezado:** Si se muestra el encabezado con el recuento. Por defecto `true`.

```
[fictioneer_story_comments story_id="13"]
```

```
[fictioneer_story_comments story_id="13" header="0"]
```

### Story Data Shortcode

Muestra un único dato de la historia especificada, como el **conteo de palabras** o la **valoración de edad**. Puede utilizarlo para mostrar sus propias estadísticas autoactualizables. Simplemente omite el bloque shortcode y escríbelo directamente en el texto.

* **datos:** Los datos solicitados, en singular. Elija entre `word_count`, `chapter_count`, `status`, `icon` (status), `age_rating`, `rating_letter`, `comment_count`, `id`, `date`, `time`, `datetime`, `categories`, `tags`, `genres`, `fandoms`, `characters`, y `warnings`.
* **story_id:** El ID de la historia. Por defecto es el ID del post actual.
**formato:** Formato especial para algunos datos. Utilizado sobre todo para los recuentos, utilice `short` o `raw`.
* **date_format:** Cadena de formato para la fecha. Por defecto a la configuración de WordPress.
**time_format:** Cadena de formato para la hora. Por defecto a la configuración de WordPress.
* **separador:** Cadena entre elementos de la lista, como etiquetas. Por defecto es `", "` (coma + espacio en blanco).
**tag:** Etiqueta HTML envolvente. Por defecto es `span`.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **inner_class:** Clases CSS adicionales para elementos anidados (si los hay), separadas por espacios en blanco.
* **style:** Estilo CSS en línea aplicado al elemento envolvente.
* **inner_style:** Estilo CSS en línea aplicado a los elementos anidados (si los hay).

```
La historia de ejemplo Katalepsis tiene [fictioneer_story_data story_id="13" data="chapter_count"] capítulos presentados en este sitio, que contienen un total de [fictioneer_story_data story_id="13" data="word_count"] palabras.
```

```
Puedes formatear el recuento de palabras con "raw" ([fictioneer_story_data story_id="13" data="word_count" format="raw"]) o "short" ([fictioneer_story_data story_id="13" data="word_count" format="short"]).
```

```
Katalepsis tiene las siguientes etiquetas: [fictioneer_story_data story_id="13" data="tags" separator=" | " inner_style="color: var(--red-500);"].
```

### Subscribe Button Shortcode

Muestra un botón de suscripción para la historia especificada.

* **story_id:** El ID de la historia para la que es el botón.
**class:** Clases CSS adicionales, separadas por espacios en blanco.

```
[fictioneer_subscribe_button story_id="228"]
```

### Font Awesome Shortcode

Renderiza un icono *gratis* [Font Awesome](https://fontawesome.com/), que técnicamente también podrías hacer manualmente en el editor de código. Algo más conveniente, supongo. Simplemente omite el bloque del shortcode y escríbelo directamente en el texto. Este shortcode también funciona si tu función carece de la capacidad de shortcode.

* **class:** Clases CSS de Font Awesome, separadas por espacios en blanco. Usted puede personalizar algunos, también.

```
Toma un poco de [fictioneer_fa class="fa-solid fa-mug-hot"]
```

### Article Cards

Presenta una cuadrícula de varias columnas de tarjetas de medios paginadas ordenadas por fecha de publicación, descendente. A menos que proporcione el parámetro **count**, sólo añada esto una vez por página, ya que utiliza el argumento principal de la página de consulta. La miniatura es la **Imagen de paisaje** o la **Imagen de portada**, dependiendo de la relación de aspecto y la disponibilidad, con los capítulos por defecto en la historia padre.

* **post_type:** Lista separada por comas de los tipos de post a consultar. Por defecto `post`.
* **post_ids:** Lista separada por comas de IDs de post, si desea elegir de un grupo curado.
* **per_page:** Número de mensajes por página. Por defecto la configuración del tema.
* **contar:** Limitar los artículos a cualquier número positivo, deshabilitando la paginación.
* **order:** O `desc` (descendente) o `asc` (ascendente). Por defecto `desc`.
* **orderby:** Por defecto es `date`, pero también puedes usar `modified` y [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
**ignore_sticky:** Si los mensajes pegajosos deben ser ignorados o no. Por defecto `false`.
**ignore_protected:** Si los mensajes protegidos deben ser ignorados o no. Por defecto `false`.
**only_protected:** Si desea consultar sólo los puestos protegidos o no. Por defecto `false`.
**autor:** Mostrar sólo las recomendaciones de un autor específico. Asegúrese de utilizar el url-safe nice_name.
* **author_ids:** Mostrar sólo entradas de una lista separada por comas de IDs de autor.
* **exclude_author_ids:** Lista separada por comas de IDs de autores a excluir.
* **exclude_cat_ids:** Lista separada por comas de ID de categoría a excluir.
* **exclude_tag_ids:** Lista separada por comas de ID de etiquetas a excluir.
**categorías:** Lista separada por comas de nombres de categorías (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto de categorías.
**etiquetas:** Lista de nombres de etiquetas separadas por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlas de un conjunto de etiquetas.
* **fandoms:** Lista separada por comas de nombres de fandom (sin distinguir mayúsculas de minúsculas), si quieres elegir de un grupo seleccionado.
**géneros:** Lista de nombres de géneros separada por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlos de un grupo seleccionado.
* **caracteres:** Lista de nombres de caracteres separados por comas (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto curado.
* **rel:** Relación entre diferentes taxonomías, ya sea `AND` o `OR`. Por defecto `AND`.
* **seamless:** Si desea eliminar el espacio entre la imagen y el marco. Por defecto `false` (configuración del personalizador).
* **thumbnail:** Si mostrar la imagen en miniatura/portada. Por defecto `true` (configuración del personalizador).
* **lightbox:** Si al hacer click en la imagen miniatura/portada se abre el lightbox o el enlace de la entrada. Por defecto `true`.
* **terms:** O bien `inline`, `pills`, o `none`. Por defecto `inline`.
**max_terms:** Número máximo de taxonomías mostradas. Por defecto `10`.
* **date_format:** Cadena para anular el [formato de fecha](https://wordpress.org/documentation/article/customize-date-and-time-format/). Por defecto `''`.
* **footer:** Si se muestra el pie de página (si existe). Por defecto `true`.
* **footer_author:** Si mostrar el autor del post. Por defecto `true`.
* **footer_date:** Si mostrar la fecha del post. Por defecto `true`.
* **footer_comments:** Si mostrar el recuento de comentarios del post. Por defecto `true`.
* **aspect_ratio:** Valor CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) para la imagen (X/Y). Por defecto `3/1`.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **splide:** JSON de configuración para convertir la rejilla en un deslizador. Ver [Slider](#slider).
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_article_cards]
```

```
[fictioneer_article_cards post_type="post" per_page="4" ignore_sticky="true"]
```

```
[fictioneer_article_cards post_type="story, chapter" count="8" ignore_protected="true"]
```

```
[fictioneer_article_cards post_type="story, chapter" seamless="true" aspect_ratio="4/1"]
```

![Article Cards](repo/assets/shortcode_example_article_cards.jpg?raw=true)
![Article Cards](repo/assets/shortcode_example_article_cards_2.png?raw=true)

### Blog

Renderiza las entradas paginadas del blog de forma similar a la página principal del blog, pero con opciones. Añádelo sólo una vez por página, ya que utiliza el argumento de la página de consulta principal, evita combinarlo con el shortcode Article Cards.

* **per_page:** Número de mensajes por página. Por defecto la configuración del tema.
**ignore_sticky:** Si los mensajes pegajosos deben ser ignorados o no. Por defecto `false`.
**ignore_protected:** Si los mensajes protegidos deben ser ignorados o no. Por defecto `false`.
**only_protected:** Si desea consultar sólo los puestos protegidos o no. Por defecto `false`.
**author:** Mostrar sólo los mensajes de un autor específico. Asegúrese de utilizar el url-safe nice_name.
* **author_ids:** Mostrar sólo entradas de una lista separada por comas de IDs de autor.
* **exclude_author_ids:** Lista separada por comas de IDs de autores a excluir.
* **exclude_cat_ids:** Lista separada por comas de ID de categoría a excluir.
* **exclude_tag_ids:** Lista separada por comas de ID de etiquetas a excluir.
**categorías:** Lista separada por comas de nombres de categorías (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto de categorías.
**etiquetas:** Lista de nombres de etiquetas separadas por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlas de un conjunto de etiquetas.
* **rel:** Relación entre diferentes taxonomías, ya sea `AND` o `OR`. Por defecto `AND`.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_blog]
```

```
[fictioneer_blog class="foo bar baz" per_page="5" exclude_cat_ids="1,23,24" categories="news"]
```

```
[fictioneer_blog categories="uncategorized"]
```

### Bookmarks

Muestra una cuadrícula de varias columnas con pequeñas tarjetas de marcadores, ordenadas por fecha de creación. Los marcadores se almacenan en el navegador y se añaden al documento mediante JavaScript. Puede combinarlo con las clases CSS adicionales `show-if-bookmarks hidden`, mostrando un titular u otro elemento sólo si los marcadores están presentes.

* **count:** Limita los marcadores a cualquier número positivo. Por defecto `-1` (todos).
* **show_empty:** Si mostrar una nota "no bookmarks" o nada si está vacío. Por defecto `false`.
* **seamless:** Si desea eliminar el espacio entre la imagen y el marco. Por defecto `false` (configuración del personalizador).
* **thumbnail:** Si mostrar la imagen en miniatura/portada. Por defecto `true` (configuración del personalizador).

```
[fictioneer_bookmarks]
```

```
[fictioneer_bookmarks count="8" show_empty="true"]
```

```
[fictioneer_bookmarks count="8" seamless="true" thumbnail="0"]
```

![Bookmarks](repo/assets/shortcode_example_bookmarks.jpg?raw=true)

### Chapter List

Genera una lista de capítulos idénticos a los de las páginas de historia, ordenados por secuencia en la fuente. Debe tener el parámetro **story_id** o **chapter_ids**, pero no ambos.

* **story_id:** ID de una sola historia. Necesitas esto o **capítulos**.
* **chapter_ids:** Lista separada por comas de IDs de capítulos. Necesita esto o **story**.
* **count:** Limita los capítulos a cualquier número positivo. Por defecto `-1` (todos).
* **offset:** Saltar un número de capítulos, que puede tener sentido si se consulta todo.
* **encabezado:** Mostrar un encabezado con conmutación de colapso por encima de la lista.
* **grupo:** Mostrar sólo capítulos con un nombre de grupo específico, que puede trascender historias.
**class:** Clases CSS adicionales, separadas por espacios en blanco. `no-auto-collapse` evita el colapso del grupo por defecto (si está configurado).
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_chapter_list story="69"]
```

```
[fictioneer_chapter_list class="foobar no-auto-collapse" story="69" count="10" offset="2"]
```

```
[fictioneer_chapter_list chapters="13,21,34" heading="Pigs are a lot bigger than you expect" group="You could ride it"]
```

![Chapter List](repo/assets/shortcode_example_chapter_list_1.jpg?raw=true)

### Contact Form

Muestra un formulario de contacto con varios campos (opcionales). Los envíos son validados, desinfectados, tienen una protección básica contra el spam y se comprueban con la lista de no permitidos de WordPress en **Configuración > Discusiones**. Si se superan todos los pasos, el formulario se envía a las direcciones de correo electrónico que aparecen en **Fictioneer > General > Receptores del formulario de contacto**, que nunca se revelan al público. Si está vacía, se utilizará la dirección de correo electrónico del administrador.

* **title:** Título del formulario que se muestra en los correos electrónicos. Por defecto es "Formulario sin nombre".
* **submit:** Etiqueta del botón de envío. Por defecto es "Enviar".
* **privacy_policy:** Si la política de privacidad debe ser aceptada. Por defecto `false`.
* **required:** Si todos los campos deben ser rellenados. Por defecto `false`.
* **email:** Dirección de correo electrónico del remitente para las respuestas.
* **nombre:** Nombre del remitente para respuestas personales.
* **text_[1-6]:** Campos de texto personalizados del 1 al 6, por ejemplo del **text_1** al **text_6**.
* **check_[1-6]:** Casillas de verificación personalizadas del 1 al 6, por ejemplo del **check_1** al **check_6**.
**class:** Clases CSS adicionales, separadas por espacios en blanco.

```
[fictioneer_contact_form]
```

```
[fictioneer_contact_form email="Email Address (required)" check_1="Totally not a robot" title="Human Test" privacy_policy="true" required="true"]
```

```
[fictioneer_contact_form email="Email Address (optional)" name="Your Name (optional)" text_1="Topic (optional)" title="Privacy Policy Contact Form" privacy_policy="true"]
```

![Contact Form](repo/assets/shortcode_example_contact_form_1.jpg?raw=true)

### Cookie Buttons

Presenta dos botones para tratar las cookies, "Restablecer consentimiento" y "Borrar cookies". Se utiliza mejor en la sección Cookies de su Política de privacidad.

```
[fictioneer_cookie_buttons]
```

![Bookmarks](repo/assets/shortcode_example_cookie_buttons.jpg?raw=true)

### Latest Chapters

Muestra una cuadrícula de varias columnas de tarjetas pequeñas, con los cuatro últimos capítulos ordenados por fecha de publicación, en orden descendente. Tenga en cuenta que el tipo `list` se comporta un poco diferente con los parámetros.

* **count:** Limita los capítulos a cualquier número positivo, aunque deberías mantenerlo razonable. Por defecto `4`.
* **tipo:** O bien `default`, `simple`, `compact`, o bien `list`. Las otras variantes son más pequeñas con menos datos.
* **author:** Mostrar sólo capítulos de un autor específico. Asegúrese de utilizar el url-safe nice_name.
* **order:** O `desc` (descendente) o `asc` (ascendente). Por defecto `desc`.
* **orderby:** Por defecto es `date`, pero también puedes usar `modified` y [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **spoiler:** El extracto está ofuscado, establezca `true` si desea revelarlo. Por defecto `false`.
* **source:** Si mostrar los nodos de autor e historia. Por defecto `true`.
* **post_ids:** Lista separada por comas de IDs de entradas de capítulos, si desea elegir de un grupo curado.
**ignore_protected:** Si los mensajes protegidos deben ser ignorados o no. Por defecto `false`.
**only_protected:** Si desea consultar sólo los puestos protegidos o no. Por defecto `false`.
* **author_ids:** Mostrar sólo entradas de una lista separada por comas de IDs de autor.
* **exclude_author_ids:** Lista separada por comas de IDs de autores a excluir.
* **exclude_cat_ids:** Lista separada por comas de ID de categoría a excluir.
* **exclude_tag_ids:** Lista separada por comas de ID de etiquetas a excluir.
**categorías:** Lista separada por comas de nombres de categorías (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto de categorías.
**etiquetas:** Lista de nombres de etiquetas separadas por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlas de un conjunto de etiquetas.
* **fandoms:** Lista separada por comas de nombres de fandom (sin distinguir mayúsculas de minúsculas), si quieres elegir de un grupo seleccionado.
**géneros:** Lista de nombres de géneros separada por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlos de un grupo seleccionado.
* **caracteres:** Lista de nombres de caracteres separados por comas (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto curado.
* **rel:** Relación entre diferentes taxonomías, ya sea `AND` o `OR`. Por defecto `AND`.
* **vertical:** Si renderizar las tarjetas con la imagen en la parte superior. Por defecto `false`.
* **seamless:** Si desea eliminar el espacio entre la imagen y el marco. Por defecto `false` (configuración del personalizador).
* **thumbnail:** Si mostrar la imagen en miniatura/portada. Por defecto `true` (configuración del personalizador).
* **lightbox:** Si al hacer click en la imagen miniatura/portada se abre el lightbox o el enlace de la entrada. Por defecto `true`.
* **infobox:** Si mostrar la caja de información y alternar en versiones compactas. Por defecto `true`.
* **date_format:** Cadena para anular el [formato de fecha](https://wordpress.org/documentation/article/customize-date-and-time-format/). Por defecto `''`.
* **footer:** Si se muestra el pie de página (si existe). Por defecto `true`.
* **footer_author:** Si mostrar el autor del capítulo. Por defecto `true`.
* **footer_words:** Si mostrar el recuento de palabras del capítulo. Por defecto `true`.
* **footer_date:** Si mostrar la fecha del capítulo. Por defecto `true`.
* **footer_comments:** Si mostrar el recuento de comentarios del capítulo (no en `list`). Por defecto `true`.
* **footer_status:** Si mostrar el estado de la historia del capítulo. Por defecto `true`.
* **footer_rating:** Si mostrar la clasificación por edades del capítulo. Por defecto `true`.
* **aspect_ratio:** Valor CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) para la imagen (X/Y; sólo vertical). Por defecto `3/1`.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **splide:** JSON de configuración para convertir la rejilla en un deslizador. Ver [Slider](#slider).
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_latest_chapters]
```

```
[fictioneer_latest_chapters genres="adventure, historical" characters="indiana jones"]
```

```
[fictioneer_latest_chapters count="10" type="compact" author="Tetrakern" order="asc" orderby="modified" spoiler="true" source="false" chapters="1,2,3,5,8,13,21,34"]
```

```
[fictioneer_latest_chapters source="false" vertical="true" seamless="true" aspect_ratio="5/1"]
```

![Latest Chapters](repo/assets/shortcode_example_latest_chapters.jpg?raw=true)
![Latest Chapters](repo/assets/shortcode_example_latest_chapters_2.png?raw=true)

```
[fictioneer_latest_chapters type="list" count="2"]
```

```
[fictioneer_latest_chapters type="list" orderby="rand" count="2" source="false" footer_status="false"]
```

![Latest Chapters](repo/assets/shortcode_example_latest_chapters_3.png?raw=true)

### Latest Posts

Muestra la última entrada del blog o una lista de entradas del blog, ignorando las entradas pegajosas, ordenadas por fecha de publicación, descendente.

* **count:** Limita los mensajes a cualquier número positivo, aunque deberías mantenerlo razonable. Por defecto `1`.
**author:** Mostrar sólo los mensajes de un autor específico. Asegúrese de utilizar el url-safe nice_name.
* **post_ids:** Lista separada por comas de IDs de post, si desea elegir de un grupo curado.
**ignore_protected:** Si los mensajes protegidos deben ser ignorados o no. Por defecto `false`.
**only_protected:** Si desea consultar sólo los puestos protegidos o no. Por defecto `false`.
* **author_ids:** Mostrar sólo entradas de una lista separada por comas de IDs de autor.
* **exclude_author_ids:** Lista separada por comas de IDs de autores a excluir.
* **exclude_cat_ids:** Lista separada por comas de ID de categoría a excluir.
* **exclude_tag_ids:** Lista separada por comas de ID de etiquetas a excluir.
**categorías:** Lista separada por comas de nombres de categorías (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto de categorías.
**etiquetas:** Lista de nombres de etiquetas separadas por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlas de un conjunto de etiquetas.
* **rel:** Relación entre diferentes taxonomías, ya sea `AND` o `OR`. Por defecto `AND`.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_latest_posts]
```

```
[fictioneer_latest_posts count="16" tags="world building, characters" categories="blog, tutorials" rel="or"]
```

```
[fictioneer_latest_posts count="4" author="Tetrakern" posts="1,2,3,5,8,13,21,34"]
```

![Latest Posts](repo/assets/shortcode_example_latest_posts.jpg?raw=true)

### Latest Recommendations

Presenta una cuadrícula de varias columnas de pequeñas tarjetas, mostrando las últimas cuatro recomendaciones ordenadas por fecha de publicación, en orden descendente.

* **count:** Limita las recomendaciones a cualquier número positivo, aunque deberías mantenerlo razonable. Por defecto `4`.
* **tipo:** Puede ser `default` o `compact`. La variante compacta es más pequeña con menos datos.
**autor:** Mostrar sólo las recomendaciones de un autor específico. Asegúrese de utilizar el url-safe nice_name.
* **order:** O `desc` (descendente) o `asc` (ascendente). Por defecto `desc`.
* **orderby:** Por defecto es `date`, pero también puedes usar `modified` y [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **post_ids:** Lista separada por comas de IDs de post, si desea elegir de un grupo curado.
**ignore_protected:** Si los mensajes protegidos deben ser ignorados o no. Por defecto `false`.
**only_protected:** Si desea consultar sólo los puestos protegidos o no. Por defecto `false`.
* **author_ids:** Mostrar sólo entradas de una lista separada por comas de IDs de autor.
* **exclude_author_ids:** Lista separada por comas de IDs de autores a excluir.
* **exclude_cat_ids:** Lista separada por comas de ID de categoría a excluir.
* **exclude_tag_ids:** Lista separada por comas de ID de etiquetas a excluir.
**categorías:** Lista separada por comas de nombres de categorías (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto de categorías.
**etiquetas:** Lista de nombres de etiquetas separadas por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlas de un conjunto de etiquetas.
* **fandoms:** Lista separada por comas de nombres de fandom (sin distinguir mayúsculas de minúsculas), si quieres elegir de un grupo seleccionado.
**géneros:** Lista de nombres de géneros separada por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlos de un grupo seleccionado.
* **caracteres:** Lista de nombres de caracteres separados por comas (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto curado.
* **rel:** Relación entre diferentes taxonomías, ya sea `AND` o `OR`. Por defecto `AND`.
* **vertical:** Si renderizar las tarjetas con la imagen en la parte superior. Por defecto `false`.
* **seamless:** Si desea eliminar el espacio entre la imagen y el marco. Por defecto `false` (configuración del personalizador).
* **thumbnail:** Si mostrar la imagen en miniatura/portada. Por defecto `true` (configuración del personalizador).
* **terms:** O bien `inline`, `pills`, o `none`. Por defecto `inline`.
**max_terms:** Número máximo de taxonomías mostradas. Por defecto `10`.
* **lightbox:** Si al hacer click en la imagen miniatura/portada se abre el lightbox o el enlace de la entrada. Por defecto `true`.
* **infobox:** Si mostrar la caja de información y alternar en versiones compactas. Por defecto `true`.
* **aspect_ratio:** Valor CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) para la imagen (X/Y; sólo vertical). Por defecto `3/1`.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **splide:** JSON de configuración para convertir la rejilla en un deslizador. Ver [Slider](#slider).
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_latest_recommendations]
```

```
[fictioneer_latest_recommendations genres="isekai" fandoms="original, fanfiction"]
```

```
[fictioneer_latest_recommendations count="10" type="compact" author="Tetrakern" order="asc" orderby="rand" recommendations="1,2,3,5,8,13,21,34"]
```

```
[fictioneer_latest_recommendations vertical="true" seamless="true"]
```

![Latest Recommendations](repo/assets/shortcode_example_latest_recommendations.jpg?raw=true)
![Latest Recommendations](repo/assets/shortcode_example_latest_recommendations_2.png?raw=true)

### Latest Stories

Presenta una cuadrícula de varias columnas de pequeñas tarjetas, mostrando las últimas cuatro historias ordenadas por fecha de publicación, de forma descendente. Tenga en cuenta que el tipo `list` se comporta un poco diferente con los parámetros.

* **count:** Limita las historias a cualquier número positivo, aunque deberías mantenerlo razonable. Por defecto `4`.
* **tipo:** O bien `default`, `compact`, o `list`. La variante compacta es más pequeña con menos datos.
* **autor:** Mostrar sólo historias de un autor específico. Asegúrate de escribir bien el _nombre_ de usuario.
* **order:** O `desc` (descendente) o `asc` (ascendente). Por defecto `desc`.
* **orderby:** Por defecto es `date`, pero también puedes usar `modified` y [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **post_ids:** Lista separada por comas de los IDs de los posts de las historias, si quieres elegir de un grupo curado.
**ignore_protected:** Si los mensajes protegidos deben ser ignorados o no. Por defecto `false`.
**only_protected:** Si desea consultar sólo los puestos protegidos o no. Por defecto `false`.
* **author_ids:** Mostrar sólo entradas de una lista separada por comas de IDs de autor.
* **exclude_author_ids:** Lista separada por comas de IDs de autores a excluir.
* **exclude_cat_ids:** Lista separada por comas de ID de categoría a excluir.
* **exclude_tag_ids:** Lista separada por comas de ID de etiquetas a excluir.
**categorías:** Lista separada por comas de nombres de categorías (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto de categorías.
**etiquetas:** Lista de nombres de etiquetas separadas por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlas de un conjunto de etiquetas.
* **fandoms:** Lista separada por comas de nombres de fandom (sin distinguir mayúsculas de minúsculas), si quieres elegir de un grupo seleccionado.
**géneros:** Lista de nombres de géneros separada por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlos de un grupo seleccionado.
* **caracteres:** Lista de nombres de caracteres separados por comas (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto curado.
* **rel:** Relación entre diferentes taxonomías, ya sea `AND` o `OR`. Por defecto `AND`.
* **fuente:** Si mostrar el nodo autor. Por defecto `true`.
* **vertical:** Si renderizar las tarjetas con la imagen en la parte superior. Por defecto `false`.
* **seamless:** Si desea eliminar el espacio entre la imagen y el marco. Por defecto `false` (configuración del personalizador).
* **thumbnail:** Si mostrar la imagen en miniatura/portada. Por defecto `true` (configuración del personalizador).
* **lightbox:** Si al hacer click en la imagen miniatura/portada se abre el lightbox o el enlace de la entrada. Por defecto `true`.
* **infobox:** Si mostrar la caja de información y alternar en versiones compactas. Por defecto `true`.
* **date_format:** Cadena para anular el [formato de fecha](https://wordpress.org/documentation/article/customize-date-and-time-format/). Por defecto `''`.
* **terms:** O bien `inline`, `pills`, o `none`. Por defecto `inline`.
**max_terms:** Número máximo de taxonomías mostradas. Por defecto `10`.
* **footer:** Si se muestra el pie de página (si existe). Por defecto `true`.
* **footer_author:** Si mostrar el autor. Por defecto `true`.
* **footer_chapters:** Si mostrar el recuento de capítulos (no en `list`). Por defecto `true`.
* **footer_words:** Si mostrar el recuento de palabras. Por defecto `true`.
* **footer_date:** Si mostrar la fecha. Por defecto `true`.
* **footer_status:** Si mostrar el estado. Por defecto `true`.
* **footer_rating:** Si mostrar la clasificación por edades. Por defecto `true`.
* **aspect_ratio:** Valor CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) para la imagen (X/Y; sólo vertical). Por defecto `3/1`.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **splide:** JSON de configuración para convertir la rejilla en un deslizador. Ver [Slider](#slider).
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_latest_stories]
```

```
[fictioneer_latest_stories genres="adventure, cyberpunk" characters="Rebecca" rel="or"]
```

```
[fictioneer_latest_stories count="10" type="compact" author="Tetrakern" order="asc" orderby="modified" stories="1,2,3,5,8,13,21,34"]
```

```
[fictioneer_latest_stories count="2" author="Hungry" seamless="true"]
```

```
[fictioneer_latest_stories type="compact" vertical="true" aspect_ratio="3/2"]
```

![Latest Stories](repo/assets/shortcode_example_latest_stories.jpg?raw=true)
![Latest Stories](repo/assets/shortcode_example_latest_stories_3.png?raw=true)
![Latest Stories](repo/assets/shortcode_example_latest_stories_2.png?raw=true)

```
[fictioneer_latest_stories type="list"]
```

```
[fictioneer_latest_stories type="list" footer_status="false" footer_rating="false" terms="pills" aspect_ratio="2/3"]
```

![Latest Stories](repo/assets/shortcode_example_latest_stories_4.png?raw=true)

### Latest Updates

Presenta una cuadrícula de varias columnas de tarjetas pequeñas, mostrando las últimas cuatro historias actualizadas ordenadas por fecha del último cambio de capítulo, en orden descendente. Tenga en cuenta que el tipo `list` se comporta un poco diferente con los parámetros.

* **count:** Limita las actualizaciones a cualquier número positivo, aunque deberías mantenerlo razonable. Por defecto `4`.
* **tipo:** O bien `default`, `simple`, `single`, `compact`, o `list`. Las otras variantes son más pequeñas con menos datos.
* **single:** Si mostrar sólo un elemento de capítulo (incluido en el tipo `single`). Por defecto `false`.
**autor:** Mostrar sólo las actualizaciones de un autor específico. Asegúrese de utilizar el url-safe nice_name.
* **order:** O `desc` (descendente) o `asc` (ascendente). Por defecto `desc`.
* **post_ids:** Lista separada por comas de IDs de post, si desea elegir de un grupo curado.
**ignore_protected:** Si los mensajes protegidos deben ser ignorados o no. Por defecto `false`.
**only_protected:** Si desea consultar sólo los puestos protegidos o no. Por defecto `false`.
* **author_ids:** Mostrar sólo entradas de una lista separada por comas de IDs de autor.
* **exclude_author_ids:** Lista separada por comas de IDs de autores a excluir.
* **exclude_cat_ids:** Lista separada por comas de ID de categoría a excluir.
* **exclude_tag_ids:** Lista separada por comas de ID de etiquetas a excluir.
**categorías:** Lista separada por comas de nombres de categorías (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto de categorías.
**etiquetas:** Lista de nombres de etiquetas separadas por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlas de un conjunto de etiquetas.
* **fandoms:** Lista separada por comas de nombres de fandom (sin distinguir mayúsculas de minúsculas), si quieres elegir de un grupo seleccionado.
**géneros:** Lista de nombres de géneros separada por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlos de un grupo seleccionado.
* **caracteres:** Lista de nombres de caracteres separados por comas (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto curado.
* **rel:** Relación entre diferentes taxonomías, ya sea `AND` o `OR`. Por defecto `AND`.
* **fuente:** Si mostrar el nodo autor. Por defecto `true`.
* **vertical:** Si renderizar las tarjetas con la imagen en la parte superior. Por defecto `false`.
* **seamless:** Si desea eliminar el espacio entre la imagen y el marco. Por defecto `false` (configuración del personalizador).
* **thumbnail:** Si mostrar la imagen en miniatura/portada. Por defecto `true` (configuración del personalizador).
* **lightbox:** Si al hacer click en la imagen miniatura/portada se abre el lightbox o el enlace de la entrada. Por defecto `true`.
* **infobox:** Si mostrar la caja de información y alternar en versiones compactas. Por defecto `true`.
* **aspect_ratio:** Valor CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) para la imagen (X/Y; sólo vertical). Por defecto `3/1`.
* **words:** Si se muestra el recuento de palabras de los elementos del capítulo. Por defecto `true`.
* **date:** Si mostrar la fecha de los elementos del capítulo. Por defecto `true`.
* **date_format:** Cadena para anular el [formato de fecha](https://wordpress.org/documentation/article/customize-date-and-time-format/). Por defecto `''`.
* **nested_date_format:** Cadena para anular cualquier [formato de fecha] anidado (https://wordpress.org/documentation/article/customize-date-and-time-format/). Por defecto `''`.
* **terms:** O bien `inline`, `pills`, o `none`. Por defecto `inline`.
**max_terms:** Número máximo de taxonomías mostradas. Por defecto `10`.
* **footer:** Si se muestra el pie de página (si existe). Por defecto `true`.
* **footer_author:** Si mostrar el autor de la historia/capítulo. Por defecto `true`.
* **footer_chapters:** Si mostrar el recuento de capítulos de la historia (no en `list`). Por defecto `true`.
* **footer_words:** Si mostrar el recuento de palabras de la historia. Por defecto `true`.
* **footer_date:** Si mostrar la fecha de la historia. Por defecto `true`.
* **footer_status:** Si mostrar el estado de la historia. Por defecto `true`.
* **footer_rating:** Si mostrar la clasificación por edades de la historia. Por defecto `true`.
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **splide:** JSON de configuración para convertir la rejilla en un deslizador. Ver [Slider](#slider).
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_latest_updates]
```

```
[fictioneer_latest_updates genres="romance, drama" fandoms="original"]
```

```
[fictioneer_latest_updates count="10" type="simple" author="Tetrakern" order="asc" stories="1,2,3,5,8,13,21,34"]
```

```
[fictioneer_latest_updates type="compact" order="asc" post_ids="13,106" seamless="true"]
```

```
[fictioneer_latest_updates type="compact" vertical="true" seamless="true"]
```

```
[fictioneer_latest_updates type="simple" single="true" date="0"]
```

![Latest Updates](repo/assets/shortcode_example_latest_updates.jpg?raw=true)
![Latest Updates](repo/assets/shortcode_example_latest_updates_3.png?raw=true)
![Latest Updates](repo/assets/shortcode_example_latest_updates_2.png?raw=true)
![Latest Updates](repo/assets/shortcode_example_latest_updates_4.png?raw=true)

```
[fictioneer_latest_updates type="list" count="2" nested_date_format="m/d/Y"]
```

```
[fictioneer_latest_updates type="list" count="2" seamless="true" date="false" words="false" footer_rating="false" terms="pills"]
```

![Latest Updates](repo/assets/shortcode_example_latest_updates_5.png?raw=true)

### Search Form

Muestra el formulario de búsqueda con opciones avanzadas (si no están desactivadas en la configuración).

* **simple:** Establecer `true` para ocultar las opciones de búsqueda avanzada. Por defecto `false`.
* **marcador de posición:** Cambia el texto del marcador de posición.
* **tipo:** Preseleccione "cualquiera", "historia", "capítulo", "recomendación", "colección" o "post".
* **expanded:** Si el formulario avanzado está expandido. Por defecto `false`.
* **etiquetas:** Preseleccionar etiquetas como lista separada por comas de IDs de términos.
* **géneros:** Preseleccione los géneros como una lista separada por comas de ID de términos.
* **fandoms:** Preseleccionar fandoms como lista separada por comas de IDs de términos.
* **caracteres:** Preseleccionar caracteres como lista separada por comas de ID de términos.
* **advertencias:** Preseleccionar advertencias como lista separada por comas de IDs de términos.

```
[fictioneer_search]
```

```
[fictioneer_search simple="true" placeholder="What are you looking for?"]
```

```
[fictioneer_search tags="569" fandoms="200,199"]
```

![Contact Form](repo/assets/shortcode_example_search_1.jpg?raw=true)

### Showcase

Presenta una cuadrícula dinámica de miniaturas con título, mostrando las últimas ocho entradas del tipo especificado ordenadas por fecha de publicación, en orden descendente. Requiere el parámetro **for**. La miniatura es la **Imagen del paisaje** (si está disponible) o la **Imagen de portada**, y los capítulos son por defecto la historia principal.

* **para:** Tipo de entrada deseada, ya sea `historias`, `capítulos`, `colecciones` o `recomendaciones`.
* **count:** Limita los mensajes a cualquier número positivo, aunque deberías mantenerlo razonable. Por defecto `8`.
**autor:** Mostrar sólo los mensajes de un autor específico. Asegúrese de utilizar el url-safe nice_name.
* **order:** O `desc` (descendente) o `asc` (ascendente). Por defecto `desc`.
* **orderby:** Por defecto es `date`, pero también puedes usar `rand` y [more](https://developer.wordpress.org/reference/classes/wp_query/#order-orderby-parameters).
* **post_ids:** Lista separada por comas de IDs de post, si desea elegir de un grupo curado.
**ignore_protected:** Si los mensajes protegidos deben ser ignorados o no. Por defecto `false`.
**only_protected:** Si desea consultar sólo los puestos protegidos o no. Por defecto `false`.
* **author_ids:** Mostrar sólo entradas de una lista separada por comas de IDs de autor.
* **exclude_author_ids:** Lista separada por comas de IDs de autores a excluir.
* **exclude_cat_ids:** Lista separada por comas de ID de categoría a excluir.
* **exclude_tag_ids:** Lista separada por comas de ID de etiquetas a excluir.
**categorías:** Lista separada por comas de nombres de categorías (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto de categorías.
**etiquetas:** Lista de nombres de etiquetas separadas por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlas de un conjunto de etiquetas.
* **fandoms:** Lista separada por comas de nombres de fandom (sin distinguir mayúsculas de minúsculas), si quieres elegir de un grupo seleccionado.
**géneros:** Lista de nombres de géneros separada por comas (sin distinguir mayúsculas de minúsculas), si desea seleccionarlos de un grupo seleccionado.
* **caracteres:** Lista de nombres de caracteres separados por comas (sin distinguir mayúsculas de minúsculas), si desea elegir de un conjunto curado.
* **no_cap:** Establece `true` si quieres ocultar el pie de foto.
* **aspect_ratio:** Valor CSS [aspect-ratio](https://developer.mozilla.org/en-US/docs/Web/CSS/aspect-ratio) para el elemento (X/Y).
* **height:** Anula la altura del elemento. Sustituido por `aspect_ratio`.
* **ancho:** Anula el ancho mínimo del elemento (se seguirá estirando para llenar el espacio).
**class:** Clases CSS adicionales, separadas por espacios en blanco.
* **splide:** JSON de configuración para convertir la rejilla en un deslizador. Ver [Slider](#slider).
* **cache:** Si el shortcode debe ser almacenado en caché. Por defecto `true`.

```
[fictioneer_showcase for="collections"]
```

```
[fictioneer_showcase for="collections" count="10" author="Tetrakern" order="asc" posts="1,2,3,5,8,13,21,34"]
```

![Showcase](repo/assets/shortcode_example_showcase.jpg?raw=true)

```
[fictioneer_showcase for="stories" count="4" aspect_ratio="2/3" min_width="150px"]
```

![Showcase](repo/assets/shortcode_example_showcase_2.jpg?raw=true)

### Slider

Cualquier shortcode con el parámetro `splide` puede convertirse en un slider. [Splide](https://splidejs.com/) es un slider flexible y ligero que viene con [muchas opciones](https://splidejs.com/guides/options/) de personalización, aunque aplicarlas puede ser un reto si no estás familiarizado con [JSONs](https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Objects/JSON). Puedes consultar los detalles tú mismo.

El parámetro `splide` sólo acepta cadenas JSON, como `splide="{'type':'loop','perPage':3}"`. Tenga en cuenta que debe utilizar **comillas simples** debido a la sintaxis del shortcode. Si hay un error, aunque sea mínimo, el JSON será rechazado con una nota, y el shortcode volverá a su diseño estándar. No todas las combinaciones de parámetros han sido probadas con Splide, por lo que puede ser necesario CSS personalizado en algunos casos.

Si no desea inicializar un slider al cargar la página, puede añadir la clase CSS `no-auto-splide` a través del parámetro `class` en el shortcode o HTML personalizado (donde está la clase `splide`). Normalmente, los activos de Splide sólo se ponen en cola cuando se encuentra un shortcode con el parámetro necesario en el contenido del post, pero puede habilitar Splide globalmente en **Fictioneer > General > Compatibilidad**.

```
[fictioneer_latest_stories count="6" splide="{'type': 'loop', 'gap': '1.5rem', 'autoplay': true, 'perPage': 2, 'breakpoints': {'767': {'perPage': 1}}}"]
```

<p align="center">
  <img src="repo/assets/shortcode_example_latest_stories_slider.gif?raw=true" alt="Vista previa del deslizador" />
</p>

#### Custom HTML Sliders

Si habilitas Splide globalmente (o tienes un shortcode de slider en la misma página), puedes usar el bloque HTML para crear tu propio Slider. Simplemente copia la base [structure](https://splidejs.com/guides/structure/) y añade las diapositivas que quieras, aunque tendrás que darles estilo tú mismo usando CSS personalizado. Inicialice el deslizador con el [atributo de datos JSON](https://splidejs.com/guides/options/#by-data-attribute), pero esta vez con comillas dobles como se muestra en el ejemplo. A diferencia de los shortcodes, las flechas de navegación están activadas por defecto pero pueden desactivarse con `"arrows:" false`.

```html
<section class="splide" data-splide='{"tipo": "bucle", "intervalo": 3000, "gap": "1.5rem", "autoplay": true, "perPage": 3, "breakpoints": {"767": {"perPage": 2, "arrows": false}, "479": {"perPage": 1}}}'>
  <div class="splide__track">
    <ul class="splide__list">
      <li class="splide__slide example-side">Deslizamiento 01</li>
      <li class="splide__slide example-side">Deslizamiento 02</li>
      <li class="splide__slide example-side">Deslizamiento 03</li>
      <li class="splide__slide example-side">Deslizamiento 04</li>
      <li class="splide__slide example-side">Deslizamiento 05</li>
      <li class="splide__slide example-side">Deslizamiento 06</li>
    </ul>
  </div>
</sección>
```

### Sidebar

Muestra la barra lateral del tema (no se muestra en ninguna parte por defecto). Requiere que la opción "Desactivar todos los widgets" esté desactivada. Tenga en cuenta que la barra lateral tiene casi ningún estilo.

* **name:** Nombre de la barra lateral en caso de que añadas alguna. Por defecto `fictioneer-sidebar`.

```
[fictioneer_sidebar]
```

```
[fictioneer_sidebar name="other-sidebar"]
```

## Elementor

Si tienes instalado el plugin Elementor, considera usar el plugin [Fictioneer 002 Elementor Control](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#recommended-must-use-plugins) si sólo lo necesitas para las plantillas de página Canvas. Si tienes la versión Pro y quieres usar el constructor de temas, esto puede no ser una opción, pero puedes personalizar las siguientes ubicaciones: `header`, `footer`, `nav_bar`, `nav_menu`, `mobile_nav_menu`, `story_header`, y `page_background`.

**Fondo de página**

Esta ubicación puede resultar confusa. El fondo de la página es en realidad un elemento separado en el tema, colocado bajo el contenedor de contenido e inaccesible. Esto permite varios cambios de estilo sin afectar al contenido, como clip-paths y máscaras aplicadas a un pseudo-elemento interno `::before`. Los estilos de página del Personalizador hacen un uso intensivo de esto. Si sobrescribe esta ubicación, debe asegurarse de moverla correctamente al fondo. El CSS base por defecto es el siguiente:

```css
.main__background {
  puntero-eventos: ninguno;
  selección de usuario: ninguna;
  posición: absoluta;
  inset: var(--page-inset-top, 0px) 0 0 0 0;
  z-index: 0;
  color de fondo: var(--page-bg-color);
  box-shadow: var(--page-box-shadow);
  contener: estilo de diseño;
}
```

**Pistas:**

* La ubicación `nav_bar` también sobrescribe la ubicación `nav_menu`.
* Sobrescribir la navegación es posible, pero generalmente es una mala elección vital.
* Elementor desactiva varios estilos de bloque de WordPress cuando se aplican a una página...
* ... que puede afectar a otros elementos, como la variante de cabecera Post Content.
* Utiliza las clases CSS `padding-[top|right|bottom|left]` para aplicar el relleno de la página del tema.
* Utiliza las clases CSS `bg-[50|100|200|...|800|900|950]` para forzar los colores de fondo del tema.
* Utiliza las clases CSS `fg-[100|200|...|800|900|950]` para forzar los colores del texto del tema.
* Utilice la clase CSS `max-site-width` para aplicar el ancho máximo del sitio del tema.
* Utilice la clase CSS `page-polygon` para aplicar la ruta de recorte de página elegida en el Personalizador (si existe).
* Utilice la clase CSS `header-polygon` para aplicar la ruta de recorte de cabecera elegida en el Personalizador (si existe).
* Las máscaras grunge, picos/pasos en capas, ringbook y cabecera/página ondulada no tienen clases de utilidad.
* Algunas de las [content utility CSS classes](#additional-css-classes) también funcionarán en Elementor.
* Puedes activar el menú móvil con un elemento de etiqueta que tenga el ID `mobile-menu-toggle`.
* Puedes seleccionar las fuentes del tema en Elementor, agrupadas en "Fictioneer".
* Puedes usar los shortcodes del tema en el widget shortcode.
* Los widgets de WordPress casi no tienen estilo porque el tema no los utiliza.
* La posición y el contenido previsto de la cabecera dependen de las opciones del personalizador.
* Los colores de texto globales de Elementor se han sobrescrito con los colores del tema.
* Elementor no entiende los modos de visualización del tema, los colores o la configuración HSL.
* Elementor hace que tu sitio sea más lento a menos que tengas un buen plugin de caché.
* Utilice las plantillas de página tipo Lienzo si desea personalizar drásticamente una página.
* Fictioneer no está pensado para editores de sitios web; hay limitaciones con las que tienes que vivir.

## Images & Media

Puedes cargar imágenes y otros archivos multimedia en la Biblioteca multimedia o directamente arrastrando y soltando en el editor, como se explica en la [documentación oficial](https://wordpress.org/support/article/media-library-screen/). Asegúrate de escalar y comprimir tus imágenes, porque 20 MB de arte en la cabecera ralentizarán tu sitio hasta el extremo. No hay mucho más que añadir, excepto un concepto vital que muchos nuevos propietarios de sitios web desconocen: **nunca pongas enlaces directos a las imágenes** a menos que tengas permiso explícito. Los enlaces normales en los que hay que hacer clic están bien.

**Hotlinking:** Se refiere a incrustar imágenes (u otros medios) que están alojados en un servidor externo, descargando el trabajo y robando ancho de banda. Esto puede suponer un alto coste para la víctima y meterte en problemas legales, aunque muchos servidores bloquean el hotlinking preventivamente por ese motivo. Imagínate que te pasa a ti, que tienes que pagar porque la gente cuelgue alegremente tus imágenes en todas partes (excluidas la copia y la recarga).

Si ahora estás preocupado, puedes tomarte un respiro. Lo más probable es que este problema no le afecte inmediatamente (o nunca), a menos que tenga previsto servir muchas imágenes por entrada, página o capítulo. Normalmente, los hosts gestionados también se encargan ellos mismos de esto para ahorrar ancho de banda, y las redes de distribución de contenidos (CDN) ofrecen sus propias soluciones si es necesario. Simplemente tenlo en cuenta y no te conviertas tú mismo en un infractor.

## Users & OAuth

Fictioneer ofrece la opción de habilitar la autenticación de usuario a través del protocolo OAuth 2.0, que probablemente conozcas como el molesto popover "Iniciar sesión con Google". Aquí no hay molestos popovers, pero la funcionalidad sigue siendo la misma. En lugar de nombre de usuario/email y contraseña, te autentificas con una cuenta de redes sociales: Discord, Google, Patreon o Twitch (si está [configurado](INSTALLATION.md#connections-tab)).

Esto crea y conecta automáticamente una cuenta de suscriptor, lo que facilita los comentarios y permite a los suscriptores realizar un seguimiento de su progreso con marcas de verificación, seguimientos y recordatorios. Además, lo más probable es que no necesites nada de eso ni los posibles quebraderos de cabeza que conlleva la gestión de usuarios. A menos que alojes docenas o cientos de historias, quizás de varios autores, es mejor que no lo hagas. Incluso entonces, ten en cuenta que un sitio comunitario requiere más recursos del servidor, lo que se traduce en un mal rendimiento o un mayor coste.

**Nota:** Asegúrate de tener una [Política de privacidad](PRIVACY.md) adecuada antes de permitir registros. Fictioneer no recopila datos indebidos y se trata de una acción informada y deliberada. Sin embargo, la privacidad siempre es un problema. Por eso, los suscriptores deberían tener la opción de autoeliminar sus datos y cuentas en cualquier momento, evitándote muchos problemas potenciales (es decir, "el derecho de borrado").

Si todo está configurado y el enlace no funciona, vacíe su estructura de enlaces permanentes en **Configuración > Enlaces permanentes** (simplemente guarde, no necesita cambiar nada).

## Checkmarks, Follows & Reminders

Las marcas de verificación, los seguimientos y los recordatorios son funciones de seguimiento del progreso para los suscriptores registrados que deben activarse en la configuración. Pero a menos que alojes docenas o cientos de historias, son más que nada un truco. Las publicaciones seriadas individuales no las necesitan y los lectores son bastante capaces de realizar el seguimiento sólo con las funciones del navegador. Consulta [Usuarios y OAuth](#users--oauth) para más consideraciones sobre el registro de usuarios. Si decide habilitar estas funciones, también debe asignar una página Bookshelf en la configuración del tema.

**Marcas de verificación:** Puedes marcar capítulos e historias como leídos, mostrándose estos últimos en tu lista de finalizados. Esto también se refleja en las listas de tarjetas con un icono de verificación en la esquina superior derecha.

**Seguir:** Puedes seguir historias para recibir notificaciones de actualizaciones en el sitio (hasta 16, no por correo electrónico) y verlas en tu lista de Seguidos. Esto también se refleja en las listas de tarjetas con un icono de estrella en la esquina superior derecha.

**Recordatorios:** Puedes marcar historias para leerlas más tarde y verlas en tu lista de Recordatorios. Esto también se refleja en las listas de tarjetas con un icono de reloj en la esquina superior derecha.

## Bookmarks

Los marcadores son una función de seguimiento del progreso que no requiere una cuenta. Sólo se procesan del lado del cliente y se almacenan localmente en el navegador, lo que significa que no están disponibles en diferentes navegadores en el mismo o en diferentes dispositivos. Sin embargo, esta comodidad puede conseguirse con una cuenta. Los marcadores guardan la posición de desplazamiento de un párrafo en un capítulo, con un extracto, una miniatura, el progreso en porcentaje y el color. Sólo puedes tener un marcador por capítulo, hasta un máximo de 50 marcadores en total.

## User Profile

El perfil de usuario predeterminado de WordPress se ha ampliado con una nueva sección de Fictioneer. También tiene la opción de reducir en gran medida el perfil de _suscriptores_ en **Fictioneer > General > Seguridad y privacidad > Reducir perfil de usuario suscriptor**, deshaciéndose de campos superfluos. Otros menús están ocultos por defecto, pero se recomienda utilizar el perfil del frontend con la plantilla de página Perfil de usuario.

**Suscriptores:**

* **Huella digital:** Hash de usuario único utilizado para distinguir a los comentaristas con el mismo nick. La suplantación de identidad existe.
* **Banderas de perfil:** Casillas de verificación para usar siempre un gravatar, desactivar tu avatar, ocultar tu insignia y persistir en la notificación de respuesta a comentarios.
* **OAuth 2.0 Conexiones:** Conectar o desconectar cuentas de medios sociales como inicios de sesión: Discord, Google, Patreon y Twitch.
* **Datos:** Resumen de los datos enviados por el usuario, como comentarios, y la opción de eliminarlos.

**Autores:**

**Página de autor:** Muestra el contenido de una página seleccionada en tu perfil de autor en lugar de la información biográfica.
* **Mensaje de soporte:** Personaliza el mensaje sobre los enlaces de soporte en los capítulos.

**Moderadores:**

* **Banderas de moderación:** Casillas de verificación para deshabilitar determinadas capacidades del usuario, como el avatar o los comentarios.
* **Mensaje de moderación:** Mensaje personalizado que se muestra en el perfil del usuario. Esto puede ser algo agradable.

**Administradores:**

* **Sustituir insignia:** Sustituir la insignia de un usuario con una cadena personalizada. No intimidar.
* URL externa del avatar:** Enlace externo a una imagen de avatar alojada en una CDN.

## Color Variables

Puede que se pregunte a qué se refieren los números 50-950 de las secciones de color del personalizador. Se refieren a los nombres de las variables que contienen el color correspondiente, como `var(--rojo-500)` o `var(--fg-500)`. Cada color es en realidad una función que se adapta a la configuración del usuario en el frontend (saturación, luminosidad, etc.). Así que se recomienda usar estos colores, porque un simple código hexadecimal no se preocupa por las preferencias del usuario.

Si alguna vez quieres aplicar colores con CSS, puedes hacerlo así: `color: var(--fg-500);` o `background-color: var(--bg-700);`. Las opciones de color en el editor de entradas ya están contempladas, por lo que no debes preocuparte por ellas. Los prefijos más comunes son `--bg-#` para el fondo, `--fg-#` para el primer plano (texto), `--primary-#` para los enlaces, así como `--red-#` y `--green-#`. Puede encontrar más información en [_properties.scss](src/scss/common/_properties.scss).

## Common Problems

Problemas habituales y cómo evitarlos o solucionarlos.

### Missing Blocks

Esto no es un error sino intencionado, el tema solo permite bloques que estén correctamente integrados. Pero puedes habilitar el resto en **Fictioneer > General > Compatibilidad > Habilitar todos los bloques Gutenberg**. No hay garantía de que funcionen o se vean bien.

### Reserved URL Slugs

Hay algunos slugs de URL reservados que no debes usar en los permalinks, de lo contrario te encontrarás con páginas de error 404 o bucles de redirección infinita. Aunque poco probable, esto podría ocurrir si eliges títulos de post similares en nombre a estos slugs. Puedes cambiar los permalinks en la [barra lateral de configuración](https://wordpress.org/support/article/settings-sidebar/) si se diera el caso. Slugs reservados:

* oauth2
* descargar-epub
* fictioneer-logout

### Some block settings look bad or do nothing!

Algunos ajustes de bloque carecen de estilo en el tema o se han desactivado porque no funcionan bien con el diseño. Por ejemplo, el bloque Últimas entradas ignora la configuración de miniaturas. Los tamaños y colores de fuente personalizados sólo deberían usarse en encabezados o párrafos.