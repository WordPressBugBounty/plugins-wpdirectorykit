document.addEventListener('DOMContentLoaded', function() {
    const blockContainers = document.querySelectorAll('.wdk-latest-listing-block');

    blockContainers.forEach(container => {
        const postCount = container.getAttribute('data-post-count') || 5;
        const alignment = container.getAttribute('data-alignment') || 'none';
        const content = container.getAttribute('data-content') || '';

        wp.apiFetch({ path: `/wdk-blocks/v1/last-listings/&postCount=${postCount}` }).then(posts => {
            container.innerHTML =(posts.data);
        });

    });
});
