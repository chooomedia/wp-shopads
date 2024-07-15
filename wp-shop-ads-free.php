<?php
/*
Plugin Name: Shopads
Plugin URI: https://www.chooomedia.de/wordpress-themes/#wpshopads
Description: Ein Plugin zum Abrufen und Anzeigen zufälliger Shopify-Produkte aus dem eigenen Shopify Webshop.
Version: 0.1
Author: CHOOOMEDIA
Author URI: https://chooomedia.de
License: GPL2
<!--<label>
    <input type="radio" name="shopify_display_option" value="post_type" <?php checked($display_option, 'post_type'); ?> onchange="toggleDisplayOption()" /> Post-Typ auswählen
</label>-->
*/

// Verhindern des direkten Zugriffs auf die Datei
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode-Funktion für Shopify-Anzeigen
function fetch_random_shopify_ads($atts) {
    $atts = shortcode_atts(array(
        'number_of_ads' => 3,
        'products_collection' => 'community-merch',
    ), $atts);

    // Zuweisen der Attribute zu Variablen
    $number_of_ads = intval($atts['number_of_ads']);
    $products_collection = sanitize_text_field($atts['products_collection']);

    ob_start();
    include(plugin_dir_path(__FILE__) . 'templates/shopify-ads.php');
    return ob_get_clean();
}
add_shortcode('shopify_ads', 'fetch_random_shopify_ads');

// Plugin-Einstellungen registrieren
function wp_shopify_register_settings() {
    add_option('shopify_shop_url', '');
    add_option('shopify_storefront_access_token', '');
    add_option('shopify_number_of_products', 3);
    add_option('shopify_collection', 'community-merch'); // Standard-Kollektion festlegen
    add_option('shopify_display_locations', array('post')); // Standard-Post-Typ festlegen
    add_option('shopify_display_option', 'shortcode'); // Standard-Anzeigeoption festlegen
    add_option('shopify_price_text', ''); // Standard-Anzeigeoption festlegen
    register_setting('wp_shopify_options_group', 'shopify_shop_url', 'wp_shopify_validate_url');
    register_setting('wp_shopify_options_group', 'shopify_storefront_access_token');
    register_setting('wp_shopify_options_group', 'shopify_number_of_products', 'wp_shopify_validate_number_of_products');
    register_setting('wp_shopify_options_group', 'shopify_collection'); // Kollektion speichern
    register_setting('wp_shopify_options_group', 'shopify_display_locations'); // Anzeigelocations speichern
    register_setting('wp_shopify_options_group', 'shopify_display_option'); // Anzeigeoption speichern
    register_setting('wp_shopify_options_group', 'shopify_price_text'); // Neues Feld für benutzerdefinierten Text
}
add_action('admin_init', 'wp_shopify_register_settings');

// URL-Validierung
function wp_shopify_validate_url($input) {
    if (filter_var($input, FILTER_VALIDATE_URL)) {
        return $input;
    } else {
        add_settings_error('shopify_shop_url', 'invalid-url', 'Bitte geben Sie eine gültige URL ein.');
        return get_option('shopify_shop_url');
    }
}

// Anzahl der Produkte Validierung
function wp_shopify_validate_number_of_products($input) {
    $input = intval($input);
    if ($input >= 3 && $input <= 5) {
        return $input;
    } else {
        add_settings_error('shopify_number_of_products', 'invalid-number', 'Bitte geben Sie eine Zahl zwischen 3 und 5 ein.');
        return get_option('shopify_number_of_products');
    }
}

// Einstellungsseite hinzufügen
function wp_shopify_register_options_page() {
    add_menu_page('Shop Ads', 'Shop Ads', 'manage_options', 'wp-shopify', 'wp_shopify_options_page', 'dashicons-cart', 20);
    add_submenu_page('wp-shopify', 'Einstellungen', 'Einstellungen', 'manage_options', 'wp-shopify', 'wp_shopify_options_page');
}
add_action('admin_menu', 'wp_shopify_register_options_page');

// Enqueue scripts
function shop_ads_enqueue_scripts() {
    wp_enqueue_style('shop-ads-style', plugin_dir_url(__FILE__) . 'assets/shop-ads-style.css');
    wp_enqueue_script('shop-ads-script', plugin_dir_url(__FILE__) . 'assets/shop-ads-script.js', array('jquery'), null, false);
}
add_action('wp_enqueue_scripts', 'shop_ads_enqueue_scripts');

