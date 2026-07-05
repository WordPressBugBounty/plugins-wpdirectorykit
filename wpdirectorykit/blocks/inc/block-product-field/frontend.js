document.addEventListener('DOMContentLoaded', function() {
    const blockContainers = document.querySelectorAll('.wdk-latest-listing-block');

    blockContainers.forEach(container => {
        const postCount = container.getAttribute('data-post-count') || 5;
        const alignment = container.getAttribute('data-alignment') || 'none';
        const content = container.getAttribute('data-content') || '';

        const nonce = (typeof wdkBlockLastListings !== 'undefined' && wdkBlockLastListings.nonce) ? wdkBlockLastListings.nonce : '';
        wp.apiFetch({
            path: `/wdk-blocks/v1/last-listings/?postCount=${encodeURIComponent(postCount)}&_wpnonce=${encodeURIComponent(nonce)}`
        })
        .then(posts => {
            container.innerHTML = posts.data;
        })
        .catch(error => {
            console.error(error);
            container.innerHTML = '<p>Failed to load listings.</p>';
        });

    });
});
