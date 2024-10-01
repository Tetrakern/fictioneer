<p align="center"><img src="./repo/assets/fictioneer_logo.svg?raw=true" alt="Fictioneer"></p>

<p align="center">
  <a href="https://github.com/Tetrakern/fictioneer"><img alt="Tema: 5.24" src="https://img.shields.io/badge/theme-5.24-blue?style=flat" /></a>
  <a href="LICENSE.md"><img alt="Licencia: GPL v3" src="https://img.shields.io/badge/license-GPL%20v3-blue?style=flat" /></a>
  <a href="https://wordpress.org/download/"><img alt="WordPress 6.1+" src="https://img.shields.io/badge/WordPress-%3E%3D6.1-blue?style=flat" /></a>
  <a href="https://www.php.net/"><img alt="PHP: 7.4+" src="https://img.shields.io/badge/php-%3E%3D7.4-blue?logoColor=white&style=flat" /></a>
  <a href="https://github.com/sponsors/Tetrakern"><img alt="Patrocinadores GitHub" src="https://img.shields.io/github/sponsors/tetrakern" /></a>
  <a href="https://ko-fi.com/tetrakern"><img alt="Apóyame en Ko-fi" src="https://img.shields.io/badge/-Ko--fi-FF5E5B?logo=kofi&logoColor=white&style=flat&labelColor=434B57" /></a>
</p>

<p align="center"><strong>Tema de WordPress y solución independiente para la publicación y lectura de <a href="https://en.wikipedia.org/wiki/Web_fiction">ficciones web</a>.</strong></p>.

<p align="center"><a href="https://fictioneer-theme.com/" target="_blank">Demo</a> &bull; <a href="https://github.com/Tetrakern/fictioneer/releases">Descarga</a> &bull; <a href="INSTALLATION.md">Instalación</a> &bull; <a href="CUSTOMIZE.md">Personalización</a> &bull; <a href="DOCUMENTATION.md">Documentación</a> &bull; <a href="API.md">API</a> &bull; <a href="DESARROLLO.md">Desarrollo</a> &bull; <a href="FAQ.md">FAQ</a> &bull; <a href="CREDITOS.md">Créditos</a> &bull; <a href="https://discord.gg/tVfDB7EbaP" target="_blank">Discos</a></p>
<br>

## About

Fictioneer se desarrolló originalmente para un grupo cerrado de autores y no se pensó para un lanzamiento público. Esto todavía se refleja en el código, que se toma varias libertades que no se consideran las mejores prácticas. Lo más probable es que nunca lo encuentres en las bibliotecas oficiales por esa razón, lo que significa que la instalación y las actualizaciones deben hacerse manualmente.

El tema está pensado para particulares y pequeños colectivos.

