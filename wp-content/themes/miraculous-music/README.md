# Miraculous Music WordPress Theme

A professional music streaming WordPress theme converted from the Miraculous HTML template.

## Features

- 🎵 **Music Player Integration** - Full jPlayer audio player with playlist support
- 🎨 **Modern Design** - Clean, responsive design optimized for music streaming
- 📱 **Mobile Responsive** - Works perfectly on all devices
- 🎸 **Custom Post Types** - Music, Albums, Artists, Playlists
- 🎼 **Genre Taxonomy** - Organize music by genres
- 🎚️ **Audio Controls** - Volume, shuffle, repeat, quality selector
- 📊 **Featured Sliders** - Swiper.js powered carousels for content
- 👤 **User Profiles** - User authentication and profile management
- 🔍 **Search Functionality** - Search music, artists, albums
- 📰 **Blog Support** - Built-in blog functionality

## Installation

1. Upload the `miraculous-music` folder to `/wp-content/themes/`
2. Activate the theme through the WordPress admin panel
3. Go to Appearance → Customize to configure theme settings

## Theme Structure

```
miraculous-music/
├── assets/              # CSS, JS, Images, Fonts
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files & plugins
│   ├── images/         # Theme images
│   └── fonts/          # Custom fonts
├── header.php          # Header template
├── footer.php          # Footer template
├── index.php           # Main template
├── single.php          # Single post template
├── page.php            # Page template
├── sidebar.php         # Sidebar template
├── functions.php       # Theme functions
└── style.css           # Theme stylesheet (required)
```

## Custom Post Types

### Music/Songs
- URL: `/music/`
- Supports: Title, Content, Featured Image, Excerpt, Comments

### Albums
- URL: `/albums/`
- Supports: Title, Content, Featured Image, Excerpt

### Artists
- URL: `/artists/`
- Supports: Title, Content, Featured Image, Excerpt

### Playlists
- URL: `/playlists/`
- Supports: Title, Content, Featured Image, Excerpt

## Widget Areas

- **Sidebar** - Main sidebar widget area
- **Footer 1-4** - Four footer widget areas
- **Header Widgets** - Header widget area (optional)

## Customization

### Theme Customizer Options

Go to **Appearance → Customize** to configure:

- Site Identity (Logo, Site Title, Tagline)
- Banner Settings (Title, Description, Background)
- Contact Information (Phone, Email, Address)
- Social Media Links (Facebook, Twitter, LinkedIn, Google+)
- Trending Songs Text

### Menus

Configure menus in **Appearance → Menus**:

- **Primary Menu** - Top navigation
- **Sidebar Menu** - Left sidebar navigation

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Opera (latest)

## Credits

- **Original HTML Template**: Miraculous by Kamleshyadav
- **WordPress Conversion**: Phong DT
- **jQuery**: https://jquery.com/
- **Bootstrap**: https://getbootstrap.com/
- **Swiper.js**: https://swiperjs.com/
- **jPlayer**: http://jplayer.org/
- **Font Awesome**: https://fontawesome.com/

## Support

For support and questions, please visit: https://github.com/phongdt29/suno-api

## License

GPL v2 or later

## Changelog

### Version 1.0.0
- Initial release
- Converted from HTML template to WordPress theme
- Added custom post types for Music, Albums, Artists, Playlists
- Integrated jPlayer audio player
- Added Swiper.js sliders
- Responsive design implementation
