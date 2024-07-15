document.addEventListener('DOMContentLoaded', function() {
    let adsContainer = document.querySelector('.products');
    let paragraphs = document.querySelectorAll('.et_pb_post_content > p');

    if (adsContainer && paragraphs.length > 0) {
        let ads = adsContainer.children;
        let randomIndexes = getRandomIndexes(paragraphs.length, Math.min(ads.length, paragraphs.length));

        randomIndexes.sort(function(a, b) {
            return a - b;
        });

        randomIndexes.forEach(function(index, i) {
            if (i < ads.length) {
                let adContainer = ads[i].cloneNode(true);
                adContainer.classList.add('shopify-product-ad');

                let adWrapper = document.createElement('aside');
                adWrapper.appendChild(adContainer);

                // ARIA-Label für Accessibility hinzufügen
                adWrapper.setAttribute('role', 'complementary');
                adWrapper.setAttribute('aria-label', 'Advertisement');

                // Klasse basierend auf Index hinzufügen
                if (i % 2 === 0) {
                    adWrapper.classList.add('ad-left');
                } else {
                    adWrapper.classList.add('ad-right');
                }

                if (paragraphs[index] && paragraphs[index].childNodes.length === 1 && paragraphs[index].childNodes[0].nodeType === Node.TEXT_NODE) {
                    paragraphs[index].insertBefore(adWrapper, paragraphs[index].childNodes[0]);
                } else if (paragraphs[index]) {
                    paragraphs[index].appendChild(adWrapper);
                }
            }
        });

        // Entferne den ursprünglichen versteckten Container nach dem Einfügen der Anzeigen
        adsContainer.remove();
    } else {
        console.error('Ads container or paragraphs not found.');
    }
});

function getRandomIndexes(maxLength, count) {
    let randomIndexes = [];
    while (randomIndexes.length < count) {
        let randomIndex = Math.floor(Math.random() * maxLength);
        if (!randomIndexes.includes(randomIndex)) {
            randomIndexes.push(randomIndex);
        }
    }
    return randomIndexes;
}