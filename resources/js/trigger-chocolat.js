(() => {
    const { api } = Chocolat(document.querySelectorAll('.chocolat-image'), {
        imageSourceAttribute: 'data-image-source'
    });
})()