<?php
// Holen der Optionen aus der Datenbank
$shop_url = get_option('shopify_shop_url');
$storefront_access_token = get_option('shopify_storefront_access_token');
$price_text = get_option('shopify_price_text');

if (empty($shop_url) || empty($storefront_access_token)) {
    echo 'Shopify shop URL or access token is not set.';
    return;
}

$number_of_ads = intval($atts['number_of_ads']);
$products_collection = get_option('shopify_collection');
if ($products_collection === 'random') {
    $collections = wp_shopify_get_collections();
    if (!$collections) {
        echo 'No collections found.';
        return;
    }
    $products_collection = $collections[0]['node']['handle'];
}

// GraphQL Query für die Produkte
$query = <<<GQL
{
  collectionByHandle(handle: "$products_collection") {
    products(first: $number_of_ads) {
      edges {
        node {
          id
          title
          handle
          images(first: 1) {
            edges {
              node {
                src
                altText
              }
            }
          }
          priceRange {
            minVariantPrice {
              amount
              currencyCode
            }
          }
        }
      }
    }
  }
}
GQL;

// GraphQL-Anfrage senden
$response = wp_remote_post("$shop_url/api/2023-07/graphql.json", [
    'headers' => [
        'Content-Type' => 'application/json',
        'X-Shopify-Storefront-Access-Token' => $storefront_access_token,
    ],
    'body' => json_encode(['query' => $query]),
]);

if (is_wp_error($response)) {
    echo 'Error fetching products: ' . $response->get_error_message();
    return;
}

// JSON-Antwort verarbeiten
$body = json_decode(wp_remote_retrieve_body($response), true);

// Fehlerüberprüfung in der GraphQL-Antwort
if (isset($body['errors'])) {
    echo 'Error in GraphQL response: ' . json_encode($body['errors']);
    return;
}

if (!isset($body['data']['collectionByHandle'])) {
    echo 'Collection handle not found or no products in collection. Debug Info: ' . json_encode($body);
    return;
}

if (empty($body['data']['collectionByHandle']['products']['edges'])) {
    echo 'No products found in the collection.';
    return;
}

$products = $body['data']['collectionByHandle']['products']['edges'];

?>
<div class="products">
    <?php foreach ($products as $product) :
        $product = $product['node'];
        $image = $product['images']['edges'][0]['node'];
        $product_url = "$shop_url/products/{$product['handle']}?tracking=webshop";
    ?>
    
    <a class="shopify-product-link" href="<?= esc_url($product_url); ?>" target="_blank">
        <h3 class="shopify-product-title" itemprop="name"><?= esc_html($product['title']); ?></h3>
        <img class="shopify-product-image" src="<?= esc_url($image['src']); ?>" alt="<?= esc_attr($image['altText']); ?>" itemprop="image">
        <div class="shopify-product-details">
            <?php if (!empty($price_text)) : ?>
                <span class="shopify-product-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <?= esc_html($price_text); ?>
                </span>
            <?php else : ?>
                <span class="shopify-product-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <span itemprop="priceCurrency" content="<?= esc_attr($product['priceRange']['minVariantPrice']['currencyCode']); ?>"></span>
                    <span itemprop="price" content="<?= esc_attr($product['priceRange']['minVariantPrice']['amount']); ?>">
                        <?= esc_html($product['priceRange']['minVariantPrice']['amount']); ?> <?= esc_html($product['priceRange']['minVariantPrice']['currencyCode']); ?>
                    </span>
                </span>
            <?php endif; ?>
        </div>
    </a>
    <?php endforeach; ?>
</div>
