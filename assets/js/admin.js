/* * PusztaPlay Magic Login - Admin DOM Vadász
 * Kíméletlenül levadássza a szövegalapú szemetet a profiloldalon.
 */

document.addEventListener('DOMContentLoaded', function() {
    const hideByText = [
        'Elementor AI állapot',
        'Twitter',
        'Instagram',
        'Facebook profil URL',
        'Instagram profil URL',
        'LinkedIn profil URL',
        'MySpace profil URL',
        'Pinterest profil URL',
        'SoundCloud profil URL',
        'Tumblr',
        'Wikipedia',
        'X username',
        'YouTube profile',
        'Mastodon profile',
        'Életrajzi információ',
        'Yoast SEO schema fejlesztések',
        'Extra információ',
        'Munkáltatói információ',
        'Elementor Notes',
        'Yoast SEO beállítások'
    ];

    document.querySelectorAll('tr, h2, h3').forEach(function(el) {
        const text = el.textContent.trim();

        hideByText.forEach(function(label) {
            if (text.indexOf(label) !== -1) {
                if (el.tagName === 'TR') {
                    el.style.display = 'none';
                } else {
                    const next = el.nextElementSibling;
                    el.style.display = 'none';

                    if (next && (next.tagName === 'TABLE' || next.tagName === 'DIV')) {
                        next.style.display = 'none';
                    }
                }
            }
        });
    });
});