document.addEventListener('DOMContentLoaded', function() {
    // Add click event to each share link
    document.querySelectorAll('.wdk-block-share').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault(); 
            wdkBlockShareUrl(link.getAttribute('href')); 
        });
    });
});


// Share URL function
var wdkBlockShareUrl = (url = window.location.href) => {
    // Check if the browser supports the Web Share API
    if (!navigator.share) {
        console.log("Web Share API not supported.");
        return;
    }

    navigator.share({
        url: url, // Share the current page URL
    })
    .then(() => {
        console.log("Shared successfully!");
    })
    .catch((error) => {
        console.error("Sharing failed:", error);
    });
};

