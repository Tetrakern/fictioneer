# Installation

Esta guía está escrita principalmente para las personas que nunca han tenido su propio sitio de WordPress antes y pueden no tener las habilidades para resolver esto por sí mismos. Siéntase libre de saltar por delante. Dicho esto, todavía hay algunas partes de interés para los veteranos en lo que respecta al tema.

Haga clic en el conmutador de esquema de la esquina superior derecha para ver el índice.

## Choosing a Host

En primer lugar, tienes que elegir un host para tu sitio, el lugar donde "vive" tu sitio. Puede que ésta sea la parte más difícil, porque las malas elecciones son molestas de arreglar y cuestan dinero. Para ello, le animamos a que investigue por su cuenta: [Online Media Masters](https://onlinemediamasters.com/) es un buen punto de partida. Si te sientes completamente perdido, pedir ayuda está totalmente justificado.

Su elección se reducirá en última instancia a dos escuelas: alojamiento gestionado o no. El alojamiento gestionado elimina la carga de tener que, bueno, gestionar su servidor. Los problemas de configuración, mantenimiento, seguridad y rendimiento están cubiertos por el proveedor, pero a un precio. Por ejemplo, en [WordPress.com](https://wordpress.com/pricing/) necesitarías al menos el plan Business para utilizar el tema Fictioneer. El alojamiento no gestionado es más asequible y menos restrictivo, pero necesitas un poco de conocimientos técnicos o alguien que te ayude.

Si los costes de alojamiento son demasiado elevados para usted solo, también existe la opción de compartir un sitio con otros autores y dividir la factura. Por lo general, el contrato lo firma el administrador. Sólo tienes que asegurarte de que confías en todos y dejar por escrito las obligaciones y derechos de todos los participantes. Prepárate siempre para las consecuencias.

## Installing WordPress

El proceso de instalación de WordPress está [documentado en el sitio oficial](https://wordpress.org/support/article/how-to-install-wordpress/) y en muchas guías sólo hay que hacer una rápida búsqueda. Hoy en día, la mayoría de los alojamientos ofrecen también un servicio de instalación con un solo clic. Ten en cuenta que este último suele venir con plugins preinstalados de los que quizá quieras deshacerte, sobre todo plugins de análisis que suelen violar las leyes de privacidad de datos.

Fictioneer se utiliza mejor en una instalación nueva debido a su complejidad y posibles conflictos con los plugins existentes o personalizaciones. Lo que no significa que no se pueda cambiar o migrar, pero sería una odisea. Por ejemplo, Fictioneer tiene tipos de post personalizados para historias y capítulos, por lo que tendrías que [convertir los posts existentes](https://wordpress.org/plugins/post-type-switcher/) o subirlos de nuevo (lo que disociaría todos los comentarios). También tienen varios ajustes adicionales, lo que hace que los scripts de conversión automática sean arriesgados. Dependiendo del número de entradas que tengas, esto puede llevar un tiempo.

### Configuring WordPress

¿Todo instalado? Dirígete a **[Configuración](https://wordpress.org/support/article/administration-screens/#general)** en el panel de administración para configurar tu sitio. Puedes seguir una guía, pero todo esto debería ser bastante obvio. Para trabajar con el tema, lo que más te interesa son los submenús **Lectura**, **Discusión** y **Permalinks**.

* **Lectura:** Si quieres una página estática como en la demo, puedes configurar esto aquí. Por supuesto, necesitas [crear las páginas](https://wordpress.org/support/article/pages/) para el blog y la portada primero. Es mejor utilizar la plantilla "Página sin título" o "Página de historia" (para sitios de una sola historia). Mantén el número de entradas de blog y de noticias entre 8 y 20.

* **Discusión:** La mayor parte de esto depende de usted, pero el número y el orden de los comentarios no necesariamente se comportan como cabría esperar. Los comentarios siempre están anidados en el tema, independientemente de la casilla de verificación, pero la profundidad es honrado y debe estar en cualquier lugar hasta 5. Romper los comentarios en páginas con 8 a 50 comentarios cada uno, la primera página que se muestra por defecto, y los comentarios más recientes en la parte superior. El tema realmente no funciona bien con cualquier otra cosa, pero le invitamos a probar.

    * **Claves de comentarios no permitidos:** Para una protección sencilla pero fiable contra el spam de comentarios, se recomienda utilizar la [lista negra compilada por slorp](https://github.com/splorp/wordpress-comment-blacklist). Sólo tienes que copiar el contenido de la lista negra en el campo [Disallowed Comment Key](https://wordpress.org/support/article/comment-moderation/#comment-blocking). Comprueba de vez en cuando la papelera de comentarios, ya que puede dar lugar a falsos positivos. También puedes buscar listas menos restrictivas.

* **Permalinks:** Quieres que la estructura de permalink se establezca en "Post name". Como nota al margen, cada vez que algunas páginas no se muestran a pesar de que claramente debería, volver aquí y guardar para actualizar la estructura de enlace permanente. Usted se sorprendería de cuántos problemas que resuelve, incluyendo la autenticación OAuth no funciona.

* **Imagen predeterminada de Open Graph:** Sólo se utiliza cuando se activan las funciones de SEO y no se está ejecutando ningún plugin de SEO (conocido). Esta imagen se mostrará en los resultados de los motores de búsqueda y en las redes sociales si no se proporciona ninguna otra imagen en las entradas individuales, como las imágenes de portada. Se puede configurar en **Apariencia > Personalizar > Identidad del sitio**.

**Sitios web de los autores:** Técnicamente no es obligatorio, pero los autores pueden rellenar el campo del sitio web en su perfil. Estos se añaden como metaetiquetas de autor Open Graph utilizadas por los motores de búsqueda e incrustaciones de medios sociales. Si se deja en blanco, la página de autor generada del sitio se utilizará en su lugar, que podría ser lo que quieres de todos modos.

### Securing WordPress & Browser Caching

Puede mejorar enormemente la seguridad y el rendimiento de su sitio añadiendo políticas al archivo **.htaccess** situado en el directorio raíz de WordPress. Los planes de alojamiento gestionado normalmente lo hacen por usted (si lo solicita). Haga una copia de seguridad y añada las siguientes líneas en cualquier lugar antes de `# BEGIN WordPress` o después de `# END WordPress`. Si algo sale mal, simplemente elimina todo de nuevo o restaura la copia de seguridad. También puedes usar un plugin (de caché) que lo haga por ti. Esto es sólo lo básico, mucho más es posible, pero por favor consulte una guía adecuada.

  <detalles>
    <summary>Políticas de ejemplo</summary><br>

```
# === BEGIN FICTIONEER ===

# Disable directory browsing
Opciones -Índices

# Deny POST requests using HTTP 1.0
<IfModule mod_rewrite.c>
  RewriteCond %{THE_REQUEST} ^POST(.*)HTTP/(0\.9|1\.0)$ [NC]
  RewriteRule .* - [F,L]
</IfModule>

# protect wp-config.php
<archivos wp-config.php>
  orden permitir,denegar
  negar de todo
</archivos>

# Security policies
<ifModule mod_headers.c>
  Conjunto de cabeceras Strict-Transport-Security "max-age=31536000" env=HTTPS
  Header set X-XSS-Protection "1; mode=block"
  Conjunto de cabeceras X-Content-Type-Options nosniff
  Conjunto de cabeceras X-Frame-Options SAMEORIGIN
  Conjunto de cabeceras Referrer-Policy: strict-origin-when-cross-origin
  Header set Cross-Origin-Opener-Policy "same-origin"
  Encabezado establecido Cross-Origin-Resource-Policy "same-site"
  Header set Cross-Origin-Embedder-Policy "require-corp; report-to='default'"
  Header unset X-Powered-By
</ifModule>

# Add file types
AddType application/x-font-ttf .ttf
AddType application/x-font-opentype .otf
AddType application/x-font-woff .woff
AddType application/x-font-woff2 .woff2
AddType image/svg+xml .svg

# Enable compression
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/xml
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/x-javascript
  AddOutputFilterByType DEFLATE application/json
  AddOutputFilterByType DEFLATE application/x-font-opentype
  AddOutputFilterByType DEFLATE application/x-font-truetype
  AddOutputFilterByType DEFLATE application/x-font-ttf
  AddOutputFilterByType DEFLATE font/opentype
  AddOutputFilterByType DEFLATE font/otf
  AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>

# Browser cache policies
<IfModule mod_expires.c>
  CaducaActivo el

  # Iconos
  ExpiresByType image/x-icon "acceso más 1 año"
  ExpiresByType image/vnd.microsoft.icon "acceso más 30 días"

  # Imágenes
  ExpiresByType image/jpg "acceso más 1 año"
  ExpiresByType image/jpeg "acceso más 1 año"
  ExpiresByType image/png "acceso más 1 año"
  ExpiresByType image/gif "acceso más 1 año"
  ExpiresByType image/webp "acceso más 1 año"
  ExpiresByType image/avif "acceso más 1 año"
  ExpiresByType image/tiff "acceso más 1 año"
  ExpiresByType image/svg+xml "acceso más 1 año"

  # Audio/Video
  ExpiresByType audio/ogg "acceso más 1 año"
  ExpiresByType audio/mpeg "acceso más 1 año"
  ExpiresByType audio/flac "acceso más 1 año"
  ExpiresByType audio/mp3 "acceso más 1 año"
  ExpiresByType video/ogg "acceso más 1 año"
  ExpiresByType video/mp4 "acceso más 1 año"
  ExpiresByType video/webm "acceso más 1 año"
  ExpiresByType video/mpeg "acceso más 1 año"
  ExpiresByType video/quicktime "acceso más 1 año"

  # CSS/JS
  ExpiresByType text/css "acceso más 1 mes"
  ExpiresByType text/javascript "acceso más 1 mes"
  ExpiresByType application/javascript "acceso más 1 mes"
  ExpiresByType application/x-javascript "acceso más 1 mes"

  # Fuentes
  ExpiresByType application/x-font-ttf "acceso más 1 año"
  ExpiresByType application/x-font-woff "acceso más 1 año"
  ExpiresByType application/x-font-woff2 "acceso más 1 año"
  ExpiresByType application/x-font-opentype "acceso más 1 año"

  # Otros
  ExpiresByType application/pdf "acceso más 1 día"
  ExpiresByType application/epub+zip "acceso más 1 día"
  ExpiresByType application/x-mobipocket-ebook "acceso más 1 día"
</IfModule>

# === END FICTIONEER ===
```

  </detalles>

## Legal Considerations

No hay mucho que considerar aparte de la cuestión de la [privacidad de los datos](https://wordpress.com/go/website-building/how-to-write-and-add-a-privacy-policy-to-your-wordpress-site/), que depende de tu país de residencia y de dónde esté ubicado tu servidor de alojamiento. Sin embargo, para evitar cualquier problema legal, debes asumir que se aplican las leyes más estrictas: el [GDPR](https://en.wikipedia.org/wiki/General_Data_Protection_Regulation) y la [CCPA](https://en.wikipedia.org/wiki/California_Consumer_Privacy_Act). Fictioneer cumple con ambas a menos que cambies las cosas, pero también necesitas añadir una [Política de Privacidad](PRIVACY.md). Y olvídate de Google Analytics o de las fuentes.

## How to Install/Update the Fictioneer Theme

![Upload Theme Screen](repo/assets/appearance_upload_theme.jpg?raw=true)

**¡ATENCIÓN! Descargue siempre el archivo zip de la [última versión estable](https://github.com/Tetrakern/fictioneer/releases); NO el código fuente y NO a través del botón verde "Código" de GitHub - a menos que sea un desarrollador y sepa lo que está haciendo. Asegúrate de que el directorio extraído se llama "fictioneer".**

Una vez que hayas configurado tu sitio WordPress, puedes instalar el tema. Como Fictioneer no está disponible en la biblioteca oficial de temas, tendrás que hacerlo manualmente. O bien subiendo los archivos del tema *sin empaquetar* al directorio `/wp-content/themes/fictioneer` mediante FTP o subiendo el archivo `.zip` en el panel de administración en **Apariencia > Temas > Añadir nuevo > Subir tema**.

Cuando hayas terminado, activa el tema en **Apariencia > Temas**. Si quieres usar un tema hijo (https://developer.wordpress.org/themes/advanced-topics/child-themes/), que se instala de la misma manera, actívalo en su lugar (necesitas tanto el tema principal como el hijo). A continuación, dirígete a la nueva página del menú Fictioneer en la barra lateral de administración. Aquí tienes que [configurar](#Cómoconfigurar-el-tema-de-ficción) el tema. También puedes [personalizar](#Cómo-personalizar-el-tema-de-fictioneer) el aspecto.

### Updating the Theme

Actualizar el tema funciona igual que instalarlo. Si se hace en el panel de administración, se le advertirá de que el tema ya está instalado y se le ofrecerá una comparación rápida, pidiéndole que confirme la sobrescritura. Asegúrese de que sigue cumpliendo todos los requisitos, es decir, las versiones de WordPress y PHP. Puedes encontrar esta información en la pestaña de información de la [pantalla de salud del sitio](https://wordpress.org/support/article/site-health-screen/).

Tenga en cuenta que cualquier cambio realizado en los archivos del tema será deshecho - lo que no debería haber hecho en primer lugar. Para evitar este problema, utilice siempre un [tema hijo](https://developer.wordpress.org/themes/advanced-topics/child-themes/) para las modificaciones. No obstante, se conservarán las opciones del tema y la configuración del personalizador.

### Issue: Jetpack Boost

Jetpack es un plugin de WordPress que a veces se impone a los usuarios. [Jetpack Boost](https://jetpack.com/support/jetpack-boost/) es una característica opcional del plugin que en ocasiones se activa automáticamente y que tiene como objetivo hacer que su sitio sea más rápido. Sin embargo, a menudo causa problemas al romper el sitio porque concatena scripts y estilos sin tener en cuenta las consecuencias. Este problema también puede ocurrir con otros plugins de "optimización" demasiado entusiastas.

Para resolverlo, tiene dos opciones: desactivar Jetpack Boost o añadir los scripts del tema a la lista de exclusión. Es posible que algunos de ellos funcionen al concatenarlos, pero no se han probado.

```
fictioneer-dynamic-scripts, fictioneer-application-scripts, fictioneer-lightbox, fictioneer-mobile-menu-scripts, fictioneer-consent-scripts, fictioneer-chapter-scripts, fictioneer-dmp, fictioneer-tts-scripts, fictioneer-story-scripts, fictioneer-user-scripts, fictioneer-user-profile-scripts, fictioneer-bookmarks-scripts, fictioneer-follows-scripts, fictioneer-checkmarks-scripts, fictioneer-reminders-scripts, fictioneer-comments-scripts, fictioneer-ajax-comments-scripts, fictioneer-ajax-bookshelf-scripts, fictioneer-dev-scripts, fcnen-frontend-scripts, fcnmm-script, fictioneer-child-script
```

### Optional: Additional Plugins

El [ecosistema de plugins](https://wordpress.org/plugins/) de WordPress es vasto y a menudo confuso. Hay plugins para casi todo, en variantes gratuitas o premium o "freemium". A menudo se encuentran artículos sobre plugins "imprescindibles", pero es aconsejable cuestionarlos. Demasiados plugins pueden ralentizar tu sitio, abrir vulnerabilidades o entrar en conflicto con el tema. Fictioneer está diseñado como una solución independiente y técnicamente funciona sin plugins adicionales. Sin embargo, nunca hay nada completo, así que aquí tienes algunos plugins a tener en cuenta.

* [Autoptimize](https://wordpress.org/plugins/autoptimize/): Plugin de optimización para acelerar su sitio. Se utiliza mejor por su agregación y aplazamiento de recursos estáticos, como estilos y scripts, resolviendo problemas de caché del navegador por el camino. Las otras opciones son buenas si no están ya cubiertas en otra parte.

  <detalles>
    <summary>Configuración de ejemplo</summary><br>
    <blockquote>
      Se supone que las opciones que faltan están desactivadas, vacías o por defecto.<br><br>
      <strong>[JS, CSS y HTML] Opciones de JavaScript:</strong>
      <ul>
        <li>- [x] ¿Optimizar el código JavaScript?</li>
        <li>- [x] ¿Agregar archivos JS?</li>
      </ul><br>
      <strong>[JS, CSS y HTML] Opciones CSS:</strong>
      <ul>
        <li>- [x] ¿Optimizar código CSS?</li>
        <li>- [x] ¿Agregar archivos CSS?</li>
        <li>- [x] Generar datos: ¿URIs para imágenes?</li>
      </ul><br>
      <strong>[JS, CSS & HTML] Opciones varias:</strong>
      <ul>
        <li>- [x] ¿Guardar script/css agregados como archivos estáticos?</li>
        <li>- [x] ¿Activar las fallbacks 404?</li>
        <li>- [x] ¿Optimizar también para los editores/administradores que han iniciado sesión?</li>
        <li>- [x] ¿Desactivar la lógica de compatibilidad extra?</li>
      </ul><br>
      <strong>[Extra] Autooptimizaciones extra:</strong>
      <ul>
        <li>- [x] Google Fonts: Dejar como está</li>
        <li>- [x] Eliminar emojis</li>
      </ul>
    </blockquote>
  </detalles>

* [Cloudinary](https://wordpress.org/plugins/cloudinary-image-management-and-manipulation-in-the-cloud-cdn/): Gran CDN y optimizador de imágenes "plug-and-play" con un generoso plan gratuito. Descargar tus imágenes en una red de distribución de contenidos mejora el rendimiento y los tiempos de carga. Además, tus imágenes tendrán el tamaño y la compresión adecuados.

  <detalles>
    <summary>Configuración de ejemplo</summary><br>
    <p>Siga la <a href="https://cloudinary.com/documentation/wordpress_integration">guía oficial</a> para configurar su cuenta Cloudinary y el plugin. No es necesario "registrar" el CND con otros plugins de optimización o caché - simplemente funcionará.</p> <p>.
    <blockquote>
      Se supone que las opciones que faltan están desactivadas, vacías o por defecto.<br><br>
      <strong>[Ajustes generales] Ajustes de sincronización de la biblioteca multimedia:</strong>
      <ul>
        <li>- [x] Método de sincronización: Sincronización automática</li>
      </ul><br>
      <strong>[Configuración general] Ruta de la carpeta Cloudinary:</strong><br>
      Para mantener el orden, utilice un nombre de carpeta relacionado con su sitio web.
      <strong>[Configuración general] Almacenamiento:</strong>
      <ul>
        <li>- [x] Cloudinary y WordPress</li>-[x
      </ul><br>
      <strong>[Ajustes de imagen] Optimización de imagen:</strong>
      <ul>
        <li>- [x] Optimizar las imágenes en mi sitio.</li>
        <li>- [x] Formato de imagen: Auto</li>
        <li>- [x] Calidad de imagen: Auto</li>
      </ul><br>
      <strong>[Carga perezosa] Carga perezosa:</strong>
      <ul>
        <li>- [x] Habilitar la carga lenta</li>
        <li>- [x] Umbral de carga perezosa: 100px</li>
        <li>- [x] Color del precargador: ¡Tú decides!</li>
        <li>- [x] Animación previa a la carga: ¡Tú decides!</li>
        <li>- [x] Tipo de generación del marcador de posición: ¡Tú decides!</li>
        <li>- [x] Ajustes DPR: Auto (2x)</li>
      </ul><br>
      <strong>[Responsive images] Puntos de interrupción:</strong>
      <ul>
        
      </ul>
    </blockquote>
  </detalles>

* [Cloudflare](https://wordpress.org/plugins/cloudflare/): Red global de entrega de contenidos diseñada para hacer que su sitio sea seguro, privado, rápido y fiable. Se puede utilizar para almacenar en caché o para mejorar aún más un plugin de caché. Lamentablemente, la configuración no es trivial y debe consultar guías específicas o pedir ayuda.

  <detalles>
    <summary>Consideraciones sobre la caché</summary><br>
    <p>Cloudflare puede ser problemático si quieres sacar provecho de la opción "Cachear todo" porque sin un plan de pago, no puedes hacer excepciones para usuarios registrados. Esto significa que los visitantes podrían ver contenido personalizado del primer usuario que rellene la caché, ¡nada bueno! Imagina que los datos de tu cuenta se filtraran así. Tampoco coopera fácilmente con las soluciones de caché in situ.</p> <p>
    <p>Dicho esto, ¡el nivel gratuito puede ser persuadido! Deshazte del plugin oficial e instala <a href="https://wordpress.org/plugins/wp-cloudflare-page-cache/">Super Page Cache for Cloudflare</a> en su lugar. Lo mismo que antes, consulte las guías adecuadas. Asegúrate de tener la siguiente configuración y prepárate para que no funcione. Pruébalo</p> <p>
    <blockquote>
      <strong>[Plugin: Caché] No almacenar en caché los siguientes contenidos dinámicos:</strong>
      <ul>
        <li>- [x] Página 404 (is_404)</li>
        <li>- [x] Feeds (is_feed)</li>
        <li>- [x] Páginas de búsqueda (is_search)</li>
        <li>- [x] Peticiones Ajax</li>
        <li>- [x] WP JSON endpoints</li>
      </ul><br>
      <strong>[Plugin: Caché] Impide que se almacenen en caché los siguientes URI:</strong><br>
      &numsp;Añadir a valores por defecto. Los fragmentos URI "cuenta" y "estantería" pueden diferir en su sitio (ya que puede asignarles un nombre).<br>
      &numsp;<code>/oauth2*</code><br>
      &numsp;<code>/download-epub*</code><br>
      &numsp;<code>/cuenta*</code><br>
      &numsp;<code>/estantería*</code><br>
      &numsp;<code>/*commentcode=*</code><br><br>
      <strong>[Fictioneer: General] Asignación de páginas:</strong>
      <ul>
        <li>- [ ] Cuenta: Ninguna (perfil por defecto del salpicadero)</li>
        <li>- [x] Estantería: Página con la plantilla Bookshelf AJAX (si la necesitas)</li>
      </ul><br>
      <strong>[Fictioneer: General] Comentarios:</strong>
      <ul>
        <li>- [x] Habilitar el envío de comentarios AJAX</li>
      </ul><br>
      <strong>[Fictioneer: General] Seguridad y privacidad:</strong>
      <ul>
        
      </ul><br>
      <strong>[Fictioneer: General] Compatibilidad:</strong>
      <ul>
        <li>- [x] Activar el modo de compatibilidad de caché</li>.
        <li>- [x] Habilitar autenticación de usuario AJAX</li>
        <li>- [x] Habilitar formulario de comentarios AJAX (mejor rendimiento) ... o ... sección de comentarios (mejor compatibilidad)</li>
      </ul>
    </blockquote>
    <p>Opcional: Instalar <a href="https://wordpress.org/plugins/wp-super-cache/">WP Super Cache</a> con la configuración extrema. No puede cachear erróneamente páginas dinámicas si no hay ninguna!</p> <p>
  </detalles>

* [WPS Limit Login](https://wordpress.org/plugins/wps-limit-login/): Te protege de ataques de fuerza bruta limitando el número de intentos de inicio de sesión dentro de un cierto período de tiempo. El plugin hermano [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/) mueve toda la página de inicio de sesión a una nueva URL, si quieres ir un paso más allá.

  <detalles>
    <summary>Autenticación de usuario</summary><br>
    <p>Fictioneer no tiene un formulario de inicio de sesión frontend y la página de inicio de sesión no es recomendable para los suscriptores, por lo que ocultarla sirve como capa de seguridad adicional. Ten en cuenta que el sistema opcional de autenticación OAuth 2.0 a través de Discord, Google, etc. no se ve afectado por estos plugins.</p> <p>.
  </detalles>

* Sucuri Security - Auditing, Malware Scanner and Hardening](https://wordpress.org/plugins/sucuri-scanner/): La versión gratuita está pensada para complementar su postura de seguridad y viene con endurecimiento, escáner de malware, comprobación de la integridad de los archivos principales, registro de eventos, alertas por correo electrónico de problemas importantes y mucho más.

  <detalles>
    <sumario>Notas</sumario><br>
    <p>No hay mucho que fastidiar, pero deberías consultar una guía adecuada para tu propia tranquilidad. Porque Sucuri tiene tendencia a ser demasiado entusiasta con las advertencias que asustan y hasta que no configures una lista blanca, verás muchos falsos positivos. Más vale prevenir que curar.</p> <p>
    <blockquote>
      <strong>Falsos positivos típicos:</strong><br>
      &numsp;<code>error_log-* (potencialmente cualquier log de cualquier plugin)</code><br>
      &numsp;<code>.htaccess.bk (copia de seguridad generada de .htaccess)</code><br>
    </blockquote>
  </detalles>

* [UpdraftPlus](https://wordpress.org/plugins/updraftplus/): Uno de los plugins de copia de seguridad más populares y prácticos. Si tu host no ofrece copias de seguridad o quieres mantener el control, esta es una buena opción para mantener tu sitio a salvo en caso de desastre.

  <detalles>
    <summary>Por qué quieres copias de seguridad</summary><br>
    <p>Por citar la propia premonición del plugin: "Puede llegar el día en que te hackeen, en que algo vaya mal con una actualización, se caiga tu servidor o quiebre tu empresa de hosting: sin buenas copias de seguridad, lo pierdes todo." La versión gratuita es perfectamente adecuada, permitiéndote programar copias de seguridad diarias guardadas directamente en un destino remoto de tu elección.</p> <p><strong>Seguridad</strong>.
  </detalles>

* EWWW Image Optimizer](https://wordpress.org/plugins/ewww-image-optimizer/): Un plugin de optimización para escalar, comprimir y (opcionalmente) convertir correctamente tus imágenes. Los archivos de gran tamaño reducen la velocidad de tu sitio web y su posición en las búsquedas. Redundante si usas un CDN de imágenes como Cloudinary, pero pueden funcionar juntos.

  <detalles>
    <summary>Configuración de ejemplo</summary><br>
    <p>De hecho, no necesitas este tipo de plugin en absoluto si prestas un mínimo de atención a las imágenes que subes. Uno de los errores más comunes pero fáciles de solucionar es subir imágenes demasiado grandes. Obviamente, si tu imagen de cabecera ocupa 20 MB, tu tiempo de carga se irá al garete. Su sitio será aún más rápido sin la sobrecarga de este plugin si usted acaba de pre-optimizar sus imágenes.</p> <p>
    <blockquote>
      Siga la guía de configuración inicial y, a continuación, diríjase a <strong>Configuración > EWWW Image Optimizer</strong> para revisar la configuración. También eche un vistazo a la <a href="https://docs.ewww.io/article/4-getting-started">documentación oficial</a>. Asuma que las opciones que faltan están desactivadas, vacías o por defecto.<br><br>
      <strong>Configuración básica:</strong>
      <ul>
        <li>- [x] Quédate con el modo libre por ahora</li>
        <li>- [x] Eliminar metadatos</li>
        <li>- [x] Cambiar el tamaño de las imágenes: 1920|1920 (3840|2160 si necesitas imágenes 4k)</li>
        
        <li>- [x] Lazy Load: mejora el tiempo de carga real y percibido...</li>
        <li>- [ ] Carga perezosa: Escalado automático (OFF)</li>- [ ] Carga perezosa.
        <li>- [ ] Conversión WebP (APAGADO - más pequeño pero poco fiable en calidad)</li>
      </ul>
    </blockquote>
  </detalles>

* [Filtro de carga de plugins](https://wordpress.org/plugins/plugin-load-filter/): Este plugin te permite deshabilitar otros plugins basándose en condiciones específicas, como el tipo de entrada. Esto es útil si tienes muchos plugins que sólo necesitas para páginas seleccionadas, ayudando a evitar degradar el rendimiento de tu sitio con una sobrecarga innecesaria.

### Optional: Caching

Técnicamente no es más que otro plugin, pero hará que tu sitio sea mucho más rápido. [Caching](https://wordpress.org/support/article/optimization-caching/) guarda tus posts y páginas como archivos estáticos para ser servidos más tarde en lugar de renderizarlos de nuevo en cada petición. Los visitantes ven el mismo contenido de todos modos, así que ¿por qué desperdiciar recursos? Sólo los usuarios registrados pueden tener contenido individual que no debe ser almacenado en caché, como el perfil de su cuenta. A continuación se presentan algunos plugins de caché que han demostrado funcionar bien con el tema. Hágalo después de configurar su sitio.

**Nota:** Las cachés deben ser purgadas de vez en cuando, especialmente después de actualizar el tema, la configuración o los plugins. De lo contrario, tu sitio podría mostrar páginas obsoletas. Con plugins *conocidos*, Fictioneer purga automáticamente las cachés de las entradas cuando publicas o editas contenido. Otros plugins de caché requieren algún código personalizado o necesitan ser purgados manualmente. Inconveniente, pero factible.

**La mayoría de los plugins de caché excluyen automáticamente las páginas con query vars (`/?foo=bar`), porque suelen tener contenido dinámico. Sin embargo, hay algunos query vars que pueden ser cacheados con seguridad si el plugin los reconoce como URLs separadas: `pg` (página), `tab`, y técnicamente `order` también. Es posible que tenga aún más.

**Minificar CSS/JS/HTML:** Aunque esto puede suponer un *pequeño* aumento del rendimiento, también suele provocar que los scripts no funcionen, que falten fuentes y que se produzcan problemas de visualización. Cloudflare es conocido por romper las propiedades CSS con una minificación demasiado entusiasta, LiteSpeed tiende a desordenar las rutas relativas de archivos en el CSS, y la purga de espacios en blanco presumiblemente "redundantes" del HTML puede hacer que desaparezcan los espacios entre elementos o palabras. Puedes probarlo, pero vigila los resultados y, lo que es más importante, la consola en busca de errores.

**Regla de oro:** Si falta algo o está mal colocado, ¡purga la caché! ¿El orden de los capítulos es incorrecto? Purgue la caché. ¿Colecciones obsoletas? Purgue la caché. ¿Página en rojo intermitente? Eso es, ¡llama a un exorcista y purga la caché!

* [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/): Hecho por [Automattic](https://automattic.com/), uno de los principales contribuidores de WordPress el *software* y propietario de WordPress.com el *servicio* (no los confundas), este plugin de caché gratuito es una gran elección si quieres algo sencillo y fiable. También es completamente gratuito.

  <detalles>
    <summary>Configuración recomendada</summary><br>
    <p>Esta es la configuración avanzada "más segura" en el sentido de que usted no necesita meterse con los archivos del servidor. El modo experto es un poco más rápido y en realidad no es complicado, pero si los términos ".htaccess" y "mod_rewrite" te hacen sentir mareado, estás perfectamente bien con el modo simple.
    <blockquote>
      Se supone que las opciones que faltan están desactivadas, vacías o por defecto.<br><br>
      <strong>[Avanzado] Almacenamiento en caché:</strong>
      <ul>
        <li>- [x] Activar almacenamiento en caché</li>
      </ul><br>
      <strong>[Avanzado] Método de entrega de caché:</strong>
      <ul>
        <li>- [x] Simple</li>
      </ul><br>
      <strong>[Avanzado] Varios:</strong>
      <ul>
        <li>- [x] Restricciones de caché: Desactivar el almacenamiento en caché para los visitantes registrados</li>
        <li>- [x] No almacenar en caché páginas con parámetros GET.</li>- [x] No almacenar en caché páginas con parámetros GET.
        
        <li>- [x] Reconstrucción de caché. Sirve un archivo de supercache a usuarios anónimos mientras se genera un archivo nuevo.</li>- [x] Reconstrucción de caché.
      </ul><br>
      <strong>[Avanzado] Avanzado:</strong>
      <ul>
        <li>- [x] Comprobaciones extra de la página de inicio.</li>
        <li>- [x] Sólo actualizar la página actual cuando los comentarios made.</li>
        <li>- [x] Listar las páginas más recientes en caché de esta página.</li>- [x] Listar las páginas más recientes en caché de esta página.</li>- [x] Listar las páginas más recientes en caché de esta página.</li>- [x] Listar las páginas más recientes en caché de esta página.
      </ul><br>
      <strong>[Avanzado] Tiempo de caducidad y recogida de basura:</strong>
      <ul>
        <li>- [x] Tiempo de espera de la caché: 7200</li>
        <li>- [x] Temporizador: 600</li>
      </ul><br>
      <strong>[Avanzado] Nombres de archivo aceptados y URI rechazados:</strong>
      <ul>
        <li>- [x] Feeds</li>
        <li>- [x] Páginas de búsqueda</li>
      </ul><br>
      <strong>[Avanzado] Cadenas de URL rechazadas:</strong><br>
      &numsp;Los fragmentos URI "cuenta" y "estantería" pueden diferir en su sitio (ya que puede asignarles un nombre).<br>
      &numsp;<code>/oauth2</code><br>
      &numsp;<code>/download-epub</code><br>
      &numsp;<code>/cuenta</code><br>
      &numsp;<code>/estantería</code><br>
      &numsp;<code>/wp-json/storygraph</code><br>
      &numsp;<code>/wp-json/fictioneer</code>
    </blockquote>
  </detalles>

  <detalles>
    <summary>Configuración extrema</summary><br>
    <p>Esta es la configuración más "agresiva" pensada para sitios de miembros en hosts más baratos, por ejemplo, sitios con muchas peticiones simultáneas de visitantes registrados a los que normalmente no se les servirían archivos supercached. La generación de un gran número de páginas individuales en un corto espacio de tiempo puede saturar un servidor, provocando errores de tiempo de espera. Es poco probable que se encuentre con este problema si no tiene miles de visitantes diarios. Pero en ese caso, sólo tiene que ampliar la configuración recomendada con los siguientes.</p> <p>
    <blockquote>
      Se supone que las opciones que faltan están desactivadas, vacías o por defecto.<br><br>
      <strong>[Avanzado] Varios:</strong>
      <ul>
        <li>- [x] Restricciones de caché: Activar el almacenamiento en caché para todos los visitantes</li>
        <li>- [x] Hacer anónimos a los usuarios conocidos para que se les sirvan archivos estáticos supercacheados.</li>
      </ul>
      <hr>
      Genial, ¡ahora tu sitio está roto para los usuarios que han iniciado sesión! O mejor dicho, son tratados como invitados y ya no pueden ver su contenido personal ni publicar comentarios. Para solucionarlo, dirígete a <a href="#pestaña-general">Configuración general de Fictioneer</a> y activa las siguientes opciones. A continuación, borra la caché. Sí, la barra de administración ha desaparecido. Sí, todavía puedes entrar en el admin con el enlace <code>.../wp-admin</code>. No, los mensajes protegidos con contraseña ya no funcionan.<br><br>
      <strong>[General] Asignación de páginas:</strong>
      <ul>
        <li>- [ ] Cuenta: Ninguna (perfil por defecto del salpicadero)</li>
        <li>- [x] Estantería: Página con la plantilla Bookshelf AJAX (si la necesitas)</li>
      </ul><br>
      <strong>[General] Seguridad y privacidad:</strong>
      <ul>
        
      </ul><br>
      <strong>[General] Compatibilidad:</strong>
      <ul>
        <li>- [x] Activar el modo de compatibilidad de la caché pública</li>
        <li>- [x] Habilitar autenticación de usuario AJAX</li>
        <li>- [x] Habilitar formulario de comentarios AJAX (mejor rendimiento) ... o ... sección de comentarios (mejor compatibilidad)</li>
      </ul>
    </blockquote>
  </detalles>

* [W3 Total Cache](https://wordpress.org/plugins/w3-total-cache/): Completo conjunto de funciones de caché y rendimiento con gran compatibilidad independientemente del host. Pero su instalación es bastante complicada y requiere una suscripción para sacarle el máximo partido. Consulte la guía de instalación.

  <detalles>
    <summary>Excepciones de caché</summary><br>
    <p>Mientras sólo sirvas páginas en caché a usuarios no autenticados, difícilmente podrás hacerlo mal. Para asegurarte de que todo funciona, añade las siguientes excepciones en <strong>Performance > Page Cache</strong>.
    <blockquote>
      <strong>[Caché de página] No almacenar nunca en caché las siguientes páginas:</strong><br>
      &numsp;Los fragmentos URI "cuenta" y "estantería" pueden diferir en su sitio (ya que puede asignarles un nombre).<br>
      &numsp;<code>/oauth2*</code><br>
      &numsp;<code>/download-epub*</code><br>
      &numsp;<code>/cuenta*</code><br>
      &numsp;<code>/estantería*</code><br>
      &numsp;<code>/wp-json/storygraph</code><br>
      &numsp;<code>/wp-json/fictioneer</code>
    </blockquote>
  </detalles>

* [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/): El más potente de los plugins de caché de la lista y también completamente gratuito - si puede hacerlo funcionar. Como caché del lado del servidor, su host debe soportar [LiteSpeed](https://docs.litespeedtech.com/lscache/), que suele ser un punto de venta destacado para que usted lo sepa.

  <detalles>
    <summary>Configuración de ejemplo</summary><br>
    <p>LiteSpeed Cache le ofrece mucho más de lo que se cubre aquí, así que por favor consulte guías más completas si desea tomar ventaja de eso. Sin embargo, combinado con los otros plugins recomendados, puedes prescindir de él.</p> <p>
    <blockquote>
      Se supone que las opciones que faltan están desactivadas, vacías o por defecto.<br><br>
      <strong>[1 - Caché] Ajustes de control de caché:</strong>
      <ul>
        <li>-[x] Activar caché</li>
        <li>- [ ] Caché de usuarios registrados (OFF)</li>- [ ] Caché de usuarios registrados (OFF)</li>- [ ] Caché de usuarios registrados (OFF)</li>)
        <li>- [ ] Cache Comentaristas (OFF)</li>- [ ] Cache Comentaristas (OFF)</li>- [ ] Cache Comentaristas (OFF)
        <li>-[x] API REST de caché</li>
        <li>- [x] Página de inicio de sesión en caché</li>
        <li>- [x] Caché favicon.ico</li>.
        <li>- [x] Recursos de caché de PHP</li>
        <li>- [ ] Caché Móvil (OFF)</li>- [ ] Caché Móvil (OFF)</li>- [ ] Caché Móvil (OFF)
      </ul><br>
      <strong>[2 - TTL] TTL:</strong>
      <ul>
        <li>- [x] TTL de caché pública por defecto: 28800</li>
        <li>- [x] TTL de caché privada por defecto: 1800</li>
        <li>- [x] TTL de portada por defecto: 604800</li>
        <li>- [x] TTL de alimentación por defecto: 604800</li>
        <li>- [x] TTL REST por defecto: 28800</li>
      </ul><br>
      <strong>[3 - Purga] Ajustes de purga:</strong>
      <ul>
        <li>- [x] Purgar todo al actualizar</li>
        <li>- [x] Reglas de purga automática para publicar/actualizar: todas las páginas</li>
        <li>- [ ] Servir rancio (OFF)</li>.
      </ul><br>
      <strong>[4 - Excluye] No almacenar en caché URI:</strong><br>
      &numsp;Los fragmentos URI "cuenta" y "estantería" pueden diferir en su sitio (ya que puede asignarles un nombre).<br>
      &numsp;<code>/oauth2</code><br>
      &numsp;<code>/download-epub</code><br>
      &numsp;<code>/cuenta</code><br>
      &numsp;<code>/estantería</code><br>
      &numsp;<code>/wp-json/storygraph</code><br>
      &numsp;<code>/wp-json/fictioneer</code><br><br>
      <strong>[4 - Excluye] No almacenar en caché cadenas de consulta:</strong><br>
      &numsp;<code>commentcode</code><br><br>
      <strong>[4 - Excluye] No almacenar en caché funciones:</strong>
      <ul>
        <li>- [x] Administrador</li>
        <li>- [x] Moderador</li>.
        <li>- [x] Editor</li>.
        <li>- [x] Autor</li>
      </ul><br>
      <strong>[5 - ESI] Ajustes ESI:</strong>
      <ul>
        <li>- [x] Activar ESI</li>
        <li>- [x] Barra Admin Caché</li>
        <li>- [x] Caché Formulario de comentarios</li>
      </ul><br>
      <strong>[5 - ESI] Nonces ESI:</strong><br>
      &numsp;<code>oauth_nonce</code><br>
      &numsp;<code>fictioneer_nonce</code><br>
      &numsp;<code>fictioneer-ajax-nonce</code><br><br>
      <strong>[5 - ESI] Grupo Vary:</strong>
      <ul>
        <li>- [x] Administrador: 99</li>
        <li>- [x] Moderador: 50</li>
        <li>- [x] Editor: 40</li>
        <li>- [x] Autor: 30</li>
        <li>- [x] Contribuyente: 20</li>
        <li>- [x] Abonado: 0</li>
      </ul><br>
      <strong>[7 - Navegador] Configuración de la caché del navegador:</strong>
      <ul>
        <li>- [x] Caché del navegador</li>.
        <li>- [x] Browser Cache TTL: 31557600</li>
      </ul>
    </blockquote>
  </detalles>

### Recommended: Must-Use Plugins

[Must-Use Plugins](https://wordpress.org/documentation/article/must-use-plugins/) no se instalan, sino que deben copiarse en el directorio **wp-content/mu-plugins** (no existe por defecto). Siempre se cargan, en orden alfabético, y antes que cualquier otro plugin o tema. Este comportamiento puede ser explotado para aumentar el rendimiento. Cuando busques en el directorio del tema Fictioneer, encontrarás un subdirectorio mu-plugins con archivos de plugins listos para ser copiados.

**Desde 5.20.2,** puedes añadir y eliminar los mu-plugins del tema rápidamente en **Fictioneer > Plugins**. También te mostrará si hay una actualización disponible, pero no la instalará automáticamente en caso de que hayas personalizado el archivo. Compruébalo de vez en cuando.

Si surgen problemas, puede eliminar los archivos del plugin.

**Fictioneer 001 Fast Requests](https://github.com/Tetrakern/fictioneer/tree/main/mu-plugins)** acelera las peticiones AJAX y REST desactivando los plugins no permitidos durante las acciones seleccionadas del tema. Dependiendo del número de plugins que tengas instalados, esto puede aumentar significativamente el rendimiento de tus peticiones. Sin embargo, impedirá que los plugins funcionen durante estas peticiones, aunque eso no tiene ningún efecto sobre la funcionalidad por defecto del tema. No tema editar el archivo y ampliar la lista de permitidos, no se sobrescribirá cuando actualice el tema. O añada sus propios archivos de plugin. Esta es una de las mejores optimizaciones de velocidad que puedes hacer.

**[Fictioneer 002 Elementor Control](https://github.com/Tetrakern/fictioneer/tree/main/mu-plugins)** desactiva el plugin Elementor en todas las páginas excepto en aquellas con plantillas Canvas. Lamentablemente, Elementor consume una cantidad significativa de recursos del servidor, por lo que limitar su uso a las páginas necesarias es ideal para mantener un sitio más rápido. Esto supone que no necesitas Elementor en ningún otro sitio; si es así, este método no te funcionará. Alternativamente, puede utilizar el plugin [Plugin Load Filter](https://wordpress.org/plugins/plugin-load-filter/) para un control más preciso, aunque requiere más configuración.

### Warning: SEO Plugins

Mientras que los plugins de optimización de motores de búsqueda como [Yoast](https://wordpress.org/plugins/wordpress-seo/) y [AIOSEO](https://wordpress.org/plugins/all-in-one-seo-pack/) son generalmente el camino a seguir, no se recomiendan aquí. Fictioneer ya viene con una optimización para motores de búsqueda, no perfecta, pero adaptada al propósito. Los plugins de terceros no entienden el tema, y mucho menos las ficciones web. Asumen que todo son artículos o productos basados en el tema, lo que lleva a resultados defectuosos a menos que les enseñes y eso requiere código personalizado. También bloquean características esenciales detrás de una suscripción que Fictioneer proporciona de forma gratuita.

Y para dar un paso atrás y ser realistas: el SEO es importante. Desde luego. Por desgracia. Pero si realmente intentas optimizar tu *prosa* en función de la densidad de palabras clave, la complejidad de las palabras, la longitud de las frases y párrafos, o cualquier otra locura estadística para suplicar al gran algoritmo, tienes un veneno en la cabeza.

### Warning: CSS Minification/Combination

El CSS del tema ya viene minificado y, aunque las optimizaciones adicionales como la combinación de archivos o el filtrado de estilos *presumiblemente* no utilizados pueden mejorar aún más la velocidad, también pueden romper fácilmente tu diseño. Esto se ha demostrado que es un problema con la función de auto-minificación de Cloudflare, por ejemplo, que elimina los espacios en blanco en las funciones `clamp()` que son necesarios para que funcionen. Un caso especialmente insidioso que podría costarte localizar, ya que ocurre durante la petición, no en tu propio servidor.

## How to Configure the Fictioneer Theme

![General Settings Preview](repo/assets/settings_general_preview.jpg?raw=true)

### General Tab

La mayor parte de la configuración del tema se encuentra aquí, las opciones se explican por sí mismas. Tenga en cuenta que probablemente no necesitará todas las funciones disponibles, como Marcas o Seguimientos. Éstas son para sitios con muchos autores o historias; publicar una serie semanal es mejor para ahorrar recursos del servidor. Algunos cambios requieren que purgues las cachés del tema después de actualizar en **Fictioneer > Herramientas**. Las opciones más oscuras tienen un botón (?) para mostrar un modal de ayuda.

### Roles Tab

![Roles Settings Preview](repo/assets/settings_roles_preview.jpg?raw=true)

El gestor de roles integrado para añadir, editar y eliminar roles. No es el más sofisticado en comparación con los plugins dedicados, pero viene con capacidades personalizadas adaptadas al tema. Debido a que Fictioneer ofrece algunas opciones y herramientas poderosas es posible que desee mantener alejados a ciertos grupos de usuarios. Si los roles no se han inicializado correctamente cuando activaste el tema, puedes hacerlo en la pestaña **Herramientas**. Como referencia, mira las [capacidades de WordPress] por defecto (https://wordpress.org/documentation/article/roles-and-capabilities/).

**Nota:** La capacidad de "lectura" es la que da acceso al perfil del usuario administrador y al panel de control, lo que no es obvio sólo por el nombre.

<detalles>
  <summary>Nuevas capacidades</summary><br>

  **Códigos cortos:** Sin esta función, los códigos cortos se eliminan al guardar una entrada.
  * **Seleccionar plantilla de página:** No puede cambiar la plantilla de página sin esto.
  * **Custom Page CSS:** Inyecta CSS en la cabecera para un estilo único. ¡Peligroso!
  * **Custom ePUB CSS:** Inyecta CSS en el ePUB para conseguir un estilo único. ¡Peligroso!
  * **Carga de ePUB personalizado:** Asigna un archivo ePUB personalizado de la biblioteca multimedia a la historia.
  * **Custom Page Header:** Cambia la imagen de cabecera de las páginas seleccionadas.
  * **SEO Meta:** Mostrar y editar la meta SEO para los mensajes (si está habilitado en la configuración).
  * **Make Sticky:** Puedes hacer que los mensajes y las historias se peguen a la parte superior en las listas.
  * **Editar Permalink:** Personaliza el permalink slug derivado del título. Peligroso
  * **Todos los Bloques:** Tus opciones de bloque son bastante limitadas sin esto, por razones de *sanidad*.
  * **Páginas de historias:** Te permite adjuntar hasta cuatro páginas a tus historias como pestañas adicionales.
  * **Editar fecha:** Permite cambiar la fecha de publicación *después* de la publicación.
  * **Asignar niveles de Patreon:** Te permite establecer niveles de Patreon y umbrales de compromiso para las publicaciones.
  * **Perfil reducido:** Elimina el desorden de la página de perfil de administrador, como los esquemas de color.
  * **Moderar sólo comentarios:** Limita a los moderadores a editar sólo los comentarios, no todos los mensajes.
  * **Límite de subida:** Aplica el límite de tamaño de archivo desde la Configuración General.
  * **Restricciones de carga:** Aplicar las restricciones de tipo de archivo desde la Configuración General.
  * **Acceso a la barra de administración:** Anula la configuración individual para mostrar u ocultar la barra de administración.
  * **Acceso al panel de administración:** Necesario para acceder al panel de administración, incluido su perfil de administrador.
  * **Acceso al panel de control:** Necesario si desea ver la página de administración del panel de control.
  * **Mostrar Insignia:** Muestra el nombre del rol como insignia de comentario. Se puede anular en su perfil.
  * **Moderación de comentarios en posts:** Te permite una moderación limitada de comentarios en tus propios posts. Sólo AJAX.
  * **Permitir Autoeliminación:** Le permite eliminar su propia cuenta. Por defecto para abonados.
  * **Autorización de privacidad:** Concede acceso a datos sensibles como correos electrónicos y direcciones IP.
  * **Leer archivos de otros:** Te permite ver los archivos subidos por *otros* usuarios.
  * **Editar archivos de otros:** Te permite editar los archivos subidos por *otros* usuarios.
  * **Borrar archivos de otros:** Permite borrar los archivos subidos por *otros* usuarios.
  * Desbloquear mensajes:** Permite desbloquear mensajes protegidos por contraseña para los usuarios.
  * **Gestionar {Taxonomía}:** Le permite ver la tabla de lista general de la taxonomía.
  * **Asignar {Taxonomía}:** Le permite asignar la taxonomía a sus entradas.
  * **Editar {Taxonomía}:** Le permite crear y editar taxonomías de este tipo.
  * **Eliminar {Taxonomía}:** Permite eliminar taxonomías de este tipo.
  **Ignorar {Tipo de entrada} Contraseñas:** Omite las contraseñas para este tipo de entrada.

</detalles>

### Plugins Tab

![Connections Settings Preview](repo/assets/settings_plugins_preview.jpg?raw=true)

Esta pestaña sólo es visible si los plugins relacionados con el tema están instalados y activos. El autor del plugin decidirá si las fichas mostradas son puramente informativas o si contienen funciones. No sustituye a la página de plugins por defecto de WordPress.

### Connections Tab

![Connections Settings Preview](repo/assets/settings_connections_preview.jpg?raw=true)

Cualquier cosa que conecte con proveedores de servicios externos va aquí, como el ID de Cliente y el Secreto para aplicaciones OAuth 2.0. Por favor, consulta los tutoriales respectivos sobre cómo configurarlos y siempre, *siempre* mantén esas credenciales confidenciales.

Si introduces un [Discord webhook](https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks) aquí, las notificaciones sobre nuevos comentarios, historias y/o capítulos se enviarán directamente a un canal de tu servidor (déjalo libre si no quieres eso). Asegúrate de que los comentarios se envían a un canal de moderación oculto, ya que recibirá extractos de los comentarios privados. Ten en cuenta que los webhooks dejan de funcionar si se utilizan para más de una aplicación (por motivos de seguridad).

* [Portal del Desarrollador Discord](https://discord.com/developers/docs/topics/oauth2)
* [Portal de desarrolladores de Twitch](https://dev.twitch.tv/docs/authentication/register-app)
* [Portal para desarrolladores Patreon](https://docs.patreon.com/#oauth)
* [Portal para desarrolladores de Google](https://developers.google.com/identity/protocols/oauth2)

El URI de redirección de la petición OAuth debería ser similar a `https://your-domain.com/oauth2`, siendo la parte importante el endpoint `/oauth2`. Ten en cuenta que los proveedores de servicios pueden ser quisquillosos, como rechazar una URI que incluya "www" si no forma parte de la dirección de tu sitio web. Utilice la cadena _exacta_ que ve en la barra de direcciones de su navegador. Si la redirección devuelve un 404, normalmente tendrá que vaciar los enlaces permanentes en **Configuración > Enlaces permanentes** (sólo tiene que guardar).

Para más detalles sobre Patreon, consulte [Integración de Patreon](#patreon-integration) más abajo.

### Phrases Tab

![Phrases Settings Preview](repo/assets/settings_phrases_preview.png?raw=true)

Permite algunas traducciones y cambios menores, como el banner de aviso de cookies o el correo electrónico de notificación de respuesta a comentarios. Se puede conseguir una mayor personalización con el [filtro de traducción] del tema(FILTERS.md#apply_filters-fictioneer_filter_translations_static-strings-). Pero si quieres traducir el tema a un nuevo idioma, tendrás que incluir los [archivos de traducción] adecuados(https://developer.wordpress.org/plugins/internationalization/localization/) o utilizar un plugin. Puede encontrar un archivo de plantilla .POT en la carpeta de idiomas.

### Fonts Tab

![Phrases Settings Preview](repo/assets/settings_fonts_preview.jpg?raw=true)

Una visión general de todas las fuentes instaladas, con las opciones para activarlas/desactivarlas. También puedes incluir aquí Google Fonts, pero ten en cuenta que esto infringe la GDPR. Si quieres instalar fuentes personalizadas, echa un vistazo a la sección [Fuentes personalizadas](https://github.com/Tetrakern/fictioneer/blob/main/INSTALLATION.md#custom-fonts) más abajo.

### ePUBs Tab

![ePUBs Settings Preview](repo/assets/settings_epubs_preview.jpg?raw=true)

Lista todos los ePUBs generados con estadísticas, enlaces de descarga y opciones para eliminarlos. Los nombres de archivo son iguales al `post_name` de la historia, que es el slug dentro del permalink y *no* el título. Se limpian de caracteres especiales y también se utilizan para consultar las historias asociadas. Si cambias el permalink, dejarán de coincidir y se generará un nuevo ePUB, dejando huérfano el anterior. Esto no es terrible, pero ocupa espacio.

**ePUBs fallidos:** Indicado por un archivo "download" vacío. La generación de ePUBs puede fallar debido a varias circunstancias, como la falta de permisos de escritura a lo largo de la ruta de `wp-content/uploads/epubs` o contenido no conforme en la historia o los capítulos. Desgraciadamente, los ePUB son bastante quisquillosos con el HTML permitido y, aunque el conversor intenta desinfectar el contenido, no es infalible. Como alternativa, puedes subir un archivo tú mismo en lugar de confiar en el conversor, que no se limita al formato ePUB.

### SEO Tab

![ePUBs Settings Preview](repo/assets/settings_seo_preview.png?raw=true)

Sólo está disponible si habilita las funciones SEO y no se está ejecutando ningún plugin SEO (conocido). Enumera todos los metadatos y esquemas Open Graph generados y utilizados por los motores de búsqueda y las redes sociales, creados y almacenados en caché cuando se visita una entrada por primera vez hasta que se modifican o se eliminan. Tenga en cuenta que la mayoría de las plantillas de página (además de las plantillas de lista) y las colecciones no tienen esquemas, por lo que aparecen en gris.

Que estos servicios muestren realmente los datos ofrecidos depende totalmente de ellos. Por ejemplo, no puede obligar a Google a mostrar su descripción personalizada. Después de todo, podrías escribir *cualquier cosa* ahí. Esta pestaña es principalmente informativa, pero puede eliminar los metadatos o esquemas almacenados en caché si fuera necesario.

Si quieres configurar una imagen Open Graph por defecto para los resultados de los motores de búsqueda e incrustaciones, puedes hacerlo en el **Personalizador** en **Identidad del sitio**. Esta imagen se utilizará siempre que no haya una más específica, como la miniatura de las entradas.

### Tools Tab

Una colección de acciones para añadir, actualizar, revertir, arreglar o purgar ciertos elementos. Por ejemplo, puede añadir un rol de moderador adecuado si falta o convertir etiquetas en géneros. Todo está minuciosamente explicado. Pero la única acción que probablemente necesitarás más de una vez es **Purgar cachés de temas**, que debería hacerse siempre que cambies la configuración de capítulos o historias.

Si los roles de usuario carecen de permisos, como por ejemplo que los autores no puedan añadir historias y capítulos, utiliza la acción **Inicializar roles**. Esto también restablece los valores por defecto si te equivocas, aunque no restablecerá las capacidades fuera del ámbito del tema. La mayoría de las capacidades administrativas se dejan intactas por razones de seguridad.

### Log

Registro de las acciones administrativas realizadas en relación con el tema.

## Patreon Integration

Puede conceder a los usuarios registrados acceso a contenidos protegidos por contraseña a través de la suscripción a Patreon, ya sea por niveles seleccionados, umbrales de compromiso o ambos. Esto requiere que habilites y configures la autenticación OAuth 2.0 para Patreon, permitiendo a los usuarios iniciar sesión con su cuenta de Patreon e importar sus datos de membresía. El plugin oficial de Patreon para WordPress también funciona, pero la integración con el tema no es perfecta (proceso de registro diferente, compatibilidad con el almacenamiento en caché e impacto en el rendimiento desconocido).

**Fictioneer > General > Artículo:**
* Activar la autenticación OAuth 2.0
* Habilitar la puerta de contenido de Patreon

Después de configurar la [conexión OAuth 2.0](#pestaña-conexiones), añade un enlace de campaña e importa tus tiers. Esta es una solicitud única limitada a los administradores y sólo funciona para la campaña de su cliente. No, no puedes tener diferentes campañas para diferentes autores. Los cambios de tiers en Patreon **no** se sincronizan automáticamente, tienes que sacarlos tú mismo (pero esto rara vez debería ser necesario).

Una vez hecho esto, puedes aplicar niveles y umbrales de compromiso en céntimos (por ejemplo, 350 por 3,50 $) a publicaciones individuales o establecerlos globalmente. **Si haces ambas cosas, las entradas siempre utilizarán los requisitos más bajos**. Tenga en cuenta que todavía necesita establecer una contraseña de entrada, porque esta función sólo secuestra la comprobación de contraseña de WordPress. Eliminar una contraseña también suspenderá la entrada de Patreon. Para mantener la compatibilidad con los plugins de caché, la puerta no se transmite de las historias a los capítulos.

**Opciones:**
* **Enlace de campaña (Global):** Enlace a su campaña, necesario para que aparezca el botón de enlace.
* Sustituye el mensaje debajo del enlace de Patreon en los mensajes bloqueados.
* **Tiers (Post/Global):** Lista separada por comas de IDs de tier, que puedes ver después de tirar de ellos.
* **Umbral (Post/Global):** Importe del compromiso en céntimos (por ejemplo, 350 por 3,50 $) independientemente de los niveles.
* **Umbral Vitalicio (Global):** Utiliza el total de todas las promesas pagadas, independientemente de su estado actual.
* **Umbral de desbloqueo (Global):** Entrada de usuario regular desbloquea detrás de una cantidad de compromiso en céntimos.
* **Ocultar formularios de contraseña (Global):** Oculta el formulario de contraseña normal en las entradas cerradas.

Los datos de afiliación son válidos durante una semana por defecto, por usuario, y se actualizan cada vez que se conectan con Patreon. Mientras tanto, pueden conectarse con otras cuentas. Esto puede hacer que los usuarios conserven los derechos de acceso durante más tiempo del que permite su estado de miembro (hasta seis días), lo que es consecuencia de que el tema no mantiene una conexión continua con Patreon por razones de seguridad - pero si te hackean, sus cuentas de Patreon estarán a salvo a su vez. La seguridad rara vez es conveniente.

Puede aumentar o reducir el tiempo de expiración con la constante `FICTIONEER_PATREON_EXPIRATION_TIME` en un tema hijo, pero no debería ser inferior a tres días (que es el tiempo máximo de inicio de sesión antes de que se cierre la sesión automáticamente).

**Caché:** Si utiliza un plugin de caché, asegúrese de que los mensajes protegidos por contraseña no se almacenan en caché o esto no funcionará correctamente. El plugin LiteSpeed Cache debería estar bien, pero cualquier otra cosa podría necesitar una configuración adicional.

![Patreon Gate Settings](repo/assets/connection_settings_patreon_gate.jpg?raw=true)

## How to Customize the Fictioneer Theme

![Customizer HSL Sliders](repo/assets/customizer_hsl_sliders_demo.gif?raw=true)

Hay dos maneras de personalizar el tema. La más obvia es el Personalizador de WordPress en **Apariencia > Personalizar**. Aquí puedes subir una imagen de cabecera y el logotipo, establecer un título del sitio, cambiar el esquema de color, y modificar el diseño en cierta medida. La interfaz y la vista previa en vivo hacen que esto sea sencillo. Si las opciones de color son demasiado exigentes (y lo son), tal vez quieras limitarte a los deslizadores de tono, saturación y luminosidad. Consulta también las numerosas guías sobre personalización de WordPress.

La segunda forma es modificar directamente las plantillas, estilos y scripts. Esto es indefinidamente más poderoso pero requiere algunas habilidades de desarrollador - y usted puede fácilmente romper su sitio. Los archivos del tema pueden modificarse en **Apariencia > Editor de archivos de tema**, aunque en realidad nunca deberías hacerlo. Crea siempre un [tema hijo](https://developer.wordpress.org/themes/advanced-topics/child-themes/) porque cualquier cambio de código que hagas, independientemente de su calidad, se sobrescribirá de nuevo cuando actualices el tema. Puedes encontrar un tema hijo base ya creado [aquí](https://github.com/Tetrakern/fictioneer-child-theme).

### Demo Layout

A petición del público, he aquí una pequeña guía sobre cómo imitar el sitio de demostración. Tenga en cuenta que la demostración es más para mostrar las características de ser un ejemplo de producción. Asegúrese de mirar los [shortcodes] disponibles (DOCUMENTATION.md#shortcodes) y sus posibles configuraciones. Si eres nuevo en WordPress, es mejor que leas una guía sobre el uso del CMS primero porque los conceptos básicos no están cubiertos aquí.

En primer lugar, crea dos páginas nuevas con la plantilla "Sin portada", una llamada "Inicio" y la otra "Entradas" (o como prefieras). Luego ve a **Configuración > Lectura > Tu página de inicio muestra** y configúrala como ["Una página estática"](https://wordpress.org/documentation/article/create-a-static-front-page/). Asigna las páginas que has creado. Ahora puedes añadir bloques y shortcodes a tu página "Inicio"; sólo deja la página "Posts" vacía. De forma similar, puedes añadir páginas de lista para Historias, Capítulos, etc. con las plantillas correspondientes y asignarlas en **Fictioneer > General > Asignación de páginas**. No las necesitas todas.

Para simplificar, aquí está el contenido copiado de la página de inicio de demostración (menos algunas cosas específicas del sitio). Póngalo en la vista del editor de código y ajústelo según sea necesario. Cuando vuelvas al editor visual, todo debería estar formateado correctamente como bloques.

<detalles>
  <summary>Contenido del editor</summary><br>

```html
<!-- wp:shortcode -->
[fictioneer_latest_posts count="1"]
<!-- /wp:shortcode -->

<!-- wp:spacer {"height": "24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:shortcode -->
[fictioneer_article_cards per_page="2" ignore_sticky="1"]
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

### Header Style

![Customizer HSL Sliders](repo/assets/customizer_header_style_preview.jpg?raw=true)

Puede elegir entre tres estilos de cabecera diferentes: **por defecto**, **superior**, y **dividido** - o **ninguno**, si eso es lo que quieres. El estilo **por defecto** es el que se ve en las capturas de pantalla y en el sitio de demostración, opcionalmente con título, lema y/o logotipo. El **estilo superior** coloca la identidad del sitio por encima de la navegación y elimina la imagen de cabecera. Y el **estilo dividido** es una mezcla de ambos, con la identidad arriba pero una imagen de cabecera debajo de la navegación.

### CSS Snippets

![Customizer HSL Sliders](repo/assets/developer_tools_preview.jpg?raw=true)

Aunque las opciones de personalización no son tan amplias como las de los temas polivalentes o los creadores de páginas, se pueden conseguir bastantes cosas con unos simples fragmentos [CSS](https://developer.mozilla.org/en-US/docs/Web/CSS). Fácil de aprender, difícil de dominar. Sin embargo, a continuación hay varios fragmentos que puedes utilizar y modificar según tus necesidades. Sólo tienes que ponerlos en **Customizer > CSS adicional** o en un tema hijo. Esta es, con mucho, la forma más poderosa de personalización - hay más de 500 propiedades y prácticamente infinitos valores posibles y combinaciones que se pueden asignar a todos y cada elemento.

**Herramientas para desarrolladores:** ¡Tu mejor amigo! Puedes abrirlas haciendo clic con el botón derecho en cualquier parte del sitio y pulsando **Inspeccionar**, resaltando directamente el elemento en el que te encuentras. Pulsa **[Opción] + \[⌘\] + \[J\]** (en macOS) o **[Mayús] + \[CTRL] + \[J\]** (en Windows/Linux) si quieres usar el teclado. Aquí puedes ver el HTML y los estilos CSS aplicados; incluso puedes manipularlos para ver qué ocurre. Hay muchos tutoriales en línea sobre cómo utilizar las herramientas, por favor consulta uno primero si eres nuevo.

Para apuntar a un elemento con CSS, primero necesitas encontrar un [selector] válido (https://developer.mozilla.org/en-US/docs/Learn/CSS/Building_blocks/Selectors). Normalmente se trata de una clase, uno de los valores separados por espacios en blanco que se muestran en el **Inspector de elementos** (precedido de un único punto). Son los mejores en términos de [rendimiento](https://developer.mozilla.org/en-US/docs/Learn/Performance/CSS) y compatibilidad. Incluso puedes encadenarlos para mayor [especificidad](https://developer.mozilla.org/en-US/docs/Web/CSS/Specificity). El **Inspector de estilos** muestra las propiedades y valores aplicados actualmente.

#### Dark/Light Mode & Media Queries

A menudo es necesario aplicar estilos específicos en función del modo del tema o del tamaño de la pantalla. Especialmente los colores son un problema, ya que algunos que resaltan sobre un fondo claro pueden desaparecer sobre uno oscuro. Otro problema son las limitaciones impuestas por las pantallas de los móviles: rara vez hay espacio suficiente. Por suerte, esto se puede tener en cuenta.

```css
/* Sólo se aplica a tamaños de ventana de 768px y superiores. */
@media only screen and (min-width: 768px) {
  .selector {
    propiedad: valor;
  }
}

/* Sólo se aplica a tamaños de ventana de 767px o inferiores. */
@media only screen and (max-width: 767px) {
  .selector {
    propiedad: valor;
  }
}

/* Se aplica siempre. */
.selector {
  propiedad: valor;
}

/* Sólo se aplica en modo luz (selector encadenado). */
:root[data-mode="light"] .selector {
  propiedad: valor;
}

/* Sólo se aplica en modo oscuro (selector encadenado). */
:root[data-mode="dark"] .selector {
  propiedad: valor;
}
```

#### Overwrite Custom Properties

[Custom properties](https://developer.mozilla.org/en-US/docs/Web/CSS/--*)también conocidas como variables CSS, contienen valores que pueden ser asignados a propiedades de estilo usando la función `var()`. Se asignan al selector o selectores en los que se declaran, pero normalmente se asignan a `:root` para que estén disponibles en todas partes. Fictioneer hace un uso liberal de las propiedades personalizadas (ver [aquí](https://github.com/Tetrakern/fictioneer/blob/main/src/scss/common/_properties.scss)) y puedes cambiar muchas cosas simplemente sobrescribiéndolas. Pero ten cuidado, pueden causar graves problemas de rendimiento si se hacen dinámicas.

```css
/* Hacer el fondo de navegación *sticky* 10% transparente (siempre). */
:root {
  --navigation-background-end-opacity: 0.9;
}

/* Hacer el fondo de navegación *sticky* 10% transparente (sólo en modo oscuro). */
:root[data-mode="dark"]{
  --navigation-background-end-opacity: 0.9;
}

/* Hacer que el fondo de navegación sea siempre visible. */
:root {
  --opacidad de inicio de fondo de navegación: 1;
}
```

#### Button Colors

Los colores de los botones se basan en las propiedades CSS background `var(--bg-x)` y foreground `var(--fg-x)`, siendo `x` la asignación numérica del tono tal y como se ve en el Personalizador. Esto puede hacer que sean difíciles de modificar. La razón de la falta de opciones de entrada de color es que simplemente hay demasiadas propiedades asociadas a los botones. Sin embargo, ¡puede modificarlos con CSS personalizado! A continuación se muestra la configuración de propiedades por defecto para los botones.

```css
/* Modo oscuro */
:root, :root[data-theme=base] {
  --button-font-weight: 500;
  --button-box-shadow: none;
  --button-color-activo: var(--fg-invertido);
  --button-background-active: var(--bg-100);
  --button-border-active: 1px solid transparent;
  --button-barberpole: var(--bg-500);
  --button-oauth-connected: var(--button-background-active);

  --button-primary-background: var(--bg-400);
  --button-primary-background-hover: var(--bg-300);
  --button-primary-background-disabled: var(--bg-500);
  --button-primary-color: var(--fg-400);
  --button-primary-color-hover: var(--fg-300);
  --button-primary-color-disabled: var(--fg-700);
  --button-primary-filter-disabled: saturate(.7) opacity(.3) brightness(1.4);

  --button-secondary-background: transparente;
  --button-secondary-background-hover: var(--bg-500);
  --button-secondary-background-disabled: repeating-linear-gradient(-45deg, rgb(255 255 255 / 6%), rgb(255 255 255 / 6%) 2px, transparent 2px, transparent 4px);
  --button-secondary-color: var(--fg-600);
  --button-secondary-color-hover: var(--fg-400);
  --button-secondary-color-disabled: var(--fg-800);
  --button-secondary-border: 1px solid var(--bg-300);
  --button-secondary-border-hover: 1px solid var(--bg-200);
  --button-secondary-border-disabled: 1px solid var(--bg-300);

  --button-warning-background: var(--red-500);
  --button-warning-background-hover: var(--red-600);
  --button-warning-color: #fff;
  --button-warning-colour-hover: #fff;

  --button-suggestion-color: var(--fg-inverted);
  --button-suggestion-color-hover: var(--fg-inverted);
  --button-suggestion-background: var(--bg-100);
  --button-suggestion-background-hover: var(--bg-50);

  --button-quick-background: var(--bg-500);
  --button-quick-background-hover: var(--bg-300);
  --button-quick-color: var(--fg-600);
  --button-quick-color-hover: var(--fg-400);

  --button-file-block-color: var(--fg-inverted);
  --button-file-block-color-hover: var(--fg-inverted);
  --button-file-block-background: var(--bg-100);
  --button-file-block-background-hover: var(--bg-50);
}

/* Modos de iluminación */
:root[data-mode=light] {
  --button-color-activo: var(--fg-invertido);
  --button-background-active: var(--bg-700);
  --button-barberpole: var(--bg-300);

  --button-primary-background: var(--bg-600);
  --button-primary-background-hover: var(--bg-700);
  --button-primary-background-disabled: var(--bg-400);
  --button-primary-color: var(--fg-inverted);
  --button-primary-color-hover: var(--fg-inverted);
  --button-primary-color-disabled: var(--fg-700);
  --button-primary-filter-disabled: opacity(.6);

  --button-secondary-background-hover: var(--bg-300);
  --button-secondary-background-disabled: repeating-linear-gradient(-45deg, hsl(var(--bg-950-free) / 6%), hsl(var(--bg-950-free) / 6%) 2px, transparent 2px, transparent 4px);
  --button-secondary-border: 1px solid var(--bg-400);
  --button-secondary-border-hover: 1px solid var(--bg-500);
  --button-secondary-border-disabled: 1px solid var(--bg-400);

  --button-suggestion-background: var(--bg-600);
  --button-suggestion-background-hover: var(--bg-700);

  --button-quick-background: var(--bg-600);
  --button-quick-background-hover: var(--bg-700);
  --button-quick-color: var(--fg-invertido);
  --button-quick-color-hover: var(--fg-inverted);

  --button-file-block-color: var(--fg-inverted);
  --button-file-block-color-hover: var(--fg-inverted);
  --button-file-block-background: var(--bg-600);
  --button-file-block-background-hover: var(--bg-700);
}
```

#### Top-Header & Navigation Backgrounds

Suponiendo que haya configurado el **Estilo de cabecera** como **superior** o **split**, el siguiente fragmento de código hace que el fondo de navegación sea siempre visible independientemente de la posición de desplazamiento y añade un color de fondo semitransparente a la cabecera. Esto puede ser útil si su sitio tiene una imagen de fondo.

```css
:root {
  --top-header-background: hsl(calc(221deg + var(--hue-rotate)) calc(16% * var(--saturation)) clamp(10%, 20% * var(--darken), 60%) / 70%); /* Ejemplo de color HSL dinámico con 70% de opacidad; rgb(43 48 59 / 70%). */
  --opacidad de inicio de fondo de navegación: 1;
}

.top-header {
  padding-bottom: 1rem; /* Espaciado inferior dentro de la cabecera. */
}

.main-navigation {
  margin-top: 0; /* Cerrar el hueco entre la navegación y la cabecera. */
}

/* Se aplica a dos selectores encadenados; el primero sólo afecta a los descendientes directos (>) */
.main-navigation__list > .menu-item,
.main-navigation .icon-menu__item {
  border-radius: 0 !important; /* El !important impone el valor, aunque el selector sea demasiado débil */
}
```

#### Background Overlay & Filters

Suponiendo que haya establecido una imagen de fondo para su sitio, este fragmento añade una superposición que le permite matizar y filtrar dicha imagen - a un coste de rendimiento. Por lo general, es mejor utilizar una imagen ya preparada para sus necesidades, pero esta es una forma de aplicar ajustes dinámicos para el modo oscuro o claro. Un simple color semitransparente puede servir, pero también puedes volverte loco con [mix-blend-mode](https://developer.mozilla.org/en-US/docs/Web/CSS/mix-blend-mode) y [backdrop-filter](https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter).

```css
.site {
  fondo: transparente;
}

/* Ejemplo: Superposición negra semitransparente. */
body::before {
  contenido: "";
  posición: fija;
  recuadro: 0;
  z-index: -1000;
  visualización: bloque;
  color de fondo: rgb(0 0 0 / 50%);
}

/* Ejemplo: Filtros de fondo (cuidado con el rendimiento) */
body::before {
  contenido: "";
  posición: fija;
  recuadro: 0;
  z-index: -1000;
  visualización: bloque;
  filtro de fondo: desenfoque(3px) sepia(90%) brillo(0,5);
  -webkit-backdrop-filter: blur(3px) sepia(90%) brightness(0.5); /* Para Safari. */
}
```

#### Merge Top-Header & Navigation

![Customizer HSL Sliders](repo/assets/merged_header_and_nav_preview.jpg?raw=true)

¿Quieres la navegación junto a la cabecera alineada en la parte superior, sin cambiar el HTML? Difícil, pero posible. Los valores reales y el resultado dependerán del tamaño de tu cabecera y del número de elementos del menú, ya que esto puede dar lugar a elementos superpuestos si no tienes cuidado. Además, dependiendo de tu fondo, puede que necesites ajustar algunos colores tanto para el modo claro como para el oscuro.

**2024/01/23:** Actualizado el reset en CSS pegajoso.

**Título del sitio - Tamaño mínimo:** 40px<br>
**Título del sitio - Tamaño máximo:** 40px<br>
**Línea de etiquetas - Tamaño mínimo:** 12px<br>
**Tamaño máximo de la etiqueta:** 16px

```css
@media only screen and (min-width: 1024px) {
  .main-navigation {
    margin-top: calc(-1 * var(--navigation-height) + 5px); /* Ajústelo a su sitio. */
  }

  .main-navigation__wrapper {
    posición: relativa;
    alinear-elementos: flex-end;
    flex-dirección: columna-reverso;
  }

  .main-navigation__right {
    posición: absoluta;
    arriba: 0;
    derecha: 0;
    transform: translateY(calc(-100% - 2px)); /* Ajústelo a su sitio. */
  }

  .main-navigation.is-sticky .main-navigation__left {
    pantalla: flex;
    justify-content: flex-end; /* Utilice el espacio intermedio si añade el bloque opcional ::before.  */
    separación: 2rem;
    padding-left: 1rem;
    anchura: 100%;
    max-width: 100%;
  }

  /* Opcional: Añade esto si quieres mostrar algo en el lado izquierdo cuando la navegación se vuelve pegajosa. */
  .main-navigation.is-sticky .main-navigation__left::before {
    contenido: "FICTIONEER";
    flex: 0 0 auto;
    visualización: bloque;
    font-weight: 700;
    altura de línea: var(--navigation-height);
  }
}

/* Opcional: Añade esto si quieres restablecer los cambios cuando la navegación se vuelva pegajosa. */
:root:not(.no-nav-sticky) body:not(.scrolled-to-top) .main-navigation.is-sticky .main-navigation__wrapper {
  flex-dirección: fila;
}

/* Añadido el 23/01/2024. */
:root:not(.no-nav-sticky) body:not(.scrolled-to-top) .main-navigation.is-sticky .main-navigation__left {
  justify-content: flex-start;
  relleno-izquierda: 0;
}

:root:not(.no-nav-sticky) body:not(.scrolled-to-top) .main-navigation.is-sticky .main-navigation__right {
  transformar: ninguna;
}
```

#### Overlay Navigation

¿Quieres la navegación encima de la imagen de cabecera? Sólo tienes que ir a **Apariencia > Personalizar > Diseño** y cambiar el Estilo de cabecera a "Superponer". También puedes ajustar la imagen de cabecera, el título, el eslogan o el logotipo (si lo hay). Las personalizaciones adicionales requieren algo de CSS. Tenga en cuenta que los siguientes fragmentos son *ejemplos*; no los copie y pegue sin sentido, ajústelos a sus necesidades.

```css
/* Barra de navegación semitransparente. */
.header-style-overlay .main-navigation {
  --opacidad de inicio de fondo de navegación: .72;
  --opacidad del fondo de navegación: .9;
  backdrop-filter: blur(4px); /* Difumina todo lo que hay detrás de la barra; puede disminuir el rendimiento del renderizado. */
  -webkit-backdrop-filter: blur(4px); /* ... lo mismo pero funciona en Safari. */
}

/* Elimina la sombra cuando el sitio se desplaza a la parte superior. */
.header-style-overlay .scrolled-to-top {
  --navigation-drop-shadow: none;
}

/* Aumentar la altura en móviles y superiores. */
:root {
  --altura de navegación: 48px;
}

/* Aumentar la altura a partir del escritorio. */
@media only screen and (min-width: 1024px) {
  :root {
    --altura de navegación: 60px;
  }
}

/* Evita aumentar la altura de los submenús. */
.sub-menú {
  --altura de navegación: 40px;
}
```

#### Card Grids

Puedes cambiar la anchura mínima de las tarjetas y el espaciado de los espacios en **Apariencia > Personalizar > Diseño**, normalmente en combinación con un aumento de la anchura del sitio. Pero si quieres tener también una cuadrícula en las plantillas de páginas de lista, por ejemplo Historias y Capítulos, necesitas algo de CSS personalizado. Tenga en cuenta que la clase `.card-list` puede ser conveniente para convertir todas las listas de tarjetas en cuadrículas, pero puede tener efectos secundarios no deseados, ya que la clase se utiliza en muchos lugares. Es mejor ser específico.

```css
/* Apuntar a los ID únicos de las listas es seguro. */
#list-of-stories, #list-of-chapters {
  --card-list-row-gap: max(4cqw, 2rem); /* ¡Más grande por defecto que las cuadrículas shortcode! */
  --card-list-col-gap: max(4cqw, 2rem); /* ¡Más grande por defecto que las cuadrículas de los shortcodes! */
  --card-list-template-columns: repeat(auto-fill, minmax(308px, 1fr));
}

/* Fallback para navegadores antiguos que no soportan consultas de contenedor. */
@soporta (anchura: 1cqw) {
  #list-of-stories, #list-of-chapters {
    --card-list-row-gap: 2rem;
    --card-list-col-gap: 2rem;
  }
}
```

#### Custom Header/Page Style

Tanto el encabezado como el estilo de página pueden configurarse como "CSS personalizado", pero notarás que no aparece ninguna interfaz. Eso es porque se supone que el CSS está en la sección **CSS adicional**. La opción sólo aplica las clases raíz necesarias para que los estilos funcionen en primer lugar. Hay muchas maneras de modificar la forma de un contenedor, pero normalmente se reduce a un [polígono](https://developer.mozilla.org/en-US/docs/Web/CSS/basic-shape/polygon), [imagen de máscara](https://developer.mozilla.org/en-US/docs/Web/CSS/mask-image), o ambos. Ten en cuenta que esto **no** es fácil.

Un buen punto de partida para las máscaras es [haikai](https://app.haikei.app/), pero añade `preserveAspectRatio="none"` después del viewBox o el SVG no se estirará correctamente. Asegúrate de que tu estilo se vea bien tanto en el escritorio como en el móvil, lo que puede ser difícil de conseguir. Los siguientes ejemplos utilizan las funciones [clamp()](https://developer.mozilla.org/en-US/docs/Web/CSS/clamp) y [calc()](https://developer.mozilla.org/en-US/docs/Web/CSS/calc), pero las consultas de medios o un enfoque que se ajuste a todo también funcionan.

```css
/* Ejemplo: Estilo de página Wave */

:root.page-style-mask-image-wave-a:not(.minimal) .main__background {
  filtro: var(--page-drop-shadow);
}

:root.page-style-mask-image-wave-a:not(.minimal) .main__background::before {
  --mp: top calc(-1 * clamp(5px, 0.7633587786vw + 2.1374045802px, 8px)) left 0, top clamp(22px, 2.2900763359vw + 13.4122137405px, 31px) left 0; /* mask-position */
  --ms: 100% clamp(28px, 3.3078880407vw + 15.5954198473px, 41px), 100% calc(100% - clamp(22px, 2.2900763359vw + 13.4122137405px, 31px)); /* mask-size */
  --mr: repeat-x, no-repeat; /* mask-repeat */
  --mi: url('data:image/svvg+xml,%3Csvg width="100%25" height="100%25" id="svg" viewBox="0 0 0 1440 690" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" class="transition duration-300 ease-in-out delay-150"%3E%3Cpath d="M 0,700 L 0,262 C 85.6267942583732,192.2488038277512 171.2535885167464,122.49760765550238 260,138 C 348.7464114832536,153.50239234449762 440.6124401913876,254.25837320574163 541,315 C 641.3875598086124,375.74162679425837 750.2966507177035,396.46889952153106 854,374 C 957.7033492822965,351.53110047846894 1056.200956937799,285.86602870813397 1153,260 C 1249.799043062201,234.133971291866 1344.8995215311006,248.06698564593302 1440,262 L 1440,700 L 0,700 Z" stroke="none" stroke-width="none" stroke-width="0" fill="%23000000" fill-opacity="1" class="transition-all duration-300 ease-in-out delay-150 path-0"%3E%3C/path%3E%3C/svg%3E'), var(--data-image-2x2-black); /* máscara-imagen */
  border-radius: 56px / clamp(6px, 1.5267175573vw + 0.2748091603px, 12px);
}
```

```css
/* Ejemplo: Estilo de página maltratada */

:root.page-style-polygon-chamfered:not(.minimal) .main__background {
  filtro: var(--page-drop-shadow);
}

:root.page-style-polygon-chamfered:not(.minimal) .main__background::before {
  --m: clamp(6px, 1.3392857143vw + 1.7142857143px, 12px);
  clip-path: polygon(0% var(--m), var(--m) 0%, calc(100% - var(--m)) 0%, 100% var(--m), 100% calc(100% - var(--m)), calc(100% - var(--m)) 100%, var(--m) 100%, 0% calc(100% - var(--m))); /* Lista de puntos X/Y calculados dinámicamente. */
}
```

```css
/* Ejemplo: Estilo de imagen de cabecera */

:root.header-image-style-polygon-battered .header-background {
  border-radius: 0 !important;
}

:root.header-image-style-polygon-battered .header-background__wrapper {
  border-radius: 0 !important;
  clip-path: var(--polygon-battered-half);
}

@media only screen and (min-width: 768px) {
  :root.header-image-style-polygon-battered .header-background__wrapper {
    margin-left: 4px; /* Espacio para la sombra que se cortaría. */
    margin-right: 4px; /* Espacio para sombra que se cortaría. */
    clip-path: var(--polygon-battered);
  }
}

:root.header-image-style-polygon-battered:not(.inset-header-image) .header-background__wrapper {
  --polígono-destrozado: var(--polígono-destrozado-mitad);
  margin-left: 0;
  margin-right: 0;
}
```

#### Uppercase Site Title

En lugar de simplemente poner la cadena en mayúsculas, lo que la pondría en mayúsculas en todas partes, incluidos los resultados de los motores de búsqueda, puedes usar un poco de CSS para conseguirlo de forma limpia y adecuada. Tendrás que inspeccionar tu sitio para encontrar el selector correcto, pero debería ser cualquiera de estos: `header__title-link`, `top-header__link`, o `wide-header-identity__title-link`.

```css
/* O usa los tres como un vago. */

.header__title-link,
.top-header__link,
.wide-header-identity__title-link {
  text-transform: uppercase;
}

```

#### Dynamic Main Container Offset

Puedes añadir un desplazamiento al contenedor principal en el Personalizador, pero ese es estático. ¿Quizás necesitas uno que cambie con el ancho del sitio o algo así? Hay muchas maneras de lograrlo, pero probablemente quieras usar una función `clamp()`. Para ahorrarte las matemáticas de bachillerato, puedes usar una [herramienta online para calcularlo](https://utopia.fyi/clamp/calculator/). Asigna el resultado a la propiedad personalizada `--main-offset` en un ámbito más profundo que la raíz.

```css
/* Ejemplo: Esto interpola de 32px a 375px de ancho y 48px a 768px de ancho. */

cuerpo {
  --desplazamiento principal: clamp(32px, 16.7328px + 4.0712vi, 48px);
}
```

#### Make Single Pages Wider

¿Quizás quieras hacer tu página de aterrizaje más ancha, para mostrar más tarjetas o tarjetas más grandes? Pero sin cambiar la anchura global del sitio, porque eso parecería una tontería para los capítulos? Todo lo que necesita para ello es el ID de su página, que puede encontrar en la barra de direcciones de su navegador en la pantalla de edición, y la propiedad personalizada `--site-width`. Haremos los cambios en ese ID y sólo en ese ID; puedes repetir el proceso para otros IDs, por supuesto.

WordPress añade automáticamente una clase con el ID al cuerpo, como `.page-id-69`. Usando eso como ámbito, puede afectar a todo el sitio sólo para esta instancia. Sin embargo, puede que quiera limitarlo a la clase contenedora `.main`. De lo contrario, su sitio podría mostrar parpadeos extremos de diseño cuando el usuario visita otra página. Obviamente, usted puede hacer más que simplemente aumentar el ancho.

```css
/* Ejemplo: Aumentar el ancho del contenedor principal en la página con ID 69. */

.page-id-69 .main {
  --anchura del sitio: 1300px;
}
```

#### Border Under Navigation

Puede eliminar la sombra de la barra de navegación y añadir un borde en su lugar. Tenga en cuenta que la barra de navegación adquiere un color de fondo cuando se desplaza hacia abajo, que también puede personalizar o desactivar. También puede ser necesario un relleno adicional para que se vea bien. Si quieres que el borde sólo aparezca cuando la navegación esté pegajosa, añádelo a `.main-navigation__background` en su lugar.

```css
/* Ejemplo: Borde semitransparente en negro (modo claro) y blanco (modo oscuro). */

.main-navigation__background {
  filtro: ninguno;
  box-shadow: ninguna;
}

.main-navigation {
  borde inferior 1px solid rgb(0 0 0 / 7%);
}

:root[data-mode="dark"] .main-navigation {
  borde inferior 1px solid rgb(255 255 255 / 7%);
}
```

#### Dark/Light Variants for the Logo/Header Image

No se pueden establecer diferentes variantes de imagen de logo/encabezado para modo claro u oscuro. Esto se ha eliminado en una versión muy anterior del tema *porque era un lío*, con cada campo de imagen duplicado. De todos modos, se puede lograr esto con un poco de CSS también - o JavaScript, pero que se complica. Las dos opciones más fáciles son reemplazar la imagen o aplicar un [filtro](https://developer.mozilla.org/en-US/docs/Web/CSS/filter), siendo preferible esta última si puedes hacer que funcione.

```css
:root[data-mode="dark"] .custom-logo {
  content: url("https..."); /* Sobreescribir el src del img. */
}

:root[data-mode="dark"] .header-background__image {
  filter: invert(1); /* Invierte la imagen, si aún se ve bien. */
}
```

#### Move the Title/Logo

![Customizer HSL Sliders](repo/assets/customizer_move_title_logo.jpg?raw=true)

Para mover el título o el logotipo, necesitas un poco de CSS personalizado. Esto se puede añadir directamente en **Apariencia > Personalizar > CSS adicional**. Dependiendo de si tienes un logo o no, tendrás una de las siguientes combinaciones HTML/CSS (y alguna más, pero esta es la parte relevante).

Lo que te interesa son las propiedades `position`, `transform`, y/o `text-align`. `transform` cambia el punto de origen (kinda) del elemento, que normalmente es la esquina superior izquierda, para que se pueda desplazar mejor. El `text-align` sólo funciona en el título. Si sobreescribes esos valores, puedes desplazar el elemento o el texto. Si quieres desplazarlo desde la derecha o desde abajo, tienes que añadir `top: unset;` o `left: unset;` o ambos. Asegúrate de que el título o el logotipo se ajustan al móvil. Consulte las referencias [position](https://developer.mozilla.org/en-US/docs/Web/CSS/position), [transform](https://developer.mozilla.org/en-US/docs/Web/CSS/transform) y [text-align](https://developer.mozilla.org/en-US/docs/Web/CSS/text-align).

<table>
<tr>
<th>HTML</th>
<th>CSS</th>
</tr>
<tr>
<td>

```html
<header class="header hide-on-fullscreen">
  <div class="header__content">
    <div class="header__title">
      <div class="header__title-heading">
        <a href="#" class="header__title-link" rel="home">Título</a>
      </div>
      <div class="header__title-tagline">Línea de etiquetas</div>
    </div>
  </div>
</header>
```

</td>
<td>

```css
.header__title {
  posición: relativa;
  arriba: 40%;
  /* ... */
  transformar: translateY(-50%);
  /* ... */
}
```

</td>
</tr>
<tr></tr>
<tr>
<td>

```html
<header class="header hide-on-fullscreen">
  <div class="header__logo">
    <a href="#" class="custom-logo-link" rel="home">
      <img width="x" height="y" src="#">
    </a>
  </div>
</header>
```

</td>
<td>

```css
.header__logo {
  posición: absoluta;
  arriba: 50%;
  izquierda: 50%;
  transformar: translate3d(-50%, -50%, 0);
  /* ... */
}
```

</td>
</tr>
</table>

### Minimum/Maximum Values

![Customizer HSL Sliders](repo/assets/dynamic_scaling_demo.gif?raw=true)

Los valores mínimo y máximo encontrados en el Personalizador se utilizan para calcular [pinzas](https://developer.mozilla.org/en-US/docs/Web/CSS/clamp), que son responsables del escalado dinámico del sitio. Viewport se refiere a las dimensiones reales de la pantalla, de nuevo con un mínimo (fijo) y un máximo (ancho del sitio). Todo se interpola entre esos valores. Utiliza los modos de visualización responsive incorporados en la parte inferior del Personalizador para revisar tus cambios. No olvides marcar "Usar propiedades de diseño personalizadas" o tus ajustes serán ignorados.

### Menus

![Menu Screen](repo/assets/menu_screen_options.jpg?raw=true)

Fictioneer viene con dos ubicaciones de menú, **Navegación** y **Menú de Pie**, situadas precisamente donde cabría esperar. Puedes leer cómo crear y añadir menús en la [documentación oficial](https://codex.wordpress.org/WordPress_Menu_User_Guide). Lo único destacable aquí son las clases CSS especiales que puedes asignar a los elementos del menú para conseguir ciertos efectos (separados por espacios en blanco). Asegúrate de activar las propiedades adicionales del menú en Opciones de pantalla en la parte superior.

En el escritorio, los submenús se muestran como desplegables. En móvil, la **Navegación** muestra o bien los elementos del nivel superior en una pista desplazable (desbordamiento) o bien sólo el botón del menú móvil (colapso). Puedes configurarlo en el **Personalizador**. El menú móvil es una lista desplegada de todos los elementos si no se excluyen específicamente con clases CSS opcionales.

* `not-in-mobile-menu`: Como se puede adivinar, esto ocultará el elemento de menú en el menú móvil. Sin embargo, los elementos del submenú se seguirán mostrando, por lo que puede utilizar esta opción para ocultar los padres desplegables superfluos.
* `static-menu-item`: Para elementos de menú sin enlace. Cambia el cursor y no se puede seleccionar por teclado (los subelementos sí).

### Queries

Para mantener la base de datos ordenada, Fictioneer no guarda ni conserva metavalores "falsos" (`""`, `0`, `null`, `false`, `[]`). Esto puede causar problemas con las [meta queries](https://developer.wordpress.org/reference/classes/wp_query/#custom-field-post-meta-parameters) que buscan estos valores, porque los mensajes sin ellos son excluidos de los resultados. Los problemas más comunes son `fictioneer_story_sticky`, `fictioneer_story_hidden`, y `fictioneer_chapter_hidden`. Existen múltiples soluciones para esto.

**1) Utilizar una metaconsulta ampliada (pero más lenta):**

```php
$cargas_consulta = array(
  ...
  'meta_query' => array(
    'relación' => 'OR',
    array(
      'key' => 'fictioneer_chapter_hidden',
      'value' => '0'
    ),
    array(
      'key' => 'fictioneer_chapter_hidden',
      'compare' => 'NOT EXISTS'
    )
  )
);
```

**2) Permitir guardar los campos meta "falsos" deseados:**

```php
add_filter( 'fictioneer_filter_falsy_meta_allow_list', function( $allowed ) {
  $allowed[] = 'fictioneer_story_sticky'; // Por ejemplo

  devolver $permitido;
});
```

A continuación, puede añadir los campos meta que falten con el valor `0` en **Fictioneer > Herramientas**. El filtro también evitará que esas filas se eliminen al optimizar la base de datos.

**3) Enganche en `posts_clauses` (complicado; ejemplo del tema):**

```php
/**
 * Filtra las historias pegajosas a la parte superior y tiene en cuenta los campos meta que faltan.
 *
 * @desde 5.7.3
 * @desde 5.9.4 - Comprobar orderby por componentes, ampliar lista de permitidos.
 *
 * @param array $clauses Un array asociativo de cláusulas SQL WP_Query.
 * @param WP_Query $wp_query La instancia de WP_Query.
 *
 * @return string Las cláusulas SQL actualizadas o no modificadas.
 */

function fictioneer_clause_sticky_stories( $clauses, $wp_query ) {
  global $wpdb;

  // Configuración
  $vars = $wp_query->query_vars;
  $queries_permitidas = ['historias_lista', 'ultimas_historias', 'ultimas_historias_compactas', 'autor_historias'];
  $allowed_orderby = ['', 'date', 'modified', 'title', 'meta_value', 'name', 'ID', 'post__in'];
  $given_orderby = $vars['orderby'] ?? [''];
  $given_orderby = is_array( $given_orderby ) ? $given_orderby : explode( ' ', $vars['orderby'] );

  // Devuelve si la consulta no está permitida
  si (
    ! in_array( $vars['fictioneer_query_name'] ?? 0, $allowed_queries ) ||
    ! empty( array_diff( $given_orderby, $allowed_orderby ) )
  ) {
    devolver $clausuras;
  }

  // Cláusulas de actualización para poner a 0 la metatecla que falta
  $clauses['join'] .= " LEFT JOIN $wpdb->postmeta AS m ON ($wpdb->posts.ID = m.post_id AND m.meta_key = 'fictioneer_story_sticky')";
  $clauses['orderby'] = "COALESCE(m.meta_value+0, 0) DESC, " . $clauses['orderby'];
  $clauses['groupby'] = "$wpdb->posts.ID";

  // Pasar a consulta
  devolver $clausuras;
}

if ( FICTIONEER_ENABLE_STICKY_CARDS ) {
  add_filter( 'posts_clauses', 'fictioneer_clause_sticky_stories', 10, 2 );
}
```

### Font Awesome

Fictioneer carga la versión gratuita de [Font Awesome 6.4.2](https://fontawesome.com/) por defecto y a menos que quieras usar una diferente o encuentres problemas de compatibilidad (normalmente cuando un plugin incluye FA también), no se requiere ninguna acción aquí.

* Si desea incluirlo mediante un plugin (quizás un Pro Kit) o una función personalizada, desactive la versión del tema en **Fictioneer > General > Compatibilidad**.

* Si desea cambiar el enlace CDN y el hash de integridad, hágalo sobrescribiendo las constantes `FICTIONEER_FA_CDN` y `FICTIONEER_FA_INTEGRITY` en un [tema hijo](https://developer.wordpress.org/themes/advanced-topics/child-themes/). Puede establecer la integridad a `null` si no es necesario.

### Custom Fonts

**Nota:** La librería de fuentes introducida en WP 6.5 ha sido desactivada debido a incompatibilidades con el tema, concretamente con el sistema de formateo de capítulos que permite elegir fuentes. Esto puede cambiar en el futuro, pero por ahora debe evitarse.

Puedes añadir fuentes personalizadas, bien subiendo una carpeta de configuración a `/themes/your-child-theme/fonts/` o con un CDN como Google Fonts. Esto último es mucho más conveniente, aunque también viola la GDPR y por lo tanto no se recomienda excepto para pruebas. Entregar las fuentes desde tu servidor es legalmente seguro, pero puede afectar al rendimiento si no [aprovechas la caché del navegador](#securing-wordpress--browser-caching) o utilizas un plugin de caché (que deberías).

A continuación se explican ambos métodos con el ejemplo de [Noto Sans](https://fonts.google.com/noto/specimen/Noto+Sans?noto.query=noto+sans), que también tiene grandes variantes para sistemas de escritura logográfica si lo necesita. Ten en cuenta que no todas las fuentes que encuentres en Internet son de uso gratuito.

Purga la caché del tema en **Fictioneer > Herramientas** después de añadir o eliminar una fuente. Puede que también tengas que forzar la actualización. Una vez que todo esté en orden y actualizado, podrás ver la fuente en **Fictioneer > Fuentes**. Con eso, puedes asignar las fuentes a partes específicas del tema en **Apariencia > Personalizar > Fuentes**. Más es posible con CSS personalizado.

#### A) Upload a font configuration folder

Este método requiere cierta preparación. Echa un vistazo a la carpeta de fuentes predeterminadas [roboto-serif](https://github.com/Tetrakern/fictioneer/tree/main/fonts/roboto-serif); encontrarás varios archivos .woff2, un archivo .css y un archivo .json. Puedes replicarlo con relativa facilidad utilizando el [Google Fonts Webhelper](https://gwfh.mranftl.com/fonts/noto-sans) y un editor de texto de tu elección. Busca "Noto Sans", selecciona los conjuntos de caracteres y estilos que necesites (normalmente entre 300 y 700) y cambia el prefijo de la carpeta a `../fonts/noto-sans/`. Copia el CSS proporcionado en un nuevo archivo font.css, descarga y descomprime el archivo, pon todo en una carpeta "noto-sans". Puedes renombrar los archivos, eliminar los comentarios y minificar el CSS si tienes paciencia. Sólo asegúrate de que todo sigue siendo correcto.

El archivo font.json puede parecer un poco complicado, pero en realidad es sobre todo informativo. Los únicos pares nombre-valor de importancia en este momento son **skip**, **chapter**, **remove**, **key**, **name** y **family**. Le animamos a que rellene el resto, por si fuera necesario en el futuro. Aquí tienes uno ya hecho para Noto Sans:

<detalles>
  <sumario>Explicaciones</sumario><br>

| Clave Tipo Explicación
| :--- | :---: | :---
| skip | boolean | Si se omite la agrupación de CSS (si se carga por otros medios o una fuente del sistema). Por defecto `false`.
| remove | boolean | Si eliminar la fuente en lugar de añadirla. Por defecto `false`.
| chapter | boolean | Si la fuente está disponible en los capítulos. Por defecto `false`.
| version | string | Número de versión. Vacío por defecto.
| clave * | cadena | Identificador único y clave para la matriz asociativa de fuentes.
| nombre * | cadena | Nombre para mostrar.
| family * | string | Valor CSS para la propiedad font-family (sin comillas extra).
| alt | string | Pila de valores de font-family de reserva, por ejemplo "Helvetica, Arial". Vacía por defecto.
| Tipo | cadena | Tipo de diseño, como "sans-serif", "serif" o "monospace". Vacío por defecto.
| Estilos de fuente disponibles. Vacío por defecto.
| weights | integer[] | Font-weights disponibles. Vacío por defecto.
| charsets | string[] | Sistemas de escritura soportados. Vacío por defecto.
| formatos | cadena[] | Formatos de los archivos de fuentes, como .woff2, .woff, .ttf, etc. Vacío por defecto.
| about | string | Descripción de la fuente para la página de administración. Vacía por defecto.
| nota | cadena | Nota especial sobre la fuente para la página de administración. Vacía por defecto.
| vista previa | cadena | Cambia la frase de ejemplo que se muestra en la página de administración. Vacía por defecto.
| Colección de sub-objetos que listan las fuentes para la fuente. Vacía por defecto.

\* Pares clave-valor requeridos.

</detalles><br>

```json
{
  "skip: false
  "eliminar": falso,
  "capítulo": true,
  "version": "35",
  "key": "noto-sans",
  "nombre": "Noto Sans",
  "family": "Noto Sans",
  "alt": "",
  "tipo": "sans-serif",
  "styles": ["normal", "cursiva"],
  "pesos": [300, 400, 500, 600, 700],
  "charsets": ["cirílico", "cirílico-ext", "devanagari", "griego", "griego-ext", "latín", "latín-ext", "vietnamita"],
  "formats": ["woff2"],
  "acerca de": "Noto Sans es un diseño sin modulación ("sans serif") para textos en las escrituras latina, cirílica y griega, que también es adecuado como opción complementaria para otras fuentes Noto Sans específicas de escritura.",
  "nota": "",
  "vista previa": "El zorro marrón rápido salta sobre el perro perezoso",
  "fuentes": {
    "googleFonts": {
      "nombre": "Google Fonts",
      "url": "https://fonts.google.com/noto/specimen/Noto+Sans"
    },
    "googleWebfontsHelper": {
      "name": "Google Webfonts Helper",
      "url": "https://gwfh.mranftl.com/fonts/noto-sans?subsets=cyrillic,cyrillic-ext,devanagari,greek,greek-ext,latin,latin-ext,vietnamese"
    }
  }
}
```

Puede encontrar una colección de carpetas de fuentes prefabricadas en [/repo/fonts/](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts). Actualmente disponibles:

* [Noto Sans](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans): La fuente Noto Sans de Google.
* [Noto Sans JP](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-jp): Variante de Noto Sans para japonés.
* [Noto Sans KR](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-kr): Variante de Noto Sans para coreano.
* [Noto Sans TC](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-tc): Variante de Noto Sans para chino tradicional.
* [Noto Sans SC](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/noto-sans-sc): Variante de Noto Sans para chino simplificado.
* [Elite especial](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/special-elite): Fuente similar a la de las máquinas de escribir, adecuada para títulos o secciones especiales.
* [Verdana](https://github.com/Tetrakern/fictioneer/tree/main/repo/fonts/verdana): fuente segura para la web y ejemplo para añadir fuentes preinstaladas en dispositivos.

#### B) Load from the Google Fonts CDN

Visita [Google Fonts](https://fonts.google.com/) y busca una fuente que te guste. En la pestaña **Especimen**, desplázate hasta **Styles** y selecciona lo que necesites, normalmente todo lo que vaya de 300 a 700 si quieres cubrir todos los casos del tema. Si faltan algunos estilos, puedes seguir utilizando la fuente, sólo que quizá no como principal. A la derecha, en **Utilizar en la web**, elige la opción **<link\>** y copia el enlace del atributo href (nada más). Asegúrese de que sólo se selecciona una fuente, porque los enlaces de fuentes agrupadas no son entendidos actualmente por el tema.

```
https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap
```

### Constants

Algunas opciones no están disponibles en la configuración porque temperar con ellas puede romper el tema o resultar en un comportamiento inesperado. Esas opciones se definen mediante constantes en el **function.php**. Si quieres cambiarlas, necesitas un [tema hijo](https://developer.wordpress.org/themes/advanced-topics/child-themes/) o acceso a tu **wp-config.php**. Sólo tienes que anularlas en el propio **function.php** o config del tema hijo, ¡pero sólo si sabes lo que estás haciendo!

```php
define( 'CONSTANT_NAME', valor );
```

| Constante Tipo Explicación
| :--- | :---: | :---
CHILD_VERSION | string||null | Número de versión del tema hijo. Por defecto `null`.
CHILD_NAME | string||null | Nombre del tema hijo. Por defecto `null`.
| FICTIONEER_OAUTH_ENDPOINT | string | URI slug para llamar al script OAuth. Por defecto `'oauth2'`.
| FICTIONEER_EPUB_ENDPOINT | string | URI slug para llamar al script ePUB. Por defecto `'download-epub'`.
| FICTIONEER_LOGOUT_ENDPOINT | string | URI slug para llamar al script de cierre de sesión. Por defecto `'fictioneer-logout'`.
| FICTIONEER_PRIMARY_FONT_CSS | string | Nombre CSS de la fuente primaria. Por defecto `'Open Sans'`.
| FICTIONEER_PRIMARY_FONT_NAME | string | Nombre para mostrar de la fuente primaria. Por defecto `'Open Sans'`.
| FICTIONEER_TTS_REGEX | cadena | Divide el texto del capítulo en frases para la función de texto a voz. Default `'([.!?:"\'\u201C\u201D])\s+(?=[A-Z"\'\u201C\u201D])'`.
| FICTIONEER_DEFAULT_CHAPTER_ICON | string | Icono del capítulo Clases de Font Awesome (Gratis). Por defecto `'fa-solid fa-book'`.
| FICTIONEER_LATEST_UPDATES_LI_DATE | string | Formato de fecha del elemento de la lista del shortcode Últimas actualizaciones. Por defecto `'M j'`.
| FICTIONEER_LATEST_UPDATES_FOOTER_DATE | string | Formato de fecha para el pie de página del shortcode Latest Updates. Por defecto `"M j, 'y"`.
| FICTIONEER_LATEST_CHAPTERS_FOOTER_DATE | string | Formato de fecha para el pie de página de los últimos capítulos. Por defecto `"M j, 'y"`.
| FICTIONEER_LATEST_STORIES_FOOTER_DATE | string | Formato de fecha para el pie de página del shortcode Latest Stories. Por defecto `"M j, 'y"`.
| FICTIONEER_CARD_STORY_LI_DATE | string | Formato de fecha de los elementos de la lista de tarjetas de historia. Por defecto `"M j, 'y"`.
| FICTIONEER_CARD_STORY_FOOTER_DATE | cadena | Formato de fecha de pie de página. Por defecto `"M j, 'y"`.
| FICTIONEER_CARD_CHAPTER_FOOTER_DATE | string | Formato de fecha de pie de capítulo. Por defecto `"M j, 'y"`.
| FICTIONEER_CARD_COLLECTION_LI_DATE | cadena | Formato de fecha del elemento de la lista de tarjetas de colección. Por defecto `"M j, 'y"`.
| FICTIONEER_CARD_COLLECTION_FOOTER_DATE | cadena | Formato de fecha de pie de página de la tarjeta de colección. Por defecto `"M j, 'y"`.
| FICTIONEER_CARD_POST_FOOTER_DATE | string | Formato de fecha de pie de página. Por defecto `"M j, 'y"`.
| FICTIONEER_CARD_PAGE_FOOTER_DATE | string | Formato de fecha de pie de página. Por defecto `"M j, 'y"`.
| FICTIONEER_CARD_ARTICLE_FOOTER_DATE | string | Formato de fecha del pie de la ficha del artículo. Por defecto `"M j, 'y"`.
| FICTIONEER_STORY_FOOTER_B480_DATE | string | Formato de fecha de pie de página (<= 480px). Por defecto `"M j, 'y"`.
| FICTIONEER_FA_CDN | string | Font Awesome CDN URL.
| FICTIONEER_FA_INTEGRITY | cadena | Integridad de Font Awesome hash SHA384.
| FICTIONEER_DISCORD_EMBED_COLOR | string | Código de color para las notificaciones de Discord. Por defecto `'9692513'`.
| FICTIONEER_TRUNCATION_ELLIPSIS | string | Se aplica a las cadenas truncadas. Por defecto `...`.
| FICTIONEER_AGE_CONFIRMATION_REDIRECT | string | Redirigir URL si un visitante rechaza la confirmación de edad. Por defecto `https://search.brave.com/`.
| FICTIONEER_DEFAULT_SITE_WIDTH | integer | Ancho del sitio por defecto. Por defecto `960`.
| FICTIONEER_COMMENTCODE_TTL | integer | Cuanto tiempo los invitados pueden ver sus comentarios privados/no aprobados en _segundos_. Por defecto `600`.
| FICTIONEER_AJAX_TTL | integer | Cuanto tiempo cachear ciertas peticiones AJAX localmente en _millisegundos_. Por defecto `60000`.
| FICTIONEER_AJAX_LOGIN_TTL | integer | Cuánto tiempo cachear localmente las autenticaciones AJAX en _millisegundos_. Por defecto `15000`.
| FICTIONEER_AJAX_POST_DEBOUNCE_RATE | integer | Cuánto tiempo rebotar las peticiones AJAX del mismo tipo en _millisegundos_. Por defecto `700`.
| FICTIONEER_AUTHOR_KEYWORD_SEARCH_LIMIT | integer | Número máximo de autores en las sugerencias de búsqueda avanzada. Por defecto `100`.
| FICTIONEER_UPDATE_CHECK_TIMEOUT | integer | Tiempo de espera entre comprobaciones de actualizaciones de temas en _segundos_. Por defecto `43200`.
| FICTIONEER_API_STORYGRAPH_CACHE_TTL | integer | Cuánto tiempo se almacenan en caché las respuestas de Storygraph en _segundos_. Por defecto `3600`.
| FICTIONEER_API_STORYGRAPH_STORIES_PER_PAGE | integer | Cuántos elementos devuelve el punto final Storygraph `/stories`. Por defecto 10.
| FICTIONEER_MAX_CUSTOM_PAGES_PER_STORY | integer | Número máximo de páginas personalizadas por historia. Por defecto `4`.
| FICTIONEER_CHAPTER_FOLDING_THRESHOLD | integer | Umbral antes y después del plegado en las listas de capítulos. Por defecto `5`.
| FICTIONEER_SHORTCODE_TRANSIENT_EXPIRATION | integer | Duración de la expiración de los transitorios del shortcode en segundos. Por defecto `300`.
| FICTIONEER_STORY_COMMENT_COUNT_TIMEOUT | integer | Tiempo de espera entre actualizaciones del recuento de comentarios para historias en _segundos_. Por defecto `900`.
| FICTIONEER_REQUESTS_PER_MINUTE | integer | Peticiones máximas por minuto y acción si el límite de velocidad está activado. Por defecto `5`.
| FICTIONEER_QUERY_ID_ARRAY_LIMIT | integer | IDs máximos permitidos en argumentos de consulta 'post__{not}_in'. Por defecto `1000`.
| FICTIONEER_PATREON_EXPIRATION_TIME | integer | Tiempo hasta que los datos de Patreon de un usuario expiren en segundos. Por defecto `WEEK_IN_SECONDS`.
| FICTIONEER_PARTIAL_CACHE_EXPIRATION_TIME | integer | Tiempo hasta que un parcial cacheado expira en segundos. Por defecto `4 * HOUR_IN_SECONDS`.
| FICTIONEER_CARD_CACHE_LIMIT | integer | Número de tarjetas de historia almacenadas en caché si la función está activada. Por defecto `50`.
| FICTIONEER_CARD_CACHE_EXPIRATION_TIME | integer | Tiempo hasta que toda la caché de tarjetas de historia expire en segundos. Por defecto `HOUR_IN_SECONDS`.
| FICTIONEER_STORY_CARD_CHAPTER_LIMIT | integer | Número máximo de capítulos mostrados en las tarjetas de historia. Por defecto 3.
| FICTIONEER_QUERY_RESULT_CACHE_THRESHOLD | integer | Número de resultados de una consulta que deben ser almacenados en caché. Por defecto `50`.
| FICTIONEER_QUERY_RESULT_CACHE_LIMIT | integer | Número de resultados de consulta almacenados en caché si la función está activada. Por defecto `50`.
| FICTIONEER_QUERY_RESULT_CACHE_BREAK | integer | Limitar el número de cargas de caché de resultados de consulta grandes por petición. Por defecto `3`.
| FICTIONEER_CACHE_PURGE_ASSIST | boolean | Si se llama a la función de asistencia de purga de caché en las actualizaciones. Por defecto `true`.
| FICTIONEER_RELATIONSHIP_PURGE_ASSIST | boolean | Si se purgan las cachés de mensajes relacionados. Por defecto `true`.
| FICTIONEER_CHAPTER_LIST_TRANSIENTS | boolean | Si se almacenan en caché las listas de capítulos en las páginas de historias como Transients. Por defecto `true`.
| FICTIONEER_SHOW_SEARCH_IN_MENUS | boolean | Si se muestran los enlaces de las páginas de búsqueda en los menús. Por defecto `true`.
| FICTIONEER_THEME_SWITCH | boolean | Si mostrar el cambio de tema en los temas hijo (volver a la base). Por defecto `true`.
| FICTIONEER_ATTACHMENT_PAGES | boolean | Habilitar páginas para adjuntos (sin plantillas temáticas). Por defecto `false`.
| FICTIONEER_SHOW_OAUTH_HASHES | boolean | Si se muestran los hashes de OAuth ID en los perfiles de usuario (sólo admin). Por defecto `false`.
| FICTIONEER_DISALLOWED_KEY_NOTICE | boolean | Si mostrar feedback por contenido de comentario rechazado. Por defecto `true`.
| FICTIONEER_FILTER_STORY_CHAPTERS | boolean | Si se filtran los capítulos seleccionables por historia asignada. Por defecto `true`.
| FICTIONEER_COLLAPSE_COMMENT_FORM | boolean | Ocultar las entradas del formulario de comentarios hasta que se pulse sobre el área de texto. Por defecto `true`.
| FICTIONEER_API_STORYGRAPH_IMAGES | boolean | Si se añaden enlaces de imagen al Storygraph. Por defecto `true`.
| FICTIONEER_API_STORYGRAPH_HOTLINK | boolean | Si se permite el hotlinking de imágenes desde el Storygraph. Por defecto `false`.
| FICTIONEER_API_STORYGRAPH_CHAPTERS | boolean | Si se añaden capítulos al final del Storygraph `/stories`. Por defecto `true`.
| FICTIONEER_API_STORYGRAPH_TRANSIENTS | boolean | Si se almacenan en caché las respuestas del Storygraph como Transients. Por defecto `true`.
| FICTIONEER_ENABLE_STICKY_CARDS | boolean | Si permitir tarjetas adhesivas. Caro. Por defecto `true`.
| FICTIONEER_ENABLE_STORY_DATA_META_CACHE | boolean | Si "cachear" los datos de la historia en un campo meta. Por defecto `true`.
| FICTIONEER_ENABLE_MENU_TRANSIENTS | boolean | Si cachear los menús de navegación como transitorios. Por defecto `true`.
| FICTIONEER_ORDER_STORIES_BY_LATEST_CHAPTER | boolean | Si se ordenan las historias actualizadas según el último capítulo añadido, excluyendo las historias sin capítulos. Por defecto `false`.
| FICTIONEER_ENABLE_STORY_CHANGELOG | boolean | Si los cambios en la lista de capítulos deben ser registrados. Por defecto `true`.
| FICTIONEER_DEFER_SCRIPTS | boolean | Si se deben diferir los scripts o cargarlos en el pie de página. Por defecto `true`.
| FICTIONEER_ENABLE_ASYNC_ONLOAD_PATTERN | boolean | Si se utiliza el [onload pattern](https://www.filamentgroup.com/lab/load-css-simpler/) para la carga asíncrona de CSS. Por defecto `true`.
| FICTIONEER_SHOW_LATEST_CHAPTERS_ON_STORY_CARDS | boolean | Si mostrar los últimos capítulos en lugar de los primeros en las tarjetas de historia. Por defecto `false`.
| FICTIONEER_EXAMPLE_CHAPTER_ICONS | array | Colección de cadenas de clase de iconos Font Awesome de ejemplo.