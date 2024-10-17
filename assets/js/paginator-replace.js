var page = document.getElementById("pageInput");

function replaceValuePaginator(valeur) {
    page.value = valeur;

    var event = new Event('change', {
        'bubbles': true,
        'cancelable': true
    });

    page.dispatchEvent(event);
}

page.addEventListener('input', function() {
console.log("La valeur de l'input a été modifiée à : " + page.value);
});