Fictioneer es de código abierto y completamente gratuito. Sin embargo, mantener y desarrollar un tema de estas proporciones requiere una cantidad considerable de tiempo y esfuerzo. Así que si te gusta Fictioneer y tienes la capacidad, por favor considera apoyarme en [Patreon](https://www.patreon.com/tetrakern), [Ko-fi](https://ko-fi.com/tetrakern), o [Patrocinadores GitHub](https://github.com/sponsors/Tetrakern).

## Key Features

historias, capítulos, colecciones y recomendaciones &bull; lector web personalizable &bull; códigos cortos &bull; conversión de texto a voz &bull; marcadores &bull; rastreador de progreso &bull; lightbox &bull; modo oscuro/claro &bull; conversor ePUB &bull; formulario de búsqueda avanzada &bull; barra lateral &bull; OAuth 2.0 (Discord, Google, Twitch y Patreon) &bull; puerta de contenido Patreon &bull; caducidad de la contraseña del post &bull; puerta de contenido para usuarios y roles &bull; gestor de roles &bull; diseño responsive &bull; cache aware &bull; sistema de comentarios personalizado &bull; comentarios AJAX &bull; comentarios privados &bull; suscripciones de respuesta de comentario &bull; enviar notificaciones a Discord &bull; optimización de motores de búsqueda &bull; GDPR compatible &bull; deslizadores de tono, saturación y luminosidad &bull; traducción lista &bull; compatible con Elementor

## Migration

Migrar una base de datos de WordPress existente puede ser una auténtica pesadilla. Dependiendo de lo que hayas hecho y de los temas y plugins que hayas usado antes, puedes encontrarte con graves problemas para hacer coincidir las estructuras de datos anteriores con las usadas en Fictioneer. Para hacerlo más fácil, echa un vistazo a la [guía de migración](MIGRATION.md).

## Free Plugins

[Fictioneer Email Notifications](https://github.com/Tetrakern/fictioneer-email-notifications): Permite a los lectores suscribirse a actualizaciones seleccionadas por correo electrónico. Puede elegir recibir notificaciones de todos los contenidos nuevos, de tipos de entradas específicos o de historias y taxonomías seleccionadas.

## Customization & Child Themes

[Child themes](https://developer.wordpress.org/themes/advanced-topics/child-themes/) son la mejor manera de personalizar Fictioneer si las opciones proporcionadas resultan insuficientes. Ni siquiera necesitas mucha experiencia en programación para esto, ya que hay muchas guías y fragmentos de código para ajustar WordPress a tus necesidades. Pero tenga en cuenta que Fictioneer no es un constructor de páginas, por lo que cambiar todo el diseño requiere experiencia. Los plugins pueden o no funcionar aquí.

* [Base child theme](https://github.com/Tetrakern/fictioneer-child-theme)
* [Tema infantil minimalista](https://github.com/Tetrakern/fictioneer-minimalist)
* Fragmentos CSS](INSTALLATION.md#css-snippets)
* [Fragmentos de acciones y filtros PHP](CUSTOMIZE.md)

Desde la versión 5.20.0, el tema es compatible con el plugin constructor de páginas/sitios [Elementor](https://elementor.com/). Esto te permite personalizar partes del tema sin conocimientos de programación, aunque se recomienda tener un poco de conocimiento sobre HTML y CSS. Más información sobre la implementación de Elementor en la [documentación](https://github.com/Tetrakern/fictioneer/blob/main/DOCUMENTATION.md#elementor).

## Commissions

Acepto comisiones por personalizaciones y nuevas características, *dentro de lo razonable.* Sólo tienes que escribirme a Discord, y podemos ver qué es factible. Sin embargo, ten en cuenta que cualquier característica por la que pagues puede ser añadida al tema para que todos la disfruten. Varias características ya han sido patrocinadas de esta manera. Compartir es cuidar.<sup>*</sup>

<sup>* Siempre que tenga sentido y no sea perjudicial.</sup>

## Support the Development

Fictioneer (hasta la versión 5.24) ha sido desarrollado por un solo autor, salvo los fragmentos de código [acreditados](CREDITS.md). Ha sido un esfuerzo agotador y no es sostenible, por lo que se agradece cualquier ayuda en el futuro. Si estás interesado, o quieres crear tu propia versión, echa un vistazo a las directrices [development](DEVELOPMENT.md), los hooks [action](ACTIONS.md), y los hooks [filter](FILTERS.md). Puedes encontrar un plugin base relacionado con el tema [aquí](https://github.com/Tetrakern/fictioneer-base-plugin). También puedes unirte al [Discord](https://discord.gg/tVfDB7EbaP).

**Traducciones:** Portugués de Brasil por [@c-cesar](https://github.com/c-cesar)

## Screenshots

<p align="center">Tema Base (Claro/Oscuro)</p> <p

![Screenshot Collage](repo/assets/fictioneer_preview.jpg?raw=true)

<p align="center">Tema Base - Barra lateral (Claro/Oscuro)</p> <p

![Screenshot Collage](repo/assets/two_columns_layout.jpg?raw=true)

<p align="center"><a href="https://github.com/Tetrakern/fictioneer-minimalist">Minimalist Child Theme</a> - Sidebar (Light/Dark)</p>

![Screenshot Collage](repo/assets/fictioneer_minimalist.jpg?raw=true)

<p align="center">Partes del Tema Base</p>

![Screenshot Collage](repo/assets/screenshots.jpg?raw=true)