// HTML für die Einstellungsseite
function wp_shopify_options_page() {
    ?>
    <style>
        .valid-input {
            border: 1px solid green;
            padding: 5px;
            border-radius: 3px;
        }
        .invalid-input {
            border: 1px solid red;
            padding: 5px;
            border-radius: 3px;
        }
        .wrap h1::after {
            content: 'v0.1';
            width: auto;
            padding: .1rem .6em;
            position: absolute;
            right: 1.2rem;
            background-color: #96bf47;
            border-radius: 40px;
            font: 8px sans-serif;
            color: #ffffff;
            line-height: 12px;
        }
    </style>
    <div class="wrap">
        <h1><img src="<?php echo plugin_dir_url(__FILE__) . 'assets/logo-wp-shopads.png'; ?>" alt="Logo" style="width: 32px; height: 32px; vertical-align: middle;"> Shop Ads Einstellungen</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wp_shopify_options_group'); ?>
            <?php do_settings_sections('wp_shopify'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="shopify_shop_url">Shopify Shop URL</label>
                        <p class="description">Geben Sie die URL Ihres Shopify-Shops ein.</p>
                    </th>
                    <td>
                        <?php $shop_url = get_option('shopify_shop_url'); ?>
                        <input type="url" id="shopify_shop_url" name="shopify_shop_url" value="<?php echo esc_attr($shop_url); ?>" class="regular-text <?php echo (filter_var($shop_url, FILTER_VALIDATE_URL)) ? 'valid-input' : 'invalid-input'; ?>" />
                        <?php if (filter_var($shop_url, FILTER_VALIDATE_URL)) : ?>
                            <span class="dashicons dashicons-yes"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-no"></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="shopify_storefront_access_token">Storefront Access Token</label>
                        <p class="description"><a href="https://shopify.dev/docs/storefront-api/getting-started" target="_blank">Erfahren Sie, wie Sie ein Access Token erstellen</a></p>
                    </th>
                    <td>
                        <?php $storefront_access_token = get_option('shopify_storefront_access_token'); ?>
                        <input type="text" id="shopify_storefront_access_token" name="shopify_storefront_access_token" value="<?php echo esc_attr($storefront_access_token); ?>" class="regular-text <?php echo (!empty($storefront_access_token)) ? 'valid-input' : 'invalid-input'; ?>" />
                        <?php if (!empty($storefront_access_token)) : ?>
                            <span class="dashicons dashicons-yes"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-no"></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="shopify_number_of_products">Anzahl der Produkte</label>
                        <p class="description">Wählen Sie die Anzahl der anzuzeigenden Produkte (zwischen 3 und 5).</p>
                    </th>
                    <td>
                        <?php $number_of_products = get_option('shopify_number_of_products', 3); ?>
                        <input type="number" id="shopify_number_of_products" name="shopify_number_of_products" value="<?php echo esc_attr($number_of_products); ?>" min="3" max="5" class="<?php echo ($number_of_products >= 3 && $number_of_products <= 5) ? 'valid-input' : 'invalid-input'; ?>" onchange="updateShortcode()" />
                        <?php if ($number_of_products >= 3 && $number_of_products <= 5) : ?>
                            <span class="dashicons dashicons-yes"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-no"></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="shopify_collection">Produktkollektion</label>
                        <p class="description">Wählen Sie die Kollektion aus, aus der die Produkte stammen sollen, oder wählen Sie "Random" für eine zufällige Auswahl.</p>
                    </th>
                    <td>
                        <?php $collections = wp_shopify_get_collections(); ?>
                        <select id="shopify_collection" name="shopify_collection" class="<?php echo (get_option('shopify_collection') !== 'random' && get_option('shopify_collection')) ? 'valid-input' : 'invalid-input'; ?>" onchange="updateShortcode()">
                            <option value="random" <?php selected(get_option('shopify_collection'), 'random'); ?>>Random</option>
                            <?php if ($collections) : ?>
                                <?php foreach ($collections as $collection) : ?>
                                    <?php $selected = (get_option('shopify_collection') == $collection['node']['handle']) ? 'selected' : ''; ?>
                                    <option value="<?php echo esc_attr($collection['node']['handle']); ?>" <?php echo $selected; ?>><?php echo esc_html($collection['node']['title']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (get_option('shopify_collection') !== 'random' && get_option('shopify_collection')) : ?>
                            <span class="dashicons dashicons-yes"></span>
                        <?php else : ?>
                            <span class="dashicons dashicons-no"></span>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">
                        <label for="shopify_display_option">Anzeigen auf</label>
                        <p class="description">Wählen Sie, wie die Produkte angezeigt werden sollen.</p>
                    </th>
                    <td>
                        <?php $display_option = get_option('shopify_display_option', 'shortcode'); ?>
                        <label>
                            <input type="radio" name="shopify_display_option" value="shortcode" <?php checked($display_option, 'shortcode'); ?> onchange="toggleDisplayOption()" /> Shortcode verwenden
                        </label><br>

                        <?php if ($display_option === 'shortcode') : ?>
                            <div id="shortcode-option">
                                <textarea id="shortcode-preview" readonly="readonly" style="width: 100%; height: 60px; font-size: 16px; padding: 10px;"></textarea>
                                <button id="copy-shortcode-button" class="button button-secondary">Shortcode kopieren</button>
                            </div>
                        <?php endif; ?>
                        <?php if ($display_option === 'post_type') : ?>
                            <div id="post-type-option">
                                <select id="shopify_display_locations" name="shopify_display_locations[]" multiple class="regular-text">
                                    <?php
                                    $post_types = get_post_types(array('public' => true), 'objects');
                                    foreach ($post_types as $post_type) {
                                        $selected = (in_array($post_type->name, get_option('shopify_display_locations', array()))) ? 'selected' : '';
                                        echo '<option value="' . esc_attr($post_type->name) . '" ' . $selected . '>' . esc_html($post_type->labels->singular_name) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="shopify_price_text">Preis / Text</label>
                        <p class="description">Leer lassen wenn Preis angezeigt werden soll</p>
                    </th>
                    <td>
                        <?php $price_text = get_option('shopify_price_text', ''); ?>
                        <label>
                            <input type="text" id="shopify_price_text" name="shopify_price_text" value="<?php echo esc_attr($price_text); ?>" />
                        </label>
                    </td>
                </tr>
            </table>

            <?php submit_button('Einstellungen speichern'); ?>
        </form>
    </div>

    <div class="notice notice-info is-dismissible">
        <p>Unterstützen Sie die Weiterentwicklung dieses Plugins mit einer Spende.</p>
        <p><a href="https://www.paypal.me/choooomedia/3" class="button button-primary">Jetzt spenden</a></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    function toggleDisplayOption() {
        var displayOption = document.querySelector('input[name="shopify_display_option"]:checked');
        
        var shortcodeOption = document.getElementById('shortcode-option');
        var postTypeOption = document.getElementById('post-type-option');

        if (displayOption && displayOption.value === 'shortcode') {
            if (shortcodeOption) {
                shortcodeOption.style.display = 'block';
            }
            if (postTypeOption) {
                postTypeOption.style.display = 'none';
            }
            updateShortcode(); // Update des Shortcodes bei Änderung
        } else if (displayOption && displayOption.value === 'post_type') {
            if (shortcodeOption) {
                shortcodeOption.style.display = 'none';
            }
            if (postTypeOption) {
                postTypeOption.style.display = 'block';
            }
            updateSettings(); // Update der Einstellungen bei Änderung
        }
    }

    function updateShortcode() {
        var number_of_products = document.getElementById('shopify_number_of_products').value;
        var products_collection = document.getElementById('shopify_collection').value;
        var shortcode = '[shopify_ads number_of_ads="' + number_of_products + '" products_collection="' + products_collection + '"]';
        document.getElementById('shortcode-preview').textContent = shortcode;
    }

    function copyShortcode(event) {
        if (event) {
            event.preventDefault();
        }

        var shortcode = document.getElementById('shortcode-preview').textContent;
        navigator.clipboard.writeText(shortcode)
            .then(function() {
                var copiedMessage = document.createElement('div');
                copiedMessage.className = 'notice notice-success is-dismissible';
                copiedMessage.innerHTML = '<p>Shortcode wurde in die Zwischenablage kopiert: <strong>' + shortcode + '</strong></p>';
                document.getElementById('wpbody-content').appendChild(copiedMessage);
                setTimeout(function() {
                    copiedMessage.remove();
                }, 5000);
            })
            .catch(function(err) {
                console.error('Fehler beim Kopieren des Shortcodes: ', err);
            });
    }

    function updateSettings() {
        var selectedPostTypes = [];
        var radioButtons = document.querySelectorAll('input[name="shopify_display_locations"]:checked');
        radioButtons.forEach(function(radio) {
            selectedPostTypes.push(radio.value);
        });

        var displayOption = 'post_type';
        var shortcode = '[shopify_ads_display_option="' + displayOption + '" display_locations="' + selectedPostTypes.join(',') + '"]';
        document.getElementById('shortcode-preview').textContent = shortcode;
    }

    // Event Listener für Änderungen in den Einstellungen
    var number_of_products_input = document.getElementById('shopify_number_of_products');
    var collection_select = document.getElementById('shopify_collection');
    var display_option_radios = document.querySelectorAll('input[name="shopify_display_option"]');
    var post_type_radios = document.querySelectorAll('input[name="shopify_display_locations"]');
    
    if (number_of_products_input) {
        number_of_products_input.addEventListener('change', updateShortcode);
    }
    
    if (collection_select) {
        collection_select.addEventListener('change', updateShortcode);
    }
    
    if (display_option_radios) {
        display_option_radios.forEach(function(radio) {
            radio.addEventListener('change', toggleDisplayOption);
        });
    }

    if (post_type_radios) {
        post_type_radios.forEach(function(radio) {
            radio.addEventListener('change', updateSettings);
        });
    }

    // Event Listener für Kopieren des Shortcodes
    var copy_shortcode_button = document.getElementById('copy-shortcode-button');
    if (copy_shortcode_button) {
        copy_shortcode_button.addEventListener('click', copyShortcode);
    }

    toggleDisplayOption(); // Anfangs die richtige Anzeigeoption anzeigen
});
    </script>

    <?php
}

// Funktion zum Abrufen der Kollektionen von Shopify
function wp_shopify_get_collections() {
    $shop_url = get_option('shopify_shop_url', '');
    $storefront_access_token = get_option('shopify_storefront_access_token', '');

    if (empty($shop_url) || empty($storefront_access_token)) {
        return false;
    }

    // GraphQL Query für die Kollektionen
    $query = <<<GQL
    {
      collections(first: 250) {
        edges {
          node {
            id
            handle
            title
            products(first: 1) {
              edges {
                node {
                  id
                }
              }
            }
          }
        }
      }
    }
    GQL;

    $response = wp_remote_post("$shop_url/api/2023-07/graphql.json", [
        'headers' => [
            'Content-Type' => 'application/graphql',
            'X-Shopify-Storefront-Access-Token' => $storefront_access_token,
        ],
        'body' => $query,
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['errors']) || !isset($body['data']['collections'])) {
        return false;
    }

    $collections = $body['data']['collections']['edges'];

    // Überprüfen, ob "Random" ausgewählt ist
    $selected_collection = get_option('shopify_collection');
    if ($selected_collection === 'random') {
        $collections_with_products = array_filter($collections, function($collection) {
            return !empty($collection['node']['products']['edges']);
        });

        if (empty($collections_with_products)) {
            // Wenn keine Kollektion Produkte hat, wähle die erste Kollektion mit Produkten
            $collections_with_products = array_filter($collections, function($collection) {
                return !empty($collection['node']['products']['edges']);
            });
        }

        if (empty($collections_with_products)) {
            return false; // Keine Kollektionen mit Produkten gefunden
        }

        // Zufällige Auswahl einer Kollektion mit Produkten
        $random_index = array_rand($collections_with_products);
        return [$collections_with_products[$random_index]];
    }

    // Überprüfen, ob die ausgewählte Kollektion Produkte hat
    $selected_collection_with_products = array_filter($collections, function($collection) use ($selected_collection) {
        return $collection['node']['handle'] === $selected_collection && !empty($collection['node']['products']['edges']);
    });

    if (empty($selected_collection_with_products)) {
        // Falls die ausgewählte Kollektion keine Produkte hat, wähle die erste Kollektion mit Produkten
        $selected_collection_with_products = array_filter($collections, function($collection) {
            return !empty($collection['node']['products']['edges']);
        });
    }

    if (empty($selected_collection_with_products)) {
        return false; // Keine Kollektionen mit Produkten gefunden
    }

    return $selected_collection_with_products;
